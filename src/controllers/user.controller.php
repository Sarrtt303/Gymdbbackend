<?php
include_once __DIR__ . '/../utils/apiResponse.php';
include_once __DIR__ . "/../utils/apiError.php";
include_once __DIR__ . "/../models/user.model.php";
include_once __DIR__ . "/../models/membership.model.php";

class User
{
    private $userSchema;
    private $membershipSchema;

    public function __construct($db)
    {
        $this->userSchema = new UserSchema($db);
        $this->membershipSchema = new MembershipSchema($db);
        $this->initializeDatabase();
    }

    // It will create all the necessary tables before processing the request
    private function initializeDatabase()
    {
        $this->membershipSchema->createMembershipsTable();
        $this->userSchema->createUsersTable();
    }

    public function registerUser()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!empty($data['name']) && !empty($data['username']) && !empty($data['email']) && !empty($data['password']) && !empty($data['phone']) && !empty($data['gender']) && !empty($data['dob']) && !empty($data['address'])) {
                $createdUser = $this->userSchema->create($data);
                if ($createdUser) {
                    new ApiResponse(201, "User registered successfully", $createdUser); // 201 Created
                } else {
                    throw new Exception("User registration failed", 500); // 500 Internal Server Error
                }
            } else {
                throw new Exception("Incomplete data provided", 422); // 422 Unprocessable Entity
            }
        } catch (PDOException $e) {
            new ApiError(503, "Database error: " . $e->getMessage(),); // 503 Service Unavailable
        } catch (Exception $e) {
            new ApiError($e->getCode() ?: 500, $e->getMessage());
        }
    }


    public function login()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            if ((!empty($data["email"]) && !empty($data["password"])) || (!empty($data["username"]) && !empty($data["password"]))) {
                //finds the user in db through either email or username(according to the user request)
                $user = $this->userSchema->read($data["email"] ? "email" : "username", $data["email"] ?: $data["username"]);

                if ($user) {
                    $isPasswordValid = $this->userSchema->isPasswordCorrect($data["password"], $user["id"]);

                    // Add logging to debug password verification
                    error_log("User ID: " . $user["id"]);
                    error_log("Entered Password: " . $data["password"]);
                    error_log("Hashed Password: " . $user["password"]); // Ensure this is fetched correctly

                    if ($isPasswordValid) {
                        $accessToken = $this->userSchema->generateJwt($user, $_ENV["ACCESS_TOKEN_EXPIRY"], $_ENV["ACCESS_TOKEN_SECRET"]);
                        $expiryTime = time() + intval($_ENV["ACCESS_TOKEN_EXPIRY"]);
                        $options = [
                            "expires" => $expiryTime,
                            "path" => "/",
                            "domain" => "",
                            "secure" => true,
                            "httponly" => true,
                            "samesite" => "None"
                        ];
                        setcookie("access_token", $accessToken, $options);

                        //removes sensitive information from the fetched data before sending it to user
                        $loggedInUser = $this->userSchema->getUserByID($user["id"]);
                        $fieldsToRemove = ["password", "access_token"];

                        foreach ($fieldsToRemove as $field) {
                            if (isset($loggedInUser[$field])) {
                                unset($loggedInUser[$field]);
                            }
                        }

                        new ApiResponse(200, "User logged in successfully", $loggedInUser);
                    } else {
                        throw new Exception("Incorrect password provided", 401);
                    }
                } else {
                    throw new Exception("User not found with provided email", 404);
                }
            } else {
                throw new Exception("Incomplete data provided", 422); // Use 422 for unprocessable entity
            }
        } catch (PDOException $e) {
            new ApiError(500, "Database error: " . $e->getMessage());
        } catch (Exception $e) {
            new ApiError($e->getCode() ?: 500, "Error: " . $e->getMessage());
        }
    }


    public function logout($currentUser)
    {
        try {
            $result = $this->userSchema->updateField($currentUser["id"], "access_token", null);
            if ($result) {
                $expiryTime = time() - 10000;
                $options = [
                    "expires" => $expiryTime,
                    "path" => "/",
                    "domain" => "",
                    "secure" => true,
                    "httponly" => true,
                    "samesite" => "None"
                ];
                setcookie("access_token", "", $options);
                new ApiResponse(201, "user logged out successfully", true);
            } else {
                throw new Exception("there was an error logging out user", 401);
            }
        } catch (PDOException $th) {
            new ApiError(500, "Database error: " . $th->getMessage());
        } catch (Exception $e) {
            new ApiError($e->getCode() ?: 500, "Error: " . $e->getMessage());
        }
    }


    public function getCurrentUser($currentUser)
    {
        try {
            if ($currentUser) {
                new ApiResponse(200, "User details fetched successfully", $currentUser); // 200 OK
            } else {
                throw new Exception("No logged in user found", 404); // 404 Not Found
            }
        } catch (PDOException $th) {
            new ApiError(500, "Database error: " . $th->getMessage());
        } catch (Exception $e) {
            new ApiError($e->getCode() ?: 500, "Error: " . $e->getMessage());
        }
    }

    public function getUserByID($currentUser)
    {
        try {
            if (isset($_GET["id"])) {
                $id = $_GET["id"];
                $user = $this->userSchema->read("id", $id);
                if ($user) {
                    $fieldsToRemove = ["password", "access_token"];

                    foreach ($fieldsToRemove as $field) {
                        if (isset($user[$field])) {
                            unset($user[$field]);
                        }
                    }
                    new ApiResponse(200, "User fetched successfully", $user); // 200 OK
                } else {
                    throw new Exception("User not found", 404); // 404 Not Found
                }
            } else {
                throw new Exception("User ID is required", 400); // 400 Bad Request
            }
        } catch (PDOException $e) {
            new ApiError(500, "Database error: " . $e->getMessage());
        } catch (Exception $e) {
            new ApiError($e->getCode() ?: 500, "Error: " . $e->getMessage());
        }
    }

    public function updateUserDetails($currentUser)
    {
        try {
            if (isset($_GET["id"])) {
                $id = $_GET["id"];
                $data = json_decode(file_get_contents("php://input"), true);
                if ($data) {
                    $updatedUser = $this->userSchema->updateDetails($id, $data);
                    if ($updatedUser) {
                        $fieldsToRemove = ["password", "access_token"];

                        foreach ($fieldsToRemove as $field) {
                            if (isset($updatedUser[$field])) {
                                unset($updatedUser[$field]);
                            }
                        }
                        new ApiResponse(200, "User details updated successfully", $updatedUser); // 200 OK
                    } else {
                        throw new Exception("User not found", 404); // 404 Not Found
                    }
                } else {
                    throw new Exception("User data is required", 422); // 422 Unprocessable Entity
                }
            } else {
                throw new Exception("User ID is required", 400); // 400 Bad Request
            }
        } catch (PDOException $e) {
            new ApiError(500, "Database error: " . $e->getMessage());
        } catch (Exception $e) {
            new ApiError($e->getCode() ?: 500, "Error: " . $e->getMessage());
        }
    }

    public function deleteUser($currentUser)
    {
        try {
            if (isset($_GET["id"])) {
                $id = $_GET["id"];
                $user = $this->userSchema->delete($id);
                if ($user) {
                    new ApiResponse(200, "User deleted successfully", $user); // 200 OK
                } else {
                    throw new Exception("User not found", 404); // 404 Not Found
                }
            } else {
                throw new Exception("User ID is required", 400); // 400 Bad Request
            }
        } catch (PDOException $e) {
            new ApiError(500, "Database error: " . $e->getMessage());
        } catch (Exception $e) {
            new ApiError($e->getCode() ?: 500, "Error: " . $e->getMessage());
        }
    }

    public function updateUserPassword($currentUser)
    {
        try {
            if (isset($_GET["id"])) {
                $id = $_GET["id"];
                $data = json_decode(file_get_contents("php://input"), true);
                if ($data) {
                    $result = $this->userSchema->updatePassword($id, $data);
                    if ($result) {
                        new ApiResponse(200, "User password updated successfully", $result); // 200 OK
                    } else {
                        throw new Exception("User not found", 404); // 404 Not Found
                    }
                } else {
                    throw new Exception("User password is missing", 422); // 422 Unprocessable Entity
                }
            } else {
                throw new Exception("User ID is required", 400); // 400 Bad Request
            }
        } catch (PDOException $e) {
            new ApiError(500, "Database error: " . $e->getMessage());
        } catch (Exception $e) {
            new ApiError($e->getCode() ?: 500, "Error: " . $e->getMessage());
        }
    }

    public function updateUserRole($currentUser)
    {
        try {
            if (isset($_GET["id"])) {
                $id = $_GET["id"];
                $data = json_decode(file_get_contents("php://input"), true);
                if ($data) {
                    $updatedUser = $this->userSchema->updateRole($id, $data);
                    if ($updatedUser) {
                        $fieldsToRemove = ["password", "access_token"];

                        foreach ($fieldsToRemove as $field) {
                            if (isset($updatedUser[$field])) {
                                unset($updatedUser[$field]);
                            }
                        }
                        new ApiResponse(200, "User role updated successfully", $updatedUser); // 200 OK
                    } else {
                        throw new Exception("User not found", 404); // 404 Not Found
                    }
                } else {
                    throw new Exception("User data is required", 422); // 422 Unprocessable Entity
                }
            } else {
                throw new Exception("User ID is required", 400); // 400 Bad Request
            }
        } catch (PDOException $e) {
            new ApiError(500, "Database error: " . $e->getMessage());
        } catch (Exception $e) {
            new ApiError($e->getCode() ?: 500, "Error: " . $e->getMessage());
        }
    }

    public function addUserMembership($currentUser)
    {
        try {
            if (isset($_GET["id"]) && isset($_GET["mid"])) {
                $id = $_GET["id"];
                $membership_id = $_GET["mid"];
                $updatedUser = $this->userSchema->addMembership($id, $membership_id);
                if ($updatedUser) {
                    $fieldsToRemove = ["password", "access_token"];

                    foreach ($fieldsToRemove as $field) {
                        if (isset($updatedUser[$field])) {
                            unset($updatedUser[$field]);
                        }
                    }
                    new ApiResponse(200, "Membership added successfully", $updatedUser); // 200 OK
                } else {
                    throw new Exception("User or membership not found", 404); // 404 Not Found
                }
            } else {
                throw new Exception("User ID and membership ID are required", 400); // 400 Bad Request
            }
        } catch (PDOException $e) {
            new ApiError(500, "Database error: " . $e->getMessage());
        } catch (Exception $e) {
            new ApiError($e->getCode() ?: 500, "Error: " . $e->getMessage());
        }
    }
}

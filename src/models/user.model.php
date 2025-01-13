<?php
include_once __DIR__ . "/../utils/apiError.php";

use \Firebase\JWT\JWT;

class UserSchema
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createUsersTable()
    {
        try {
            $query = "SHOW TABLES LIKE 'users'";
            $stmt = $this->db->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $query = "
                CREATE TABLE IF NOT EXISTS users (
                    id int AUTO_INCREMENT NOT NULL,
                    name VARCHAR(100) NOT NULL,
                    username VARCHAR(50) NOT NULL UNIQUE,
                    email VARCHAR(100) NOT NULL UNIQUE,
                    password VARCHAR(512) NOT NULL,
                    role ENUM('member', 'trainer', 'admin') NOT NULL DEFAULT 'member',
                    phone VARCHAR(20) NOT NULL,
                    gender ENUM('male', 'female', 'others') NOT NULL,
                    dob DATE NOT NULL,
                    address VARCHAR(100) NOT NULL,
                    membership_id int, 
                    membership_start_date DATE,
                    membership_end_date DATE,
                    access_token VARCHAR(512),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    FOREIGN KEY (membership_id) REFERENCES memberships(id) ON DELETE CASCADE
                )";

                if ($this->db->exec($query)) {
                    echo "Users table created successfully";
                }
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    private function encryptPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function isPasswordCorrect($password, $id)
    {
        $result = $this->getUserByID($id);
        if ($result && isset($result["password"])) {
            $hashedPassword = $result["password"];
            return password_verify($password, $hashedPassword);
        }
        return false;
    }

    public function generateJwt($data, $expiry, $secret)
    {
        $payload = [
            "iat" => time(),
            "exp" => time() + $expiry,
            "data" => [
                "id" => $data["id"],
                "email" => $data["email"]
            ]
        ];

        $jwt = JWT::encode($payload, $secret, "HS256");

        try {
            $query = "UPDATE users SET access_token = :jwt WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":jwt", $jwt);
            $stmt->bindParam(":id", $data["id"]);
            $stmt->execute();
            return $jwt;
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function create($data)
    {
        ['name' => $name, 'username' => $username, 'email' => $email, 'password' => $password, 'phone' => $phone, 'gender' => $gender, 'dob' => $dob, 'address' => $address] = $data;
        try {
            $query = "INSERT INTO users (name, username, email, password, phone, gender, dob, address)
                  VALUES (:name, :username, :email, :password, :phone, :gender, :dob, :address)";
            $stmt = $this->db->prepare($query);
            $password = $this->encryptPassword($password);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":phone", $phone);
            $stmt->bindParam(":gender", $gender);
            $stmt->bindParam(":dob", $dob);
            $stmt->bindParam(":address", $address);
            return $stmt->execute();
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }


    public function getUserByID($id)
    {
        try {
            $query = "SELECT * FROM users WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function read($param, $value)
    {
        try {
            $query = "SELECT * FROM users WHERE $param = :value";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":value", $value);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function updateField($id, $field, $value)
    {
        try {
            $query = "UPDATE users SET $field = :value WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":value", $value);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch (PDOException $th) {
            throw new ApiError(500, "Database error: " . $th->getMessage());
        } catch (Exception $e) {
            throw new ApiError(500, "Error: " . $e->getMessage());
        }
    }

    public function updateDetails($id, $data)
    {
        try {
            $query = "UPDATE users SET name = :name, email = :email, phone = :phone WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":name", $data['name']);
            $stmt->bindParam(":email", $data['email']);
            $stmt->bindParam(":phone", $data['phone']);
            $stmt->execute();
            return $this->getUserByID($id);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function delete($id)
    {
        try {
            $query = "DELETE FROM users WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function updatePassword($id, $data)
    {
        try {
            $query = "UPDATE users SET password = :password WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":password", $data['password']);
            return $stmt->execute();
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function updateRole($id, $data)
    {
        try {
            $query = "UPDATE users SET role = :role WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":role", $data['role']);
            $stmt->execute();
            return $this->getUserByID($id);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function addMembership($id, $membership_id)
    {
        try {
            $query1 = "SELECT duration FROM memberships WHERE id = :mid";
            $stmt = $this->db->prepare($query1);
            $stmt->bindParam(":mid", $membership_id);
            $stmt->execute();
            $membership = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($membership === false) {
                throw new Exception("Membership ID not found", 404); // 404 Not Found
            }

            $membership_start = new DateTime("now");
            $membership_end = new DateTime("now");

            if ($membership["duration"] === "Monthly") {
                $membership_end->add(new DateInterval("P30D"));
            } elseif ($membership["duration"] === "half-yearly") {
                $membership_end->add(new DateInterval("P180D"));
            } elseif ($membership["duration"] === "yearly") {
                $membership_end->add(new DateInterval("P360D"));
            } else {
                throw new Exception("Invalid membership duration", 422); // 422 Unprocessable Entity
            }

            // Store formatted dates in variables
            $formatted_start = $membership_start->format("Y-m-d");
            $formatted_end = $membership_end->format("Y-m-d");

            $query2 = "UPDATE users SET membership_id = :mid, membership_start_date = :start, membership_end_date = :end WHERE id = :id";
            $stmt = $this->db->prepare($query2);
            $stmt->bindParam(":mid", $membership_id);
            $stmt->bindParam(":start", $formatted_start);
            $stmt->bindParam(":end", $formatted_end);
            $stmt->bindParam(":id", $id);

            $stmt->execute();

            return $this->getUserByID($id);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500); // 500 Internal Server Error
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 400); // 400 Bad Request
        }
    }
}

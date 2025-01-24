<?php

include_once __DIR__ . "/../utils/apiError.php";

class MealsSelectedSchema
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createMealsSelectedTable()
    {
        try {
            $query = "SHOW TABLES LIKE 'meals_selected'";
            $stmt = $this->db->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $query = "
                CREATE TABLE IF NOT EXISTS meals_selected (
                    id int AUTO_INCREMENT NOT NULL,
                    meal_id int NOT NULL,
                    user_id int NOT NULL,
                    PRIMARY KEY(id),
                    FOREIGN KEY (meal_id) REFERENCES meals(id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )";

                if ($this->db->exec($query)) {
                    echo "meals_selected table created successfully";
                }
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    // Create a new meal selection
    public function create($data)
    {
        try {
            if ($data) {
                $query = "INSERT INTO meals_selected (meal_id, user_id) VALUES (:meal_id, :user_id)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":meal_id", $data["meal_id"]);
                $stmt->bindParam(":user_id", $data["user_id"]);

                return $stmt->execute();
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    // Read a meal selection by ID
    public function read($id)
    {
        try {
            if ($id) {
                $query = "SELECT * FROM meals_selected WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id", $id);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result;
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    // Read all meal selections
    public function readAll()
    {
        try {
            $query = "SELECT * FROM meals_selected";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    // Update a meal selection by ID
    public function update($id, $data)
    {
        try {
            if ($id && $data) {
                $query = "UPDATE meals_selected SET meal_id = :meal_id WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":meal_id", $data["meal_id"]);
                $stmt->bindParam(":id", $id);

                return $stmt->execute();
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    // Delete a meal selection by ID
    public function delete($id)
    {
        try {
            if ($id) {
                $query = "DELETE FROM meals_selected WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id", $id);
                return $stmt->execute();
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }
}

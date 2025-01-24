<?php

include_once __DIR__ . "/../utils/apiError.php";

class MealsSchema
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createMealsTable()
    {
        try {
            $query = "SHOW TABLES LIKE 'meals'";
            $stmt = $this->db->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $query = "
                CREATE TABLE IF NOT EXISTS meals (
                    id int AUTO_INCREMENT NOT NULL,
                    breakfast VARCHAR(100) NOT NULL,
                    lunch VARCHAR(100) NOT NULL,
                    supper VARCHAR(100) NOT NULL,
                    dinner VARCHAR(100) NOT NULL,
                    other VARCHAR(200),
                    trainer_id int NOT NULL,
                    PRIMARY KEY(id),
                    FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE CASCADE
                )";

                if ($this->db->exec($query)) {
                    echo "meals table created successfully";
                }
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function createMeal($data)
    {
        ['breakfast' => $breakfast, 'lunch' => $lunch, 'supper' => $supper, 'dinner' => $dinner, 'other' => $other, 'trainer_id' => $trainer_id] = $data;

        try {
            $query = "INSERT INTO meals (breakfast, lunch, supper, dinner, other, trainer_id)
                      VALUES (:breakfast, :lunch, :supper, :dinner, :other, :trainer_id)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":breakfast", $breakfast);
            $stmt->bindParam(":lunch", $lunch);
            $stmt->bindParam(":supper", $supper);
            $stmt->bindParam(":dinner", $dinner);
            $stmt->bindParam(":other", $other);
            $stmt->bindParam(":trainer_id", $trainer_id);

            $stmt->execute();
            return $this->getMealById($this->db->lastInsertId());
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getMealById($id)
    {
        try {
            $query = "SELECT * FROM meals WHERE id = :id";
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

    public function updateMeal($id, $data)
    {
        try {
            $query = "UPDATE meals SET 
                      breakfast = :breakfast,
                      lunch = :lunch,
                      supper = :supper,
                      dinner = :dinner,
                      other = :other
                      WHERE id = :id";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":breakfast", $data['breakfast']);
            $stmt->bindParam(":lunch", $data['lunch']);
            $stmt->bindParam(":supper", $data['supper']);
            $stmt->bindParam(":dinner", $data['dinner']);
            $stmt->bindParam(":other", $data['other']);
            $stmt->bindParam(":id", $id);

            $stmt->execute();
            return $this->getMealById($id);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function deleteMeal($id)
    {
        try {
            $query = "DELETE FROM meals WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return true;
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getAllMeals()
    {
        try {
            $query = "SELECT * FROM meals";
            $stmt = $this->db->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }
}

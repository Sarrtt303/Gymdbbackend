<?php

include_once __DIR__ . "/../utils/apiError.php";

class GoalsSchema
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createGoalsTable()
    {
        try {
            $query = "SHOW TABLES LIKE 'goals'";
            $stmt = $this->db->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $query = "
                CREATE TABLE IF NOT EXISTS goals (
                    id int AUTO_INCREMENT NOT NULL,
                    desired_weight int NOT NULL,
                    desired_lean_mass_percent int NOT NULL,
                    desired_fat_percent int NOT NULL,
                    user_id int NOT NULL,
                    PRIMARY KEY(id),
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )";

                if ($this->db->exec($query)) {
                    echo "goals table created successfully";
                }
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function createGoal($data)
    {
        ['desired_weight' => $desired_weight, 'desired_lean_mass_percent' => $desired_lean_mass_percent, 'desired_fat_percent' => $desired_fat_percent, 'user_id' => $user_id] = $data;

        try {
            $query = "INSERT INTO goals (desired_weight, desired_lean_mass_percent, desired_fat_percent, user_id)
                      VALUES (:desired_weight, :desired_lean_mass_percent, :desired_fat_percent, :user_id)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":desired_weight", $desired_weight);
            $stmt->bindParam(":desired_lean_mass_percent", $desired_lean_mass_percent);
            $stmt->bindParam(":desired_fat_percent", $desired_fat_percent);
            $stmt->bindParam(":user_id", $user_id);

            $stmt->execute();
            return $this->getGoalById($this->db->lastInsertId());
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getGoalById($id)
    {
        try {
            $query = "SELECT * FROM goals WHERE id = :id";
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

    public function updateGoal($id, $data)
    {
        try {
            $query = "UPDATE goals SET 
                      desired_weight = :desired_weight,
                      desired_lean_mass_percent = :desired_lean_mass_percent,
                      desired_fat_percent = :desired_fat_percent
                      WHERE id = :id";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":desired_weight", $data['desired_weight']);
            $stmt->bindParam(":desired_lean_mass_percent", $data['desired_lean_mass_percent']);
            $stmt->bindParam(":desired_fat_percent", $data['desired_fat_percent']);
            $stmt->bindParam(":id", $id);

            $stmt->execute();
            return $this->getGoalById($id);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function deleteGoal($id)
    {
        try {
            $query = "DELETE FROM goals WHERE id = :id";
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

    public function getAllGoals()
    {
        try {
            $query = "SELECT * FROM goals";
            $stmt = $this->db->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }
}

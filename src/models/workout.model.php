<?php

include_once __DIR__ . "/../utils/apiError.php";

class workoutSchema
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createWorkoutsTable()
    {
        try {
            $query = "SHOW TABLES LIKE 'workouts'";
            $stmt = $this->db->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $query = "
                CREATE TABLE IF NOT EXISTS workouts(
                    id int AUTO_INCREMENT NOT NULL,
                    name VARCHAR(50) NOT NULL,
                    mon VARCHAR(100),
                    tue VARCHAR(100),
                    wed VARCHAR(100),
                    thu VARCHAR(100),
                    fri VARCHAR(100),
                    sat VARCHAR(100),
                    sun VARCHAR(100),
                    trainer_id int,
                    PRIMARY KEY(id),
                    FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE CASCADE
                )";

                if ($this->db->exec($query)) {
                    echo "workouts table created successfully";
                }
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function createWorkout($name, $mon, $tue, $wed, $thu, $fri, $sat, $sun, $trainer_id)
    {
        try {
            $query = "
                INSERT INTO workouts (name, mon, tue, wed, thu, fri, sat, sun, trainer_id)
                VALUES (:name, :mon, :tue, :wed, :thu, :fri, :sat, :sun, :trainer_id)";
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':mon', $mon);
            $stmt->bindParam(':tue', $tue);
            $stmt->bindParam(':wed', $wed);
            $stmt->bindParam(':thu', $thu);
            $stmt->bindParam(':fri', $fri);
            $stmt->bindParam(':sat', $sat);
            $stmt->bindParam(':sun', $sun);
            $stmt->bindParam(':trainer_id', $trainer_id);

            $stmt->execute();

            return $this->getWorkoutById($this->db->lastInsertId());
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getWorkoutById($id)
    {
        try {
            $query = "SELECT * FROM workouts WHERE id = :id";
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

    public function getAllWorkouts()
    {
        try {
            $query = "SELECT * FROM workouts";
            $stmt = $this->db->query($query);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getWorkoutsByTrainerId($trainer_id)
    {
        try {
            $query = "SELECT * FROM workouts WHERE trainer_id = :trainer_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":trainer_id", $trainer_id);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }


    public function updateWorkout($id, $name, $mon, $tue, $wed, $thu, $fri, $sat, $sun)
    {
        try {
            $query = "
                UPDATE workouts
                SET name = :name, mon = :mon, tue = :tue, wed = :wed, thu = :thu, fri = :fri, sat = :sat, sun = :sun
                WHERE id = :id";
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":mon", $mon);
            $stmt->bindParam(":tue", $tue);
            $stmt->bindParam(":wed", $wed);
            $stmt->bindParam(":thu", $thu);
            $stmt->bindParam(":fri", $fri);
            $stmt->bindParam(":sat", $sat);
            $stmt->bindParam(":sun", $sun);
            $stmt->bindParam(":id", $id);

            $stmt->execute();

            return $this->getWorkoutById($id);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function deleteWorkout($id)
    {
        try {
            $query = "DELETE FROM workouts WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);

            return $stmt->execute();
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }
}

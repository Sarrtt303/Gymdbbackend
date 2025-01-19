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
}

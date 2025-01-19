<?php

include_once __DIR__ . "/../utils/apiError.php";

class addedWorkoutSchema
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createAddedWorkoutsTable()
    {
        try {
            $query = "SHOW TABLES LIKE 'added_workouts'";
            $stmt = $this->db->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $query = "
                CREATE TABLE IF NOT EXISTS added_workouts(
                    id int AUTO_INCREMENT NOT NULL,
                    user_id int,
                    workout_id int,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (workout_id) REFERENCES workouts(id) ON DELETE CASCADE
                )";

                if ($this->db->exec($query)) {
                    echo "added_workouts table created successfully";
                }
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }
}

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
}

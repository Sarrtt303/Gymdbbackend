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
                CREATE TABLE IF NOT EXISTS goals (
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
}

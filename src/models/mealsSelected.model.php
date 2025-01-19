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
}

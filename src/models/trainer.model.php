<?php

class TrainerSchema
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createTrainersTable()
    {
        try {
            $query = "SHOW TABLES LIKE 'trainers'";
            $stmt = $this->db->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$result) {
                $query = "
                CREATE TABLE IF NOT EXISTS trainers (
                    id int AUTO_INCREMENT NOT NULL,
                    uid int NOT NULL,
                    name VARCHAR(100) NOT NULL,
                    specialization VARCHAR(50) NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    phone VARCHAR(20) NOT NULL,
                    availability ENUM('MON-TUE','WED-THU','FRI-SAT','SUN'),
                    PRIMARY KEY (id),
                    FOREIGN KEY (uid) REFERENCES users(id) ON DELETE CASCADE
                )";

                if ($this->db->exec($query)) {
                    echo "Trainers table created successfully";
                }
            }
        } catch (PDOException $th) {
            echo $th->getMessage();
        }
    }

    //write functions here..
}

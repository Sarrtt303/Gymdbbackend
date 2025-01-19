<?php

class ClassesJoinedSchema
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createClassesJoinedTable()
    {
        try {
            $query = "SHOW TABLES LIKE 'classes_joined'";
            $stmt = $this->db->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$result) {
                $query = "
                CREATE TABLE IF NOT EXISTS classes_joined(
                    id int AUTO_INCREMENT NOT NULL,
                    class_id int NOT NULL,
                    user_id int NOT NULL,
                    PRIMARY KEY (id),
                    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )";

                if ($this->db->exec($query)) {
                    echo "classes_joined table created successfully";
                }
            }
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }

    //write functions here..
}

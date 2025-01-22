<?php

class ClassSchema
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createClassesTable()
    {
        try {
            $query = "SHOW TABLES LIKE 'classes'";
            $stmt = $this->db->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$result) {
                $query = "
                CREATE TABLE IF NOT EXISTS classes(
                    id int AUTO_INCREMENT NOT NULL,
                    name VARCHAR(20) NOT NULL,
                    description VARCHAR(100),
                    trainer_id int NOT NULL,
                    schedule TIME NOT NULL,
                    PRIMARY KEY (id),
                    FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE CASCADE
                )";

                if ($this->db->exec($query)) {
                    echo "Classes table created successfully";
                }
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function createClass($data)
    {
        ['name' => $name, 'description' => $description, 'trainer_id' => $trainer_id, 'schedule' => $schedule] = $data;

        try {
            $query = "INSERT INTO classes (name, description, trainer_id, schedule)
                      VALUES (:name, :description, :trainer_id, :schedule)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":trainer_id", $trainer_id);
            $stmt->bindParam(":schedule", $schedule);

            $stmt->execute();
            return $this->getClassById($this->db->lastInsertId());
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getClassById($id)
    {
        try {
            $query = "SELECT * FROM classes WHERE id = :id";
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

    public function getAllClassesByTrainerId($trainer_id)
    {
        try {
            $query = "SELECT * FROM classes WHERE trainer_id = :trainer_id";
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


    public function updateClass($id, $data)
    {
        try {
            $query = "UPDATE classes SET 
                      name = :name,
                      description = :description,
                      trainer_id = :trainer_id,
                      schedule = :schedule
                      WHERE id = :id";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":name", $data['name']);
            $stmt->bindParam(":description", $data['description']);
            $stmt->bindParam(":trainer_id", $data['trainer_id']);
            $stmt->bindParam(":schedule", $data['schedule']);
            $stmt->bindParam(":id", $id);

            $stmt->execute();
            return $this->getClassById($id);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function deleteClass($id)
    {
        try {
            $query = "DELETE FROM classes WHERE id = :id";
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

    public function getAllClasses()
    {
        try {
            $query = "SELECT * FROM classes";
            $stmt = $this->db->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }
}

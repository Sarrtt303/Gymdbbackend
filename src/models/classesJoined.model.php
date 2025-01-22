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
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function addClassForUser($data)
    {
        ['class_id' => $class_id, 'user_id' => $user_id] = $data;

        try {
            $query = "INSERT INTO classes_joined (class_id, user_id) VALUES (:class_id, :user_id)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":class_id", $class_id);
            $stmt->bindParam(":user_id", $user_id);

            $stmt->execute();
            return $this->getClassJoinedById($this->db->lastInsertId());
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getClassJoinedById($id)
    {
        try {
            $query = "SELECT * FROM classes_joined WHERE id = :id";
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

    public function getAllClassesJoinedByUser($user_id)
    {
        try {
            $query = "
                SELECT cj.id, cj.class_id, c.name AS class_name, c.schedule, c.trainer_id
                FROM classes_joined cj
                INNER JOIN classes c ON cj.class_id = c.id
                WHERE cj.user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function updateClassForUser($id, $data)
    {
        ['class_id' => $class_id, 'user_id' => $user_id] = $data;

        try {
            $query = "UPDATE classes_joined 
                      SET class_id = :class_id, user_id = :user_id 
                      WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":class_id", $class_id);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":id", $id);

            $stmt->execute();
            return $this->getClassJoinedById($id);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function deleteClassJoined($id)
    {
        try {
            $query = "DELETE FROM classes_joined WHERE id = :id";
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

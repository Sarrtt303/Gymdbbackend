<?php

class AttendenceSchema
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createAttendencesTable()
    {
        try {
            $query = "SHOW TABLES LIKE 'attendences'";
            $stmt = $this->db->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$result) {
                $query = "
                CREATE TABLE IF NOT EXISTS attendences(
                    id int AUTO_INCREMENT NOT NULL,
                    uid int NOT NULL,
                    class_id int NOT NULL,
                    date DATE NOT NULL,
                    PRIMARY KEY (id),
                    FOREIGN KEY (uid) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
                )";

                if ($this->db->exec($query)) {
                    echo "Attendence table created successfully";
                }
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function createAttendence($data)
    {
        ['uid' => $uid, 'class_id' => $class_id, 'date' => $date] = $data;

        try {
            $query = "
                INSERT INTO attendences (uid, class_id, date)
                VALUES (:uid, :class_id, :date)";
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':uid', $uid);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':date', $date);

            $stmt->execute();

            return $this->getAttendenceById($this->db->lastInsertId());
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getAttendenceById($id)
    {
        try {
            $query = "SELECT * FROM attendences WHERE id = :id";
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

    public function getAllAttendences()
    {
        try {
            $query = "SELECT * FROM attendences";
            $stmt = $this->db->query($query);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function updateAttendence($id, $data)
    {
        ['uid' => $uid, 'class_id' => $class_id, 'date' => $date] = $data;

        try {
            $query = "
                UPDATE attendences 
                SET uid = :uid, class_id = :class_id, date = :date
                WHERE id = :id";
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(":uid", $uid);
            $stmt->bindParam(":class_id", $class_id);
            $stmt->bindParam(":date", $date);
            $stmt->bindParam(":id", $id);

            $stmt->execute();

            return $this->getAttendenceById($id);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function deleteAttendence($id)
    {
        try {
            $query = "DELETE FROM attendences WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);

            return $stmt->execute();
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getAttendencesByUser($uid)
    {
        try {
            $query = "SELECT * FROM attendences WHERE uid = :uid";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":uid", $uid);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getAttendencesByClass($class_id)
    {
        try {
            $query = "SELECT * FROM attendences WHERE class_id = :class_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":class_id", $class_id);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }
}

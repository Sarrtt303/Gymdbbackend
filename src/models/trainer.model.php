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
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function createTrainer($uid, $name, $specialization, $email, $phone, $availability)
    {
        try {
            $query = "
                INSERT INTO trainers (uid, name, specialization, email, phone, availability)
                VALUES (:uid, :name, :specialization, :email, :phone, :availability)";
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':uid', $uid);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':specialization', $specialization);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':availability', $availability);

            $stmt->execute();

            return $this->getTrainerById($this->db->lastInsertId());
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getTrainerById($id)
    {
        try {
            $query = "SELECT * FROM trainers WHERE id = :id";
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

    public function getAllTrainers()
    {
        try {
            $query = "SELECT * FROM trainers";
            $stmt = $this->db->query($query);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function updateTrainer($id, $uid, $name, $specialization, $email, $phone, $availability)
    {
        try {
            $query = "
                UPDATE trainers
                SET uid = :uid, name = :name, specialization = :specialization, email = :email, phone = :phone, availability = :availability
                WHERE id = :id";
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(":uid", $uid);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":specialization", $specialization);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":phone", $phone);
            $stmt->bindParam(":availability", $availability);
            $stmt->bindParam(":id", $id);

            $stmt->execute();

            return $this->getTrainerById($id);
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function deleteTrainer($id)
    {
        try {
            $query = "DELETE FROM trainers WHERE id = :id";
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

<?php

class MembershipSchema
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createMembershipsTable()
    {
        try {
            $query = "SHOW TABLES LIKE 'memberships'";
            $stmt = $this->db->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $query = "
                CREATE TABLE IF NOT EXISTS memberships(
                    id int AUTO_INCREMENT NOT NULL,
                    name VARCHAR(50) NOT NULL UNIQUE,
                    price DECIMAL(20,2) NOT NULL,
                    duration ENUM('monthly','half-yearly','yearly') NOT NULL,
                    description VARCHAR(100),
                    PRIMARY KEY (id)
                )";

                if ($this->db->exec($query)) {
                    echo "Membership table created successfully";
                }
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function create($data)
    {
        try {
            if ($data) {
                $query = "INSERT INTO memberships (name, price, duration, description)
                      VALUES (:name, :price, :duration, :description)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":name", $data["name"]);
                $stmt->bindParam(":price", $data["price"]);
                $stmt->bindParam(":duration", $data["duration"]);
                $stmt->bindParam(":description", $data["description"]);

                return $stmt->execute();
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function read($id)
    {
        try {
            if ($id) {
                $query = "SELECT name, price, duration, description FROM memberships WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id", $id);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result;
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function readAll()
    {
        try {
            $query = "SELECT name, price, duration, description FROM memberships";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function update($id, $data)
    {
        try {
            if ($id) {
                $query = "UPDATE memberships SET name = :name, price = :price, duration = :duration, description = :description WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":name", $data["name"]);
                $stmt->bindParam(":price", $data["price"]);
                $stmt->bindParam(":duration", $data["duration"]);
                $stmt->bindParam(":description", $data["description"]);
                $stmt->bindParam(":id", $id);

                return $stmt->execute();
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function delete($id)
    {
        try {
            if ($id) {
                $query = "DELETE FROM memberships WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id", $id);
                return $stmt->execute();
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }
}

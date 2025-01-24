<?php
include_once __DIR__ . "/../utils/apiError.php";

use \Firebase\JWT\JWT;

class DashboardSchema
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createDashboardTables()
    {
        try {
            $tables = [
                'attendance' => "CREATE TABLE IF NOT EXISTS attendance (
                    id INT AUTO_INCREMENT NOT NULL,
                    date DATE NOT NULL,
                    time TIME NOT NULL,
                    trainer VARCHAR(100) NOT NULL,
                    session VARCHAR(100) NOT NULL,
                    PRIMARY KEY (id)
                )",

                'workout' => "CREATE TABLE IF NOT EXISTS workout (
                    id INT AUTO_INCREMENT NOT NULL,
                    name VARCHAR(100) NOT NULL,
                    details TEXT NOT NULL,
                    PRIMARY KEY (id)
                )",

                'classes' => "CREATE TABLE IF NOT EXISTS classes (
                    id INT AUTO_INCREMENT NOT NULL,
                    name VARCHAR(100) NOT NULL,
                    date DATE NOT NULL,
                    PRIMARY KEY (id)
                )",

                'goals' => "CREATE TABLE IF NOT EXISTS goals (
                    id INT AUTO_INCREMENT NOT NULL,
                    description TEXT NOT NULL,
                    deadline DATE NOT NULL,
                    PRIMARY KEY (id)
                )",

                'diet' => "CREATE TABLE IF NOT EXISTS diet (
                    id INT AUTO_INCREMENT NOT NULL,
                    meal VARCHAR(50) NOT NULL,
                    details TEXT NOT NULL,
                    PRIMARY KEY (id)
                )",

                'trainer' => "CREATE TABLE IF NOT EXISTS trainer (
                    id INT AUTO_INCREMENT NOT NULL,
                    name VARCHAR(100) NOT NULL,
                    specialization VARCHAR(100) NOT NULL,
                    PRIMARY KEY (id)
                )"
            ];

            foreach ($tables as $tableName => $query) {
                $stmt = $this->db->query("SHOW TABLES LIKE '$tableName'");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$result) {
                    $this->db->exec($query);
                    echo ucfirst($tableName) . " table created successfully\n";
                }
            }
        } catch (PDOException $th) {
            throw new Exception("Database error: " . $th->getMessage(), 500);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    public function getTableData($tableName)
    {
        try {
            $query = "SELECT * FROM $tableName";
            $stmt = $this->db->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching data from $tableName: " . $e->getMessage(), 500);
        }
    }

    public function insertData($tableName, $data)
    {
        try {
            $columns = implode(", ", array_keys($data));
            $placeholders = implode(", ", array_fill(0, count($data), "?"));
            $query = "INSERT INTO $tableName ($columns) VALUES ($placeholders)";

            $stmt = $this->db->prepare($query);
            $stmt->execute(array_values($data));

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error inserting data into $tableName: " . $e->getMessage(), 500);
        }
    }

    public function updateData($tableName, $data, $id)
    {
        try {
            $setString = implode(", ", array_map(fn($key) => "$key = ?", array_keys($data)));
            $query = "UPDATE $tableName SET $setString WHERE id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->execute([...array_values($data), $id]);
        } catch (PDOException $e) {
            throw new Exception("Error updating data in $tableName: " . $e->getMessage(), 500);
        }
    }

    public function deleteData($tableName, $id)
    {
        try {
            $query = "DELETE FROM $tableName WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Error deleting data from $tableName: " . $e->getMessage(), 500);
        }
    }
}

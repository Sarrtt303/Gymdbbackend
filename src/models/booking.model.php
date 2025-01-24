<?php
class BookingModel {
    private $conn;
    private $table = "sessions";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create the sessions table
    public function createTable() {
        $query = "
        CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            trainer_name VARCHAR(255) NOT NULL,
            session_date DATE NOT NULL,
            session_time TIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->conn->exec($query);
    }

    // Insert a session
    public function insertSession($trainerName, $sessionDate, $sessionTime) {
        $query = "INSERT INTO {$this->table} (trainer_name, session_date, session_time) VALUES (:trainer_name, :session_date, :session_time)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':trainer_name', $trainerName);
        $stmt->bindParam(':session_date', $sessionDate);
        $stmt->bindParam(':session_time', $sessionTime);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
}
?>

<?php

class Database //database configuration and connection
{
    private $host;
    private $port;
    private $dbname;
    private $username;
    private $password;
    public $conn; //db connection instance, that'll be used to talk to the db

    public function __construct()
    {
        // Initialize properties using environment variables
        $this->host = $_ENV["DB_HOST"];
        $this->port = $_ENV["DB_PORT"];
        $this->dbname = $_ENV["DB_NAME"];
        $this->username = $_ENV["DB_USERNAME"];
        $this->password = $_ENV["DB_PASSWORD"];

        $this->conn = null;
        try {
            // Correct DSN string creation
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname}";
            // This is the db connection instance that will be used to talk to the db
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            new ApiError($e->getCode(), "Error: " . $e->getMessage());
        } catch (Exception $e) {
            new ApiError($e->getCode(), "Error: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}

<?php
class Database {
    private $host = "localhost";
    private $db_name = "ccsfp_bse";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            throw $e;
        }
    }

    // Add a method to get mysqli connection for legacy code
    public function getMysqliConnection() {
        try {
            $mysqli = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            if ($mysqli->connect_error) {
                throw new Exception("Connection failed: " . $mysqli->connect_error);
            }
            return $mysqli;
        } catch (Exception $e) {
            error_log("Connection Error: " . $e->getMessage());
            throw $e;
        }
    }
}
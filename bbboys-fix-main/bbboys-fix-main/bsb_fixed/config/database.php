<?php
/**
 * Database Configuration
 * Backstreet Boys Fan Website
 */

class Database {
    private $host = "localhost";
    private $db_name = "bsb_fan_website";
    private $username = "root";
    private $password = "";
    private $conn;

    /**
     * Get database connection
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            // FIX #17: Do NOT expose internal DB error details to users.
            // Log internally and return a generic message.
            error_log("Database connection error: " . $exception->getMessage());
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "A database error occurred. Please try again later."]);
            exit();
        }

        return $this->conn;
    }
}
?>

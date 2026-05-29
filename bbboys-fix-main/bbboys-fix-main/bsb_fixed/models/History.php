<?php
/**
 * History Model - Handles database operations for band history timeline events
 */

class History {
    private $conn;
    private $table = "history";
    
    // Properties matching database columns
    public $timeline_id;
    public $year;
    public $title;
    public $description;
    public $last_updated;
    
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all history events with optional search
     */
    public function getAll($search = '') {
        $query = "SELECT * FROM " . $this->table;
        if (!empty($search)) {
            $query .= " WHERE description LIKE :search OR YEAR(year) LIKE :search";
        }
        $query .= " ORDER BY YEAR(year) ASC";
        
        $stmt = $this->conn->prepare($query);
        if (!empty($search)) {
            $searchParam = "%{$search}%";
            $stmt->bindParam(':search', $searchParam);
        }
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get single history event by ID
     */
    public function getSingle() {
        $query = "SELECT * FROM " . $this->table . " WHERE timeline_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->timeline_id]);
        return $stmt;
    }

    /**
     * Create new history event
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (year, title, description) 
                  VALUES (:year, :title, :description)";
        
        $stmt = $this->conn->prepare($query);
        
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        return $stmt->execute([
            ':year' => $this->year,
            ':title' => $this->title,
            ':description' => $this->description
        ]);
    }

    /**
     * Update history event
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET year = :year, title = :title, description = :description
                  WHERE timeline_id = :timeline_id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        return $stmt->execute([
            ':timeline_id' => $this->timeline_id,
            ':year' => $this->year,
            ':title' => $this->title,
            ':description' => $this->description
        ]);
    }

    /**
     * Delete history event
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE timeline_id = :timeline_id";
        $stmt = $this->conn->prepare($query);
        $this->timeline_id = htmlspecialchars(strip_tags($this->timeline_id));
        return $stmt->execute([':timeline_id' => $this->timeline_id]);
    }

    /**
     * Get history events count
     */
    public function getCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }
}
?>

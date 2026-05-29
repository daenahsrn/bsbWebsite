<?php
/**
 * Album Model - Handles database operations for albums
 * Matches the new database schema with: album_id, title, release, cover_img, description
 */

class Album {
    private $conn;
    private $table = "albums";

    // Properties matching database columns
    public $album_id;
    public $title;
    public $release;
    public $cover_img;
    public $description;
    
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all albums with optional search
     */
    public function getAll($search = '') {
        $query = "SELECT * FROM " . $this->table;
        if (!empty($search)) {
            $query .= " WHERE title LIKE :search OR description LIKE :search";
        }
        $query .= " ORDER BY release DESC, title ASC";
        
        $stmt = $this->conn->prepare($query);
        if (!empty($search)) {
            $searchParam = "%{$search}%";
            $stmt->bindParam(':search', $searchParam);
        }
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get single album by ID
     */
    public function getSingle() {
        $query = "SELECT * FROM " . $this->table . " WHERE album_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->album_id]);
        return $stmt;
    }

    /**
     * Create new album
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (title, release, cover_img, description) 
                  VALUES (:title, :release, :cover_img, :description)";
        
        $stmt = $this->conn->prepare($query);
        
        $this->title = htmlspecialchars(strip_tags($this->title));
        if($this->cover_img) $this->cover_img = htmlspecialchars(strip_tags($this->cover_img));
        if($this->description) $this->description = htmlspecialchars(strip_tags($this->description));
        
        return $stmt->execute([
            ':title' => $this->title,
            ':release' => $this->release,
            ':cover_img' => $this->cover_img,
            ':description' => $this->description
        ]);
    }

    /**
     * Update album
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET title = :title, release = :release, cover_img = :cover_img, description = :description
                  WHERE album_id = :album_id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->title = htmlspecialchars(strip_tags($this->title));
        if($this->cover_img) $this->cover_img = htmlspecialchars(strip_tags($this->cover_img));
        if($this->description) $this->description = htmlspecialchars(strip_tags($this->description));
        
        return $stmt->execute([
            ':album_id' => $this->album_id,
            ':title' => $this->title,
            ':release' => $this->release,
            ':cover_img' => $this->cover_img,
            ':description' => $this->description
        ]);
    }

    /**
     * Delete album
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE album_id = :album_id";
        $stmt = $this->conn->prepare($query);
        $this->album_id = htmlspecialchars(strip_tags($this->album_id));
        return $stmt->execute([':album_id' => $this->album_id]);
    }

    /**
     * Get albums count
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

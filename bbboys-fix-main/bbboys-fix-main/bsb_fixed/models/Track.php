<?php
/**
 * Track Model - Handles database operations for songs/tracks
 * Matches the new database schema with: song_id, album_id, title, duration, track_no, lyrics, audio_file
 */

class Track {
    private $conn;
    private $table = "songs";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all tracks with album info
     */
    public function getAll() {
        $query = "SELECT s.*, a.title as album_title 
                  FROM " . $this->table . " s 
                  LEFT JOIN albums a ON s.album_id = a.album_id
                  ORDER BY a.release DESC, s.track_no ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get tracks by album ID
     */
    public function getByAlbumId($albumId) {
        $query = "SELECT * FROM " . $this->table . " WHERE album_id = ? ORDER BY track_no ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$albumId]);
        return $stmt;
    }

    /**
     * Get single track by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE song_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Create new track
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (album_id, title, duration, track_no, lyrics, audio_file) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $data['album_id'],
            $data['title'],
            $data['duration'] ?? null,
            $data['track_no'] ?? null,
            $data['lyrics'] ?? null,
            $data['audio_file'] ?? null
        ]);
    }

    /**
     * Update track
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET album_id = ?, title = ?, duration = ?, track_no = ?, lyrics = ?, audio_file = ?
                  WHERE song_id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $data['album_id'],
            $data['title'],
            $data['duration'] ?? null,
            $data['track_no'] ?? null,
            $data['lyrics'] ?? null,
            $data['audio_file'] ?? null,
            $id
        ]);
    }

    /**
     * Delete track
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE song_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Get tracks count
     */
    public function getCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    /**
     * Get tracks count by album
     */
    public function getCountByAlbum($albumId) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE album_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$albumId]);
        $result = $stmt->fetch();
        return $result['count'];
    }
}
?>

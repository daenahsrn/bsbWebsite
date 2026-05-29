<?php
/**
 * Member Model - Handles database operations for band members
 * Matches the new database schema with: member_id, about_id, name, stage_name, birthdate, nationality, position, profile_img, bio
 */

class Member {
    private $conn;
    private $table = "members";

    // Properties matching database columns
    public $member_id;
    public $about_id;
    public $name;
    public $stage_name;
    public $birthdate;
    public $nationality;
    public $position;
    public $profile_img;
    public $bio;
    
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all members with optional search
     */
    public function getAll($search = '') {
        $query = "SELECT * FROM " . $this->table;
        if (!empty($search)) {
            $query .= " WHERE name LIKE :search OR stage_name LIKE :search OR nationality LIKE :search";
        }
        $query .= " ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        if (!empty($search)) {
            $searchParam = "%{$search}%";
            $stmt->bindParam(':search', $searchParam);
        }
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get single member by ID — FIX #3: added $id parameter so API can call getById($id)
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE member_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSingle() {
        $query = "SELECT * FROM " . $this->table . " WHERE member_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->member_id]);
        return $stmt;
    }

    /**
     * Create new member — FIX #4: accepts $data array so the API can pass values in
     */
    public function create($data = null) {
        // If $data array is provided, populate properties from it
        if (is_array($data)) {
            $this->about_id    = isset($data['about_id'])    ? $data['about_id']    : null;
            $this->name        = isset($data['name'])        ? $data['name']        : (isset($data['full_name']) ? $data['full_name'] : null);
            $this->stage_name  = isset($data['stage_name'])  ? $data['stage_name']  : null;
            $this->birthdate   = isset($data['birthdate'])   ? $data['birthdate']   : null;
            $this->nationality = isset($data['nationality']) ? $data['nationality'] : null;
            $this->position    = isset($data['position'])    ? $data['position']    : null;
            $this->profile_img = isset($data['profile_img']) ? $data['profile_img'] : null;
            $this->bio         = isset($data['bio'])         ? $data['bio']         : null;
        }

        $query = "INSERT INTO " . $this->table . " 
                  (about_id, name, stage_name, birthdate, nationality, position, profile_img, bio) 
                  VALUES (:about_id, :name, :stage_name, :birthdate, :nationality, :position, :profile_img, :bio)";
        
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        if ($this->stage_name)  $this->stage_name  = htmlspecialchars(strip_tags($this->stage_name));
        if ($this->nationality) $this->nationality = htmlspecialchars(strip_tags($this->nationality));
        if ($this->position)    $this->position    = htmlspecialchars(strip_tags($this->position));
        if ($this->profile_img) $this->profile_img = htmlspecialchars(strip_tags($this->profile_img));
        if ($this->bio)         $this->bio         = htmlspecialchars(strip_tags($this->bio));
        
        return $stmt->execute([
            ':about_id'    => $this->about_id,
            ':name'        => $this->name,
            ':stage_name'  => $this->stage_name,
            ':birthdate'   => $this->birthdate,
            ':nationality' => $this->nationality,
            ':position'    => $this->position,
            ':profile_img' => $this->profile_img,
            ':bio'         => $this->bio
        ]);
    }

    /**
     * Update member — FIX #4: accepts $id and $data so the API can pass values in
     */
    public function update($id = null, $data = null) {
        // If $id provided, use it; otherwise fall back to $this->member_id
        if ($id !== null) {
            $this->member_id = $id;
        }
        // If $data array is provided, populate properties from it
        if (is_array($data)) {
            $this->about_id    = isset($data['about_id'])    ? $data['about_id']    : null;
            $this->name        = isset($data['name'])        ? $data['name']        : (isset($data['full_name']) ? $data['full_name'] : null);
            $this->stage_name  = isset($data['stage_name'])  ? $data['stage_name']  : null;
            $this->birthdate   = isset($data['birthdate'])   ? $data['birthdate']   : null;
            $this->nationality = isset($data['nationality']) ? $data['nationality'] : null;
            $this->position    = isset($data['position'])    ? $data['position']    : null;
            $this->profile_img = isset($data['profile_img']) ? $data['profile_img'] : null;
            $this->bio         = isset($data['bio'])         ? $data['bio']         : null;
        }

        $query = "UPDATE " . $this->table . " 
                  SET about_id = :about_id, name = :name, stage_name = :stage_name, birthdate = :birthdate, 
                      nationality = :nationality, position = :position, profile_img = :profile_img, bio = :bio
                  WHERE member_id = :member_id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        if ($this->stage_name)  $this->stage_name  = htmlspecialchars(strip_tags($this->stage_name));
        if ($this->nationality) $this->nationality = htmlspecialchars(strip_tags($this->nationality));
        if ($this->position)    $this->position    = htmlspecialchars(strip_tags($this->position));
        if ($this->profile_img) $this->profile_img = htmlspecialchars(strip_tags($this->profile_img));
        if ($this->bio)         $this->bio         = htmlspecialchars(strip_tags($this->bio));
        
        return $stmt->execute([
            ':member_id'   => $this->member_id,
            ':about_id'    => $this->about_id,
            ':name'        => $this->name,
            ':stage_name'  => $this->stage_name,
            ':birthdate'   => $this->birthdate,
            ':nationality' => $this->nationality,
            ':position'    => $this->position,
            ':profile_img' => $this->profile_img,
            ':bio'         => $this->bio
        ]);
    }

    /**
     * Delete member — FIX #5: accepts $id parameter so API can pass it directly
     */
    public function delete($id = null) {
        if ($id !== null) {
            $this->member_id = $id;
        }
        $query = "DELETE FROM " . $this->table . " WHERE member_id = :member_id";
        $stmt = $this->conn->prepare($query);
        $this->member_id = htmlspecialchars(strip_tags($this->member_id));
        return $stmt->execute([':member_id' => $this->member_id]);
    }

    /**
     * Get members count
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

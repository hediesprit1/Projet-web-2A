<?php
require_once 'config/database.php';

class Reclamation {
    private $conn;
    private $table_name = "reclamation";
    
    public $id;
    public $user_id;
    public $object;
    public $description;
    public $status;
    public $creation_date;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create a new reclamation
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, object=:object, description=:description, status=:status, creation_date=:creation_date";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->object = htmlspecialchars(strip_tags($this->object));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->creation_date = htmlspecialchars(strip_tags($this->creation_date));
        
        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":object", $this->object);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":creation_date", $this->creation_date);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Read all reclamations
    public function readAll() {
        $query = "SELECT r.id, r.user_id, r.object, r.description, r.status, r.creation_date, u.firstname, u.lastname 
                  FROM " . $this->table_name . " r 
                  LEFT JOIN user u ON r.user_id = u.id 
                  ORDER BY r.creation_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Search reclamations by date or user name
    public function search($searchTerm, $dateFrom = null, $dateTo = null) {
        $query = "SELECT r.id, r.user_id, r.object, r.description, r.status, r.creation_date, u.firstname, u.lastname 
                  FROM " . $this->table_name . " r 
                  LEFT JOIN user u ON r.user_id = u.id 
                  WHERE 1 = 1";
        
        $params = [];
        
        // Search by user name
        if (!empty($searchTerm)) {
            $query .= " AND (u.firstname LIKE :search OR u.lastname LIKE :search OR CONCAT(u.firstname, ' ', u.lastname) LIKE :search)";
            $searchParam = '%' . $searchTerm . '%';
            $params[] = ['name' => ':search', 'value' => $searchParam, 'type' => PDO::PARAM_STR];
        }
        
        // Search by date range
        if (!empty($dateFrom)) {
            $query .= " AND r.creation_date >= :dateFrom";
            $params[] = ['name' => ':dateFrom', 'value' => $dateFrom . ' 00:00:00', 'type' => PDO::PARAM_STR];
        }
        
        if (!empty($dateTo)) {
            $query .= " AND r.creation_date <= :dateTo";
            $params[] = ['name' => ':dateTo', 'value' => $dateTo . ' 23:59:59', 'type' => PDO::PARAM_STR];
        }
        
        $query .= " ORDER BY r.creation_date DESC";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        foreach ($params as $param) {
            $stmt->bindValue($param['name'], $param['value'], $param['type']);
        }
        
        $stmt->execute();
        
        return $stmt;
    }
    
    // Read one reclamation by ID
    public function readOne() {
        $query = "SELECT r.id, r.user_id, r.object, r.description, r.status, r.creation_date, u.firstname, u.lastname 
                  FROM " . $this->table_name . " r 
                  LEFT JOIN user u ON r.user_id = u.id 
                  WHERE r.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->object = isset($row['object']) ? $row['object'] : '';
            $this->description = $row['description'];
            $this->status = $row['status'];
            $this->creation_date = $row['creation_date'];
            
            return true;
        }
        return false;
    }
    
    // Update a reclamation
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET user_id=:user_id, object=:object, description=:description, status=:status 
                  WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->object = htmlspecialchars(strip_tags($this->object));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->status = htmlspecialchars(strip_tags($this->status));
        
        // Bind parameters
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":object", $this->object);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":status", $this->status);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Delete a reclamation
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Count reclamations by status
    public function countByStatus($status) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE status = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $status);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['count'];
    }
    
    // Search reclamations by object and date for a specific user
    public function searchByUser($userId, $searchTerm, $dateFrom = null, $dateTo = null) {
        $query = "SELECT r.id, r.user_id, r.object, r.description, r.status, r.creation_date, u.firstname, u.lastname 
                  FROM " . $this->table_name . " r 
                  LEFT JOIN user u ON r.user_id = u.id 
                  WHERE r.user_id = :userId";
        
        $params = [
            ['name' => ':userId', 'value' => $userId, 'type' => PDO::PARAM_INT]
        ];
        
        // Search by object
        if (!empty($searchTerm)) {
            $query .= " AND r.object LIKE :search";
            $searchParam = '%' . $searchTerm . '%';
            $params[] = ['name' => ':search', 'value' => $searchParam, 'type' => PDO::PARAM_STR];
        }
        
        // Search by date range
        if (!empty($dateFrom)) {
            $query .= " AND r.creation_date >= :dateFrom";
            $params[] = ['name' => ':dateFrom', 'value' => $dateFrom . ' 00:00:00', 'type' => PDO::PARAM_STR];
        }
        
        if (!empty($dateTo)) {
            $query .= " AND r.creation_date <= :dateTo";
            $params[] = ['name' => ':dateTo', 'value' => $dateTo . ' 23:59:59', 'type' => PDO::PARAM_STR];
        }
        
        $query .= " ORDER BY r.creation_date DESC";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        foreach ($params as $param) {
            $stmt->bindValue($param['name'], $param['value'], $param['type']);
        }
        
        $stmt->execute();
        
        return $stmt;
    }
}
?> 
<?php
require_once 'config/database.php';

class User {
    private $conn;
    private $table_name = "user";
    
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $password;
    public $role;
    public $creation_date;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Get user by email
    public function getByEmail() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->id = $row['id'];
            $this->firstname = $row['firstname'];
            $this->lastname = $row['lastname'];
            $this->email = $row['email'];
            $this->password = $row['password'];
            $this->role = $row['role'];
            $this->creation_date = $row['creation_date'];
            
            return true;
        }
        return false;
    }
    
    // Get user by ID
    public function getById() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->firstname = $row['firstname'];
            $this->lastname = $row['lastname'];
            $this->email = $row['email'];
            $this->role = $row['role'];
            $this->creation_date = $row['creation_date'];
            
            return true;
        }
        return false;
    }
    
    // Create a new user
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET firstname=:firstname, lastname=:lastname, email=:email, 
                      password=:password, role=:role, creation_date=:creation_date";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->lastname = htmlspecialchars(strip_tags($this->lastname));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->creation_date = htmlspecialchars(strip_tags($this->creation_date));
        
        // Hash the password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        
        // Bind values
        $stmt->bindParam(":firstname", $this->firstname);
        $stmt->bindParam(":lastname", $this->lastname);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":creation_date", $this->creation_date);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Check if email exists
    public function emailExists() {
        $query = "SELECT id, firstname, lastname, password, role 
                  FROM " . $this->table_name . " 
                  WHERE email = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $row['id'];
            $this->firstname = $row['firstname'];
            $this->lastname = $row['lastname'];
            $this->password = $row['password'];
            $this->role = $row['role'];
            
            return true;
        }
        return false;
    }
    
    // Count all users
    public function countAll() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['count'];
    }
    
    // Get all users with role 'user'
    public function getAllUsers() {
        $query = "SELECT id, firstname, lastname FROM " . $this->table_name . " WHERE role = 'user' ORDER BY lastname, firstname";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
}
?> 
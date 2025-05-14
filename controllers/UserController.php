<?php
require_once 'models/User.php';
require_once 'config/database.php';

class UserController {
    private $database;
    private $db;
    private $user;
    
    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        $this->user = new User($this->db);
    }
    
    public function login() {
        // If form submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(
                !empty($_POST['email']) &&
                !empty($_POST['password'])
            ){
                $this->user->email = $_POST['email'];
                
                // Check if email exists and get user data
                if($this->user->emailExists()) {
                    // Verify password
                    if(password_verify($_POST['password'], $this->user->password)) {
                        // Start session
                        if(!isset($_SESSION)) {
                            session_start();
                        }
                        
                        // Store user data in session
                        $_SESSION['id'] = $this->user->id;
                        $_SESSION['firstname'] = $this->user->firstname;
                        $_SESSION['lastname'] = $this->user->lastname;
                        $_SESSION['role'] = $this->user->role;
                        
                        // Redirect to dashboard
                        header("Location: index.php?action=dashboard");
                        exit();
                    } else {
                        $error = "Invalid password.";
                    }
                } else {
                    $error = "Email not found.";
                }
            } else {
                $error = "Email and password are required.";
            }
        }
        
        // Include the view
        include_once 'views/user/login.php';
    }
    
    public function register() {
        // If form submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(
                !empty($_POST['firstname']) &&
                !empty($_POST['lastname']) &&
                !empty($_POST['email']) &&
                !empty($_POST['password']) &&
                !empty($_POST['confirm_password'])
            ){
                // Check if passwords match
                if($_POST['password'] !== $_POST['confirm_password']) {
                    $error = "Passwords do not match.";
                } else {
                    // Check if email already exists
                    $this->user->email = $_POST['email'];
                    if($this->user->emailExists()) {
                        $error = "Email already exists.";
                    } else {
                        // Create new user
                        $this->user->firstname = $_POST['firstname'];
                        $this->user->lastname = $_POST['lastname'];
                        $this->user->password = $_POST['password'];
                        $this->user->role = 'user'; // Default role
                        $this->user->creation_date = date('Y-m-d H:i:s');
                        
                        if($this->user->create()) {
                            // Redirect to login page
                            header("Location: index.php?action=login");
                            exit();
                        } else {
                            $error = "Unable to create user.";
                        }
                    }
                }
            } else {
                $error = "All fields are required.";
            }
        }
        
        // Include the view
        include_once 'views/user/register.php';
    }
    
    public function logout() {
        // Start session
        if(!isset($_SESSION)) {
            session_start();
        }
        
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        header("Location: index.php?action=login");
        exit();
    }
    
    public function dashboard() {
        // Check if user is logged in
        if(!isset($_SESSION)) {
            session_start();
        }
        
        if(!isset($_SESSION['id'])) {
            header("Location: index.php?action=login");
            exit();
        }
        
        // Include the view
        include_once 'views/dashboard.php';
    }
}
?> 
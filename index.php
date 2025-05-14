<?php
// Start session
if(!isset($_SESSION)) {
    session_start();
}

// Include controllers
require_once 'controllers/UserController.php';
require_once 'controllers/ReclamationController.php';
require_once 'controllers/ChatbotController.php';

// Determine the action to take
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Route to the appropriate controller and action
switch($action) {
    // User routes
    case 'login':
        $controller = new UserController();
        $controller->login();
        break;
    
    case 'register':
        $controller = new UserController();
        $controller->register();
        break;
    
    case 'logout':
        $controller = new UserController();
        $controller->logout();
        break;
    
    // Dashboard routes
    case 'dashboard':
        // Redirect to the appropriate dashboard based on role
        if(isset($_SESSION['role'])) {
            if($_SESSION['role'] == 'admin') {
                header("Location: index.php?action=backoffice_dashboard");
            } else {
                header("Location: index.php?action=frontoffice_dashboard");
            }
            exit();
        } else {
            header("Location: index.php?action=login");
            exit();
        }
        break;
    
    // FrontOffice routes
    case 'frontoffice_dashboard':
        $controller = new ReclamationController();
        $controller->frontofficeDashboard();
        break;
    
    case 'frontoffice_reclamations':
        $controller = new ReclamationController();
        $controller->frontofficeIndex();
        break;
    
    case 'frontoffice_reclamation_create':
        $controller = new ReclamationController();
        $controller->frontofficeCreate();
        break;
    
    case 'frontoffice_reclamation_read':
        if(isset($_GET['id'])) {
            $controller = new ReclamationController();
            $controller->frontofficeRead($_GET['id']);
        } else {
            header("Location: index.php?action=frontoffice_reclamations");
            exit();
        }
        break;
    
    case 'frontoffice_reclamation_update':
        if(isset($_GET['id'])) {
            $controller = new ReclamationController();
            $controller->frontofficeUpdate($_GET['id']);
        } else {
            header("Location: index.php?action=frontoffice_reclamations");
            exit();
        }
        break;
    
    case 'frontoffice_reclamation_delete':
        if(isset($_GET['id'])) {
            $controller = new ReclamationController();
            $controller->frontofficeDelete($_GET['id']);
        } else {
            header("Location: index.php?action=frontoffice_reclamations");
            exit();
        }
        break;
    
    // BackOffice routes
    case 'backoffice_dashboard':
        $controller = new ReclamationController();
        $controller->backofficeDashboard();
        break;
    
    case 'backoffice_reclamations':
        $controller = new ReclamationController();
        $controller->backofficeIndex();
        break;
    
    case 'backoffice_reclamation_read':
        if(isset($_GET['id'])) {
            $controller = new ReclamationController();
            $controller->backofficeRead($_GET['id']);
        } else {
            header("Location: index.php?action=backoffice_reclamations");
            exit();
        }
        break;
    
    case 'backoffice_reclamation_update':
        if(isset($_GET['id'])) {
            $controller = new ReclamationController();
            $controller->backofficeUpdate($_GET['id']);
        } else {
            header("Location: index.php?action=backoffice_reclamations");
            exit();
        }
        break;
    
    case 'backoffice_reclamation_delete':
        if(isset($_GET['id'])) {
            $controller = new ReclamationController();
            $controller->backofficeDelete($_GET['id']);
        } else {
            header("Location: index.php?action=backoffice_reclamations");
            exit();
        }
        break;
    
    case 'backoffice_reclamation_validate':
        if(isset($_GET['id'])) {
            $controller = new ReclamationController();
            $controller->backofficeValidate($_GET['id']);
        } else {
            header("Location: index.php?action=backoffice_dashboard");
            exit();
        }
        break;
    
    case 'backoffice_response_create':
        $controller = new ReclamationController();
        $controller->createResponse();
        break;

    // Chatbot routes
    case 'chatbot':
        $controller = new ChatbotController();
        $controller->index();
        break;
    
    case 'process_chatbot_message':
        $controller = new ChatbotController();
        $controller->processMessage();
        break;
    
    // Old routes (for backward compatibility)
    case 'reclamations':
        // Redirect to the appropriate section based on role
        if(isset($_SESSION['role'])) {
            if($_SESSION['role'] == 'admin') {
                header("Location: index.php?action=backoffice_reclamations");
            } else {
                header("Location: index.php?action=frontoffice_reclamations");
            }
            exit();
        } else {
            header("Location: index.php?action=login");
            exit();
        }
        break;
    
    // Default route
    default:
        $controller = new UserController();
        $controller->login();
        break;
}
?> 
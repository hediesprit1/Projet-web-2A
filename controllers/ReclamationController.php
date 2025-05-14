<?php
require_once 'models/Reclamation.php';
require_once 'models/User.php';
require_once 'config/database.php';

class ReclamationController {
    private $database;
    private $db;
    private $reclamation;
    
    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        $this->reclamation = new Reclamation($this->db);
    }
    
    public function index() {
        // Get all reclamations
        $stmt = $this->reclamation->readAll();
        $reclamations = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reclamations[] = $row;
        }
        
        // Count reclamations by status
        $en_cours_count = $this->reclamation->countByStatus('en_cours');
        $valider_count = $this->reclamation->countByStatus('valider');
        
        // Include the view
        include_once 'views/reclamation/index.php';
    }
    
    public function create() {
        // Get all users with role 'user'
        $user_model = new User($this->db);
        $user_stmt = $user_model->getAllUsers();
        $users = [];
        
        while ($row = $user_stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $row;
        }
        
        // If form submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(
                !empty($_POST['user_id']) &&
                !empty($_POST['object']) &&
                !empty($_POST['description'])
            ){
                $this->reclamation->user_id = $_POST['user_id'];
                $this->reclamation->object = $_POST['object'];
                $this->reclamation->description = $_POST['description'];
                $this->reclamation->status = 'en_cours'; // Default status
                $this->reclamation->creation_date = date('Y-m-d H:i:s');
                
                if($this->reclamation->create()) {
                    header("Location: index.php?action=reclamations");
                    exit();
                } else {
                    $error = "Unable to create reclamation.";
                }
            } else {
                $error = "Unable to create reclamation. Data is incomplete.";
            }
        }
        
        // Include the view
        include_once 'views/reclamation/create.php';
    }
    
    public function read($id) {
        $this->reclamation->id = $id;
        
        if($this->reclamation->readOne()) {
            include_once 'views/reclamation/read.php';
        } else {
            header("Location: index.php?action=reclamations");
            exit();
        }
    }
    
    public function update($id) {
        $this->reclamation->id = $id;
        
        // Check if reclamation exists
        if(!$this->reclamation->readOne()) {
            header("Location: index.php?action=reclamations");
            exit();
        }
        
        // Check if user is logged in
        if(!isset($_SESSION['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour modifier une réclamation.";
            header("Location: index.php?action=reclamations");
            exit();
        }
        
        // Get all users with role 'user'
        $user_model = new User($this->db);
        $user_stmt = $user_model->getAllUsers();
        $users = [];
        
        while ($row = $user_stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $row;
        }
        
        // If form submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(
                !empty($_POST['user_id']) &&
                !empty($_POST['object']) &&
                !empty($_POST['description']) &&
                !empty($_POST['status'])
            ){
                $this->reclamation->user_id = $_POST['user_id'];
                $this->reclamation->object = $_POST['object'];
                $this->reclamation->description = $_POST['description'];
                $this->reclamation->status = $_POST['status'];
                
                if($this->reclamation->update()) {
                    header("Location: index.php?action=reclamations");
                    exit();
                } else {
                    $error = "Unable to update reclamation.";
                }
            } else {
                $error = "Unable to update reclamation. Data is incomplete.";
            }
        }
        
        // Include the view
        include_once 'views/reclamation/update.php';
    }
    
    public function delete($id) {
        $this->reclamation->id = $id;
        
        // Check if reclamation exists
        if(!$this->reclamation->readOne()) {
            header("Location: index.php?action=reclamations");
            exit();
        }
        
        // Check if user is logged in
        if(!isset($_SESSION['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour supprimer une réclamation.";
            header("Location: index.php?action=reclamations");
            exit();
        }
        
        if($this->reclamation->delete()) {
            header("Location: index.php?action=reclamations");
            exit();
        } else {
            $error = "Unable to delete reclamation.";
            include_once 'views/reclamation/index.php';
        }
    }
    
    // FrontOffice methods
    public function frontofficeIndex() {
        // Initialiser les variables de recherche
        $searchTerm = '';
        $dateFrom = '';
        $dateTo = '';
        
        // Vérifier si une recherche a été soumise
        if (isset($_GET['search']) || isset($_GET['date_from']) || isset($_GET['date_to'])) {
            $searchTerm = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
            $dateFrom = isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : '';
            $dateTo = isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : '';
            
            // Effectuer la recherche
            $stmt = $this->reclamation->searchByUser($_SESSION['id'], $searchTerm, $dateFrom, $dateTo);
        } else {
            // Get reclamations for current user only if no search parameters
            $query = "SELECT r.id, r.user_id, r.object, r.description, r.status, r.creation_date, u.firstname, u.lastname 
                     FROM reclamation r 
                     LEFT JOIN user u ON r.user_id = u.id 
                     WHERE r.user_id = ? 
                     ORDER BY r.creation_date DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $_SESSION['id']);
            $stmt->execute();
        }
        
        $reclamations = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reclamations[] = $row;
        }
        
        // Count reclamations by status for current user
        $query_en_cours = "SELECT COUNT(*) as count FROM reclamation WHERE user_id = ? AND status = 'en_cours'";
        $stmt_en_cours = $this->db->prepare($query_en_cours);
        $stmt_en_cours->bindParam(1, $_SESSION['id']);
        $stmt_en_cours->execute();
        $row_en_cours = $stmt_en_cours->fetch(PDO::FETCH_ASSOC);
        $en_cours_count = $row_en_cours['count'];
        
        $query_valider = "SELECT COUNT(*) as count FROM reclamation WHERE user_id = ? AND status = 'valider'";
        $stmt_valider = $this->db->prepare($query_valider);
        $stmt_valider->bindParam(1, $_SESSION['id']);
        $stmt_valider->execute();
        $row_valider = $stmt_valider->fetch(PDO::FETCH_ASSOC);
        $valider_count = $row_valider['count'];
        
        // Include the view
        include_once 'views/frontoffice/reclamation/index.php';
    }
    
    public function frontofficeCreate() {
        // Get all users with role 'user'
        $user_model = new User($this->db);
        $user_stmt = $user_model->getAllUsers();
        $users = [];
        
        while ($row = $user_stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $row;
        }
        
        // If form submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(
                !empty($_POST['user_id']) &&
                !empty($_POST['object']) &&
                !empty($_POST['description'])
            ){
                $this->reclamation->user_id = $_POST['user_id'];
                $this->reclamation->object = $_POST['object'];
                $this->reclamation->description = $_POST['description'];
                $this->reclamation->status = 'en_cours'; // Default status
                $this->reclamation->creation_date = date('Y-m-d H:i:s');
                
                if($this->reclamation->create()) {
                    $_SESSION['success'] = "Réclamation créée avec succès.";
                    header("Location: index.php?action=frontoffice_reclamations");
                    exit();
                } else {
                    $error = "Impossible de créer la réclamation.";
                }
            } else {
                $error = "Impossible de créer la réclamation. Les données sont incomplètes.";
            }
        }
        
        // Include the view
        include_once 'views/frontoffice/reclamation/create.php';
    }
    
    public function frontofficeRead($id) {
        $this->reclamation->id = $id;
        
        // Verify if reclamation exists and belongs to current user
        $query = "SELECT r.*, u.firstname, u.lastname 
                 FROM reclamation r 
                 LEFT JOIN user u ON r.user_id = u.id 
                 WHERE r.id = ? AND r.user_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $_SESSION['id']);
        $stmt->execute();
        
        if($stmt->rowCount() == 0) {
            $_SESSION['error'] = "Réclamation non trouvée ou accès non autorisé.";
            header("Location: index.php?action=frontoffice_reclamations");
            exit();
        }
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $reclamation = (object)$row;
        
        // Get responses for this reclamation
        $response_query = "SELECT r.id, r.description, r.creation_date, u.firstname, u.lastname 
                          FROM response r
                          LEFT JOIN user u ON r.admin_id = u.id
                          WHERE r.reclamation_id = ?
                          ORDER BY r.creation_date ASC";
        
        $stmt = $this->db->prepare($response_query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        $responses = [];
        while($response = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $responses[] = $response;
        }
        
        // Include the view
        include_once 'views/frontoffice/reclamation/read.php';
    }
    
    public function frontofficeUpdate($id) {
        $this->reclamation->id = $id;
        
        // Vérifier si la réclamation existe et appartient à l'utilisateur courant
        $query = "SELECT r.*, u.firstname, u.lastname 
                 FROM reclamation r 
                 LEFT JOIN user u ON r.user_id = u.id 
                 WHERE r.id = ? AND r.user_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $_SESSION['id']);
        $stmt->execute();
        
        if($stmt->rowCount() === 0) {
            $_SESSION['error'] = "Réclamation introuvable ou vous n'avez pas les droits pour la modifier.";
            header("Location: index.php?action=frontoffice_reclamations");
            exit();
        }
        
        // Récupérer les détails de la réclamation
        $reclamation = $stmt->fetch(PDO::FETCH_OBJ);
        
        // Vérifier que la réclamation est encore en cours (non validée)
        if($reclamation->status === 'valider') {
            $_SESSION['error'] = "Cette réclamation a déjà été validée et ne peut plus être modifiée.";
            header("Location: index.php?action=frontoffice_reclamations");
            exit();
        }
        
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(
                !empty($_POST['object']) &&
                !empty($_POST['description'])
            ){
                $this->reclamation->user_id = $_SESSION['id']; // Assurer que l'ID utilisateur est celui connecté
                $this->reclamation->object = $_POST['object'];
                $this->reclamation->description = $_POST['description'];
                $this->reclamation->status = 'en_cours'; // Maintenir le statut en cours
                
                if($this->reclamation->update()) {
                    $_SESSION['success'] = "Réclamation mise à jour avec succès.";
                    header("Location: index.php?action=frontoffice_reclamations");
                    exit();
                } else {
                    $error = "Impossible de mettre à jour la réclamation.";
                }
            } else {
                $error = "Impossible de mettre à jour la réclamation. Les données sont incomplètes.";
            }
        }
        
        // Inclure la vue
        include_once 'views/frontoffice/reclamation/update.php';
    }
    
    public function frontofficeDelete($id) {
        $this->reclamation->id = $id;
        
        // Vérifier si la réclamation existe et appartient à l'utilisateur courant
        $query = "SELECT r.*, u.firstname, u.lastname 
                 FROM reclamation r 
                 LEFT JOIN user u ON r.user_id = u.id 
                 WHERE r.id = ? AND r.user_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $_SESSION['id']);
        $stmt->execute();
        
        if($stmt->rowCount() === 0) {
            $_SESSION['error'] = "Réclamation introuvable ou vous n'avez pas les droits pour la supprimer.";
            header("Location: index.php?action=frontoffice_reclamations");
            exit();
        }
        
        // Récupérer les détails de la réclamation
        $reclamation = $stmt->fetch(PDO::FETCH_OBJ);
        
        // Vérifier que la réclamation est encore en cours (non validée)
        if($reclamation->status === 'valider') {
            $_SESSION['error'] = "Cette réclamation a déjà été validée et ne peut plus être supprimée.";
            header("Location: index.php?action=frontoffice_reclamations");
            exit();
        }
        
        // Supprimer la réclamation
        $query_delete = "DELETE FROM reclamation WHERE id = ?";
        $stmt_delete = $this->db->prepare($query_delete);
        $stmt_delete->bindParam(1, $id);
        
        if($stmt_delete->execute()) {
            $_SESSION['success'] = "Réclamation supprimée avec succès.";
            header("Location: index.php?action=frontoffice_reclamations");
            exit();
        } else {
            $_SESSION['error'] = "Impossible de supprimer la réclamation.";
            header("Location: index.php?action=frontoffice_reclamations");
            exit();
        }
    }
    
    // BackOffice methods
    public function backofficeIndex() {
        // Initialiser les variables de recherche
        $searchTerm = '';
        $dateFrom = '';
        $dateTo = '';
        
        // Vérifier si une recherche a été soumise
        if (isset($_GET['search']) || isset($_GET['date_from']) || isset($_GET['date_to'])) {
            $searchTerm = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
            $dateFrom = isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : '';
            $dateTo = isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : '';
            
            // Effectuer la recherche
            $stmt = $this->reclamation->search($searchTerm, $dateFrom, $dateTo);
        } else {
            // Get all reclamations if no search parameters
            $stmt = $this->reclamation->readAll();
        }
        
        $reclamations = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reclamations[] = $row;
        }
        
        // Count reclamations by status
        $en_cours_count = $this->reclamation->countByStatus('en_cours');
        $valider_count = $this->reclamation->countByStatus('valider');
        
        // Include the view
        include_once 'views/backoffice/reclamation/index.php';
    }
    
    public function backofficeRead($id) {
        $this->reclamation->id = $id;
        
        if($this->reclamation->readOne()) {
            // Get responses for this reclamation
            $response_query = "SELECT r.id, r.description, r.creation_date, u.firstname, u.lastname 
                          FROM response r
                          LEFT JOIN user u ON r.admin_id = u.id
                          WHERE r.reclamation_id = ?
                          ORDER BY r.creation_date ASC";
            
            $stmt = $this->db->prepare($response_query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            
            $responses = [];
            while($response = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $responses[] = $response;
            }
            
            $reclamation = $this->reclamation;
            
            // Include the view
            include_once 'views/backoffice/reclamation/read.php';
        } else {
            $_SESSION['error'] = "Réclamation non trouvée.";
            header("Location: index.php?action=backoffice_reclamations");
            exit();
        }
    }
    
    public function backofficeUpdate($id) {
        $this->reclamation->id = $id;
        
        // Check if reclamation exists
        if(!$this->reclamation->readOne()) {
            $_SESSION['error'] = "Réclamation non trouvée.";
            header("Location: index.php?action=backoffice_reclamations");
            exit();
        }
        
        $reclamation = $this->reclamation;
        
        // Get all users with role 'user'
        $user_model = new User($this->db);
        $user_stmt = $user_model->getAllUsers();
        $users = [];
        
        while ($row = $user_stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $row;
        }
        
        // If form submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(
                !empty($_POST['user_id']) &&
                !empty($_POST['object']) &&
                !empty($_POST['description']) &&
                !empty($_POST['status'])
            ){
                $this->reclamation->user_id = $_POST['user_id'];
                $this->reclamation->object = $_POST['object'];
                $this->reclamation->description = $_POST['description'];
                $this->reclamation->status = $_POST['status'];
                
                if($this->reclamation->update()) {
                    $_SESSION['success'] = "Réclamation mise à jour avec succès.";
                    header("Location: index.php?action=backoffice_reclamations");
                    exit();
                } else {
                    $error = "Impossible de mettre à jour la réclamation.";
                }
            } else {
                $error = "Impossible de mettre à jour la réclamation. Les données sont incomplètes.";
            }
        }
        
        // Include the view
        include_once 'views/backoffice/reclamation/update.php';
    }
    
    public function backofficeDelete($id) {
        $this->reclamation->id = $id;
        
        if(!$this->reclamation->readOne()) {
            $_SESSION['error'] = "Réclamation non trouvée.";
            header("Location: index.php?action=backoffice_reclamations");
            exit();
        }
        
        if($this->reclamation->delete()) {
            $_SESSION['success'] = "Réclamation supprimée avec succès.";
            header("Location: index.php?action=backoffice_reclamations");
            exit();
        } else {
            $_SESSION['error'] = "Impossible de supprimer la réclamation.";
            header("Location: index.php?action=backoffice_reclamations");
            exit();
        }
    }
    
    public function backofficeValidate($id) {
        $this->reclamation->id = $id;
        
        // Vérifier si la réclamation existe
        if(!$this->reclamation->readOne()) {
            $_SESSION['error'] = "Réclamation introuvable.";
            header("Location: index.php?action=backoffice_dashboard");
            exit();
        }
        
        // Vérifier que l'utilisateur est un administrateur
        if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = "Vous n'avez pas les droits pour valider cette réclamation.";
            header("Location: index.php?action=backoffice_dashboard");
            exit();
        }
        
        // Vérifier que la réclamation est en cours (pas déjà validée)
        if($this->reclamation->status !== 'en_cours') {
            $_SESSION['error'] = "Cette réclamation a déjà été validée.";
            header("Location: index.php?action=backoffice_dashboard");
            exit();
        }
        
        // Vérifier que le message de validation a été fourni
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['validation_message'])) {
            $validation_message = htmlspecialchars(strip_tags($_POST['validation_message']));
            

            
            // Mise à jour du statut de la réclamation
            $query = "UPDATE reclamation SET status='valider' WHERE id=:id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if($stmt->execute()) {
                // Ajouter une réponse avec le message de validation
                $query_response = "INSERT INTO response SET reclamation_id=:reclamation_id, admin_id=:admin_id, description=:description, creation_date=:creation_date";
                $stmt_response = $this->db->prepare($query_response);
                
                $admin_id = $_SESSION['id'];
                $creation_date = date('Y-m-d H:i:s');
                
                $stmt_response->bindParam(":reclamation_id", $id);
                $stmt_response->bindParam(":admin_id", $admin_id);
                $stmt_response->bindParam(":description", $validation_message);
                $stmt_response->bindParam(":creation_date", $creation_date);
                
                $stmt_response->execute();
                
                $_SESSION['success'] = "Réclamation validée avec succès et réponse envoyée à l'utilisateur.";
            } else {
                $_SESSION['error'] = "Impossible de valider la réclamation.";
            }
        } else {
            $_SESSION['error'] = "Veuillez fournir un message de validation.";
        }
        
        header("Location: index.php?action=backoffice_dashboard");
        exit();
    }
    
    // Response creation (for backoffice)
    public function createResponse() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(
                !empty($_POST['reclamation_id']) &&
                !empty($_POST['admin_id']) &&
                !empty($_POST['description'])
            ){
                // Create response
                $query = "INSERT INTO response SET reclamation_id=:reclamation_id, admin_id=:admin_id, description=:description, creation_date=:creation_date";
                
                $stmt = $this->db->prepare($query);
                
                // Sanitize and bind values
                $reclamation_id = htmlspecialchars(strip_tags($_POST['reclamation_id']));
                $admin_id = htmlspecialchars(strip_tags($_POST['admin_id']));
                $description = htmlspecialchars(strip_tags($_POST['description']));
                $creation_date = date('Y-m-d H:i:s');
                
                // Vérifier la longueur minimale de la description
                if (strlen($description) < 10) {
                    $_SESSION['error'] = "La réponse doit contenir au moins 10 caractères.";
                    header("Location: index.php?action=backoffice_reclamation_read&id=" . $reclamation_id);
                    exit();
                }
                
                $stmt->bindParam(":reclamation_id", $reclamation_id);
                $stmt->bindParam(":admin_id", $admin_id);
                $stmt->bindParam(":description", $description);
                $stmt->bindParam(":creation_date", $creation_date);
                
                if($stmt->execute()) {
                    // If validate checkbox is checked, update reclamation status
                    if(isset($_POST['validate']) && $_POST['validate'] == 1) {
                        $query_update = "UPDATE reclamation SET status='valider' WHERE id=:id";
                        $stmt_update = $this->db->prepare($query_update);
                        $stmt_update->bindParam(":id", $reclamation_id);
                        $stmt_update->execute();
                    }
                    
                    $_SESSION['success'] = "Réponse ajoutée avec succès.";
                } else {
                    $_SESSION['error'] = "Impossible d'ajouter la réponse.";
                }
                
                header("Location: index.php?action=backoffice_reclamation_read&id=" . $reclamation_id);
                exit();
            } else {
                $_SESSION['error'] = "Impossible d'ajouter la réponse. Les données sont incomplètes.";
                header("Location: index.php?action=backoffice_reclamations");
                exit();
            }
        }
    }
    
    // Dashboard for frontoffice
    public function frontofficeDashboard() {
        // Get reclamations count for current user
        $query = "SELECT COUNT(*) as total FROM reclamation WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $_SESSION['id']);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_count = $row['total'];
        
        // Get en_cours count
        $query_en_cours = "SELECT COUNT(*) as count FROM reclamation WHERE user_id = ? AND status = 'en_cours'";
        $stmt_en_cours = $this->db->prepare($query_en_cours);
        $stmt_en_cours->bindParam(1, $_SESSION['id']);
        $stmt_en_cours->execute();
        $row_en_cours = $stmt_en_cours->fetch(PDO::FETCH_ASSOC);
        $en_cours_count = $row_en_cours['count'];
        
        // Get valider count
        $query_valider = "SELECT COUNT(*) as count FROM reclamation WHERE user_id = ? AND status = 'valider'";
        $stmt_valider = $this->db->prepare($query_valider);
        $stmt_valider->bindParam(1, $_SESSION['id']);
        $stmt_valider->execute();
        $row_valider = $stmt_valider->fetch(PDO::FETCH_ASSOC);
        $valider_count = $row_valider['count'];
        
        // Get all reclamations for current user (no limit)
        $query_recent = "SELECT r.id, r.object, r.status, r.creation_date, u.firstname, u.lastname 
                       FROM reclamation r 
                       LEFT JOIN user u ON r.user_id = u.id 
                       WHERE r.user_id = ? 
                       ORDER BY r.creation_date DESC";
        
        $stmt_recent = $this->db->prepare($query_recent);
        $stmt_recent->bindParam(1, $_SESSION['id']);
        $stmt_recent->execute();
        
        $recent_reclamations = [];
        while($row = $stmt_recent->fetch(PDO::FETCH_ASSOC)) {
            $recent_reclamations[] = $row;
        }
        
        // Include the view
        include_once 'views/frontoffice/dashboard.php';
    }
    
    // Dashboard for backoffice
    public function backofficeDashboard() {
        // Get total reclamations count
        $query = "SELECT COUNT(*) as total FROM reclamation";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_count = $row['total'];
        
        // Get en_cours count
        $query_en_cours = "SELECT COUNT(*) as count FROM reclamation WHERE status = 'en_cours'";
        $stmt_en_cours = $this->db->prepare($query_en_cours);
        $stmt_en_cours->execute();
        $row_en_cours = $stmt_en_cours->fetch(PDO::FETCH_ASSOC);
        $en_cours_count = $row_en_cours['count'];
        
        // Get valider count
        $query_valider = "SELECT COUNT(*) as count FROM reclamation WHERE status = 'valider'";
        $stmt_valider = $this->db->prepare($query_valider);
        $stmt_valider->execute();
        $row_valider = $stmt_valider->fetch(PDO::FETCH_ASSOC);
        $valider_count = $row_valider['count'];
        
        // Calculer le taux de validation
        $validation_rate = 0;
        if ($total_count > 0) {
            $validation_rate = ($valider_count / $total_count) * 100;
        }
        
        // Get users count
        $query_users = "SELECT COUNT(*) as count FROM user WHERE role = 'user'";
        $stmt_users = $this->db->prepare($query_users);
        $stmt_users->execute();
        $row_users = $stmt_users->fetch(PDO::FETCH_ASSOC);
        $users_count = $row_users['count'];
        
        // Get recent reclamations
        $query_recent = "SELECT r.id, r.object, r.status, r.creation_date, u.firstname, u.lastname 
                       FROM reclamation r 
                       LEFT JOIN user u ON r.user_id = u.id 
                       ORDER BY r.creation_date DESC LIMIT 10";
        
        $stmt_recent = $this->db->prepare($query_recent);
        $stmt_recent->execute();
        
        $recent_reclamations = [];
        while($row = $stmt_recent->fetch(PDO::FETCH_ASSOC)) {
            $recent_reclamations[] = $row;
        }
        
        // Include the view
        include_once 'views/backoffice/dashboard.php';
    }
}
?> 
<?php
// Check if user is logged in and is admin
$is_logged_in = isset($_SESSION['id']) ? true : false;

// Redirect to login if not logged in or not admin
if (!$is_logged_in || $_SESSION['role'] != 'admin') {
    header("Location: index.php?action=login");
    exit();
}

// Déterminer la page courante pour ajouter la classe active
$current_page = isset($_GET['action']) ? $_GET['action'] : 'backoffice_dashboard';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShareMyRide - Administration</title>
    <!-- CSS de base -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- CSS spécifiques -->
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/buttons.css">
    <link rel="stylesheet" href="assets/css/table.css">
    <link rel="stylesheet" href="assets/css/backoffice.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- jQuery & Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script de validation des réclamations -->
    <script src="js/reclamation-validation.js"></script>
</head>
<body class="backoffice has-sidebar">
    <!-- Barre latérale -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h1><i class="fas fa-car"></i> <span>ShareMyRide</span></h1>
        </div>
        <ul class="sidebar-nav">
            <li><a href="index.php?action=backoffice_dashboard" <?php echo ($current_page == 'backoffice_dashboard') ? 'class="active"' : ''; ?>><i class="fas fa-tachometer-alt"></i> <span>Tableau de bord</span></a></li>
            <li><a href="index.php?action=backoffice_reclamations" <?php echo ($current_page == 'backoffice_reclamations') ? 'class="active"' : ''; ?>><i class="fas fa-exclamation-circle"></i> <span>Réclamations</span></a></li>
            <li><a href="index.php?action=backoffice_users" <?php echo ($current_page == 'backoffice_users') ? 'class="active"' : ''; ?>><i class="fas fa-users"></i> <span>Utilisateurs</span></a></li>
            <li><a href="index.php?action=logout"><i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span></a></li>
        </ul>
    </div>

    <!-- Bouton toggle pour mobile -->
    <button class="sidebar-toggle" id="sidebar-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Contenu principal -->
    <div class="main-content">
        <div class="container">
            <?php
            // Check for error or success messages in session
            if(isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            
            if(isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
            ?>
        </div>
    </div>
</body>
</html> 
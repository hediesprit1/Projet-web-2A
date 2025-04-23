<?php
// Check if user is logged in
$is_logged_in = isset($_SESSION['id']) ? true : false;

// Redirect to login if not logged in
if (!$is_logged_in || $_SESSION['role'] != 'user') {
    header("Location: index.php?action=login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShareMyRide - Espace Client</title>
    <!-- CSS de base -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- CSS spécifiques -->
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/buttons.css">
    <link rel="stylesheet" href="assets/css/table.css">
    <link rel="stylesheet" href="assets/css/frontoffice.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="frontoffice">
    <header class="header">
        <div class="container">
            <h1><i class="fas fa-car"></i> ShareMyRide | Espace Client</h1>
            <ul class="nav">
                <li><a href="index.php?action=frontoffice_dashboard"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                <li><a href="index.php?action=frontoffice_reclamations"><i class="fas fa-exclamation-circle"></i> Mes Réclamations</a></li>
                <li><a href="index.php?action=frontoffice_reclamation_create"><i class="fas fa-plus-circle"></i> Nouvelle Réclamation</a></li>
                <li><a href="index.php?action=chatbot"><i class="fas fa-robot"></i> Assistant Virtuel</a></li>
                <li><a href="index.php?action=logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </div>
    </header>
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
</body>
</html> 
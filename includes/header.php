<?php
// Check if user is logged in
$is_logged_in = isset($_SESSION['id']) ? true : false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShareMyRide - Covoiturage</title>
    <!-- CSS de base -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- CSS spécifiques -->
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/buttons.css">
    <link rel="stylesheet" href="assets/css/auth.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/table.css">
    <link rel="stylesheet" href="assets/css/reclamation.css">
    <link rel="stylesheet" href="assets/css/form-check.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <h1><i class="fas fa-car"></i> ShareMyRide</h1>
            <ul class="nav">
                <?php if($is_logged_in): ?>
                    <li><a href="index.php?action=dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="index.php?action=reclamations"><i class="fas fa-exclamation-circle"></i> Réclamations</a></li>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <li><a href="index.php?action=users"><i class="fas fa-users"></i> Utilisateurs</a></li>
                    <?php endif; ?>
                    <li><a href="index.php?action=logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="index.php?action=login"><i class="fas fa-sign-in-alt"></i> Connexion</a></li>
                    <li><a href="index.php?action=register"><i class="fas fa-user-plus"></i> Inscription</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>
    <div class="container"><?php
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
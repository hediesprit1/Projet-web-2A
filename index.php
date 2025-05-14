<?php
// Démarrage de la session
if (!isset($_SESSION)) {
    session_start();
}

// Si l'utilisateur est déjà connecté, redirection selon son rôle
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'admin') {
        header('Location: view/back-office/index.php');
        exit;
    } else {
        header('Location: view/front-office/index.php');
        exit;
    }
} else {
    // Sinon, redirection vers la page de connexion
    header('Location: view/login.php');
    exit;
}
?> 
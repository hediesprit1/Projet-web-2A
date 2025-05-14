<?php
// Démarrage de la session
if (!isset($_SESSION)) {
    session_start();
}

// Vérification que l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
// ... contenu existant ... 
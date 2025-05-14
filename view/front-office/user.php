<?php
// Démarrage de la session
if (!isset($_SESSION)) {
    session_start();
}

// Vérification que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
// ... contenu existant ... 
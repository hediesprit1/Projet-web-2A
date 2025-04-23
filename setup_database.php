<?php
// Script pour initialiser la base de données

// Paramètres de connexion à la base de données
$host = "localhost";
$username = "root";
$password = "";

try {
    // Connexion sans spécifier de base de données
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Initialisation de la base de données</h2>";
    
    // Lire le fichier SQL
    $sql = file_get_contents('createdb.sql');
    
    // Exécuter le script SQL
    $conn->exec($sql);
    
    echo "<p style='color: green;'>Base de données initialisée avec succès!</p>";
    echo "<p>Vous pouvez maintenant accéder à l'application: <a href='index.php'>Commencer</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
}
?> 
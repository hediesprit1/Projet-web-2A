<?php
// Démarrer la session au tout début
session_start();

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Simuler $_SERVER variables pour le test
$_SERVER['HTTP_HOST'] = 'localhost';

// Simuler un utilisateur connecté avec un email différent de l'administrateur
$_SESSION['user_id'] = 123;
$_SESSION['user_nom'] = 'Utilisateur';
$_SESSION['user_prenom'] = 'Test';
$_SESSION['user_email'] = 'test.utilisateur@example.com'; // Email différent de l'administrateur pour tester
$_SESSION['user_role'] = 'client';

// Charger les dépendances
require_once('vendor/phpmailer/PHPMailer.php');
include_once('controller/EmailService.php');

echo "<h1>Test d'envoi d'email à l'utilisateur connecté</h1>";
echo "<p>L'utilisateur connecté est: <strong>{$_SESSION['user_prenom']} {$_SESSION['user_nom']}</strong> ({$_SESSION['user_email']})</p>";

// Créer un véhicule de test
$testVehicule = [
    'id' => 999,
    'marque' => 'Test',
    'modele' => 'Email',
    'matricule' => '12345',
    'couleur' => 'Bleu',
    'type' => 'Citadine',
    'capacite' => '5',
    'categorie' => 'Economique',
    'image' => '' // Pas d'image pour le test
];

// Instancier le service d'email
$emailService = new EmailService();

try {
    echo "<h2>Tentative d'envoi d'email...</h2>";
    echo "<pre>";
    
    // Afficher l'état de la session
    echo "État de la session avant envoi:\n";
    echo "user_email: " . (isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'Non définie') . "\n";
    
    // Envoi de l'email à l'utilisateur connecté
    $result = $emailService->sendVehicleAddedEmail($testVehicule);
    
    echo "</pre>";
    
    // Affichage du résultat
    if ($result) {
        echo "<h2 style='color:green;'>Email envoyé avec succès à {$_SESSION['user_email']}!</h2>";
    } else {
        echo "<h2 style='color:red;'>Échec de l'envoi d'email.</h2>";
    }
} catch (Exception $e) {
    echo "<h2 style='color:red;'>Erreur lors de l'envoi de l'email:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}

// Afficher le statut des logs
echo "<h2>Vérification des logs</h2>";

// Créer le dossier logs s'il n'existe pas
if (!is_dir('logs')) {
    mkdir('logs', 0755);
}

if (file_exists('logs/email_error.log')) {
    echo "<h3>Journal des erreurs d'email:</h3>";
    echo "<pre>" . htmlspecialchars(file_get_contents('logs/email_error.log')) . "</pre>";
}

if (file_exists('logs/email_success.log')) {
    echo "<h3>Journal des succès d'email:</h3>";
    echo "<pre>" . htmlspecialchars(file_get_contents('logs/email_success.log')) . "</pre>";
}

if (file_exists('logs/email_recipient.log')) {
    echo "<h3>Journal des destinataires d'email:</h3>";
    echo "<pre>" . htmlspecialchars(file_get_contents('logs/email_recipient.log')) . "</pre>";
}

if (file_exists('logs/session_debug.log')) {
    echo "<h3>Journal de débogage de session:</h3>";
    echo "<pre>" . htmlspecialchars(file_get_contents('logs/session_debug.log')) . "</pre>";
}
?> 
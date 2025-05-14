<?php
// Script pour ajouter la colonne 'object' à la table reclamation

require_once 'config/database.php';

try {
    // Créer une connexion à la base de données
    $database = new Database();
    $db = $database->getConnection();
    
    // Vérifier si la colonne existe déjà
    $checkQuery = "SHOW COLUMNS FROM reclamation LIKE 'object'";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute();
    
    if($checkStmt->rowCount() == 0) {
        // Ajouter la colonne 'object' à la table reclamation
        $query = "ALTER TABLE reclamation ADD COLUMN object VARCHAR(255) AFTER user_id";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        echo "<div style='padding: 20px; background-color: #d4edda; color: #155724; margin-bottom: 20px; border-radius: 5px;'>
              La colonne 'object' a été ajoutée avec succès à la table reclamation.
              </div>";
    } else {
        echo "<div style='padding: 20px; background-color: #fff3cd; color: #856404; margin-bottom: 20px; border-radius: 5px;'>
              La colonne 'object' existe déjà dans la table reclamation.
              </div>";
    }
    
    // Lien pour retourner à l'application
    echo "<div style='padding: 20px;'>
          <a href='index.php' style='padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Retour à l'application</a>
          </div>";
    
} catch(PDOException $e) {
    echo "<div style='padding: 20px; background-color: #f8d7da; color: #721c24; margin-bottom: 20px; border-radius: 5px;'>
          Erreur: " . $e->getMessage() . "
          </div>";
}
?> 
<?php
require_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../model/Vehicule.php');
include_once(__DIR__ . '/EmailService.php');

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class vehiculeC
{
    // Create method with typevehicule_id
    public function create($vehicule)
    {
        // Assurer que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $sql = "INSERT INTO vehicule (matricule, couleur, modele, marque, typevehicule_id, image, date_ajout) 
                VALUES (:matricule, :couleur, :modele, :marque, :typevehicule_id, :image, NOW())";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'matricule' => $vehicule->getMatricule(),
                'couleur' => $vehicule->getCouleur(),
                'modele' => $vehicule->getModele(),
                'marque' => $vehicule->getMarque(),
                'typevehicule_id' => $vehicule->getTypevehiculeId(),
                'image' => $vehicule->getImage()
            ]);
            
            // Récupérer l'ID du véhicule nouvellement inséré
            $insertedId = $db->lastInsertId();
            
            // Récupérer les détails du véhicule ajouté avec les infos du type
            $sql = "SELECT vehicule.*, typevehicule.type, typevehicule.capacite, typevehicule.categorie
                    FROM vehicule
                    LEFT JOIN typevehicule ON vehicule.typevehicule_id = typevehicule.id
                    WHERE vehicule.id = :id";
            $query = $db->prepare($sql);
            $query->execute(['id' => $insertedId]);
            $vehiculeDetails = $query->fetch();
            
            // Envoyer un email de notification
            if ($vehiculeDetails) {
                $emailService = new EmailService();
                
                // Vérifier et journaliser l'utilisateur connecté
                if (isset($_SESSION['user_email'])) {
                    // Log pour le debugging
                    file_put_contents(__DIR__ . '/../logs/session_debug.log', 
                        date('Y-m-d H:i:s') . ': Utilisateur connecté lors de l\'ajout du véhicule: ' . 
                        $_SESSION['user_email'] . "\n", FILE_APPEND);
                } else {
                    // Log pour le debugging
                    file_put_contents(__DIR__ . '/../logs/session_debug.log', 
                        date('Y-m-d H:i:s') . ': Aucun utilisateur connecté lors de l\'ajout du véhicule' . "\n", 
                        FILE_APPEND);
                }
                
                $emailService->sendVehicleAddedEmail($vehiculeDetails);
            }
            
            header('Location: vehicules.php');
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // Read all vehicles including typevehicule_id
    public function read()
    {
        $sql = "SELECT * FROM vehicule";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    // Read all vehicles with type information
    public function readfront()
    {
        $sql = "SELECT vehicule.*, typevehicule.type, typevehicule.capacite, typevehicule.categorie
                FROM vehicule
                LEFT JOIN typevehicule ON vehicule.typevehicule_id = typevehicule.id
                ORDER BY vehicule.id DESC";
                
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }
    
    // Count total vehicles
    public function countVehicules()
    {
        $sql = "SELECT COUNT(*) as total FROM vehicule";
        $db = config::getConnexion();
        try {
            $result = $db->query($sql);
            $count = $result->fetch();
            return $count['total'];
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    // Find a specific vehicle by id
    public function findOne($id)
    {
        $sql = "SELECT vehicule.*, typevehicule.type, typevehicule.capacite, typevehicule.categorie
                FROM vehicule
                LEFT JOIN typevehicule ON vehicule.typevehicule_id = typevehicule.id
                WHERE vehicule.id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    // Update a vehicle with typevehicule_id
    public function update($vehicule, $id)
    {
        $sql = "UPDATE vehicule SET 
                matricule = :matricule,
                couleur = :couleur,
                modele = :modele,
                marque = :marque,
                typevehicule_id = :typevehicule_id";
        
        $params = [
            'matricule' => $vehicule->getMatricule(),
            'couleur' => $vehicule->getCouleur(),
            'modele' => $vehicule->getModele(),
            'marque' => $vehicule->getMarque(),
            'typevehicule_id' => $vehicule->getTypevehiculeId(),
            'id' => $id
        ];
        
        // Only update image if a new one is provided
        if($vehicule->getImage() != null) {
            $sql .= ", image = :image";
            $params['image'] = $vehicule->getImage();
        }
        
        $sql .= " WHERE id = :id";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute($params);
            header("Location: vehicules.php");
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // Delete a vehicle by id
    public function delete()
    {
        if (isset($_GET['delete'])) {
            $id = $_GET['delete'];
            $sql = "DELETE FROM vehicule WHERE id = '$id'";
            $db = config::getConnexion();
            try {
                $db->prepare($sql)->execute();
                header("Location: vehicules.php");
            } catch (Exception $e) {
                die('Erreur:' . $e->getMessage());
            }
        }
    }
}

<?php
require_once('C:/xampp/htdocs/gvehicule/config.php');
include 'C:/xampp/htdocs/gvehicule/model/Vehicule.php';

class vehiculeC
{
    // Create method with typevehicule_id
    public function create($vehicule)
    {
        $sql = "INSERT INTO vehicule (matricule, couleur, modele, marque, typevehicule_id) 
                VALUES (:matricule, :couleur, :modele, :marque, :typevehicule_id)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'matricule' => $vehicule->getMatricule(),
                'couleur' => $vehicule->getCouleur(),
                'modele' => $vehicule->getModele(),
                'marque' => $vehicule->getMarque(),
                'typevehicule_id' => $vehicule->getTypevehiculeId() // Added typevehicule_id
            ]);
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

    // Read all vehicles including typevehicule_id
    public function readfront()
    {
        $sql = "SELECT vehicule.*, typevehicule.type
FROM vehicule
JOIN typevehicule ON vehicule.typevehicule_id = typevehicule.id;
";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    // Find a specific vehicle by id
    public function findOne($id)
    {
        $sql = "SELECT * FROM vehicule WHERE id = '$id'";
        $db = config::getConnexion();
        try {
            $res = $db->query($sql);
            return $res->fetch();
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
                typevehicule_id = :typevehicule_id
                WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'matricule' => $vehicule->getMatricule(),
                'couleur' => $vehicule->getCouleur(),
                'modele' => $vehicule->getModele(),
                'marque' => $vehicule->getMarque(),
                'typevehicule_id' => $vehicule->getTypevehiculeId(), // Include typevehicule_id
                'id' => $id
            ]);
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

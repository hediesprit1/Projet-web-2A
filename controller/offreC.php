<?php
require_once('C:/xampp/htdocs/web/config.php');
include 'C:/xampp/htdocs/web/model/offre.php';

class offreC
{
    public function create($offre)
    {
        $sql = "INSERT INTO offre (id_vehicule, dispo, prix) 
                VALUES (:id_vehicule, :dispo, :prix)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_vehicule' => $offre->getIdVehicule(),
                'dispo' => $offre->getDispo(),
                'prix' => $offre->getPrix(),
            ]);
            header('Location:offres.php');
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function read()
    {
        $sql = "SELECT * FROM offre";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    public function readDispo()
    {
        $sql = "SELECT * FROM offre where dispo = 1";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    public function findone($id)
    {
        $sql = "SELECT * FROM offre WHERE id = '$id'";
        $db = config::getConnexion();
        try {
            $res = $db->query($sql);
            return $res->fetch();
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    public function update($offre, $id)
    {
        $sql = "UPDATE offre SET 
                id_vehicule = :id_vehicule,
                dispo = :dispo,
                prix = :prix 
                WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_vehicule' => $offre->getIdVehicule(),
                'dispo' => $offre->getDispo(),
                'prix' => $offre->getPrix(),
                'id' => $id
            ]);
            header("Location: offres.php");
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function delete()
    {
        if (isset($_GET['delete'])) {
            $id = $_GET['delete'];
            $sql = "DELETE FROM offre WHERE id = '$id'";
            $db = config::getConnexion();
            try {
                $db->prepare($sql)->execute();
                header("Location:offres.php");
            } catch (Exception $e) {
                die('Erreur:' . $e->getMessage());
            }
        }
    }
}

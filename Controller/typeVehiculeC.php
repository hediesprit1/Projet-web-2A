<?php
require_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../model/TypeVehicule.php');

class typeVehiculeC
{
    public function create($typeVehicule)
    {
        $sql = "INSERT INTO typevehicule (type, capacite, categorie) 
                VALUES (:type, :capacite, :categorie)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'type' => $typeVehicule->getType(),
                'capacite' => $typeVehicule->getCapacite(),
                'categorie' => $typeVehicule->getCategorie(),
            ]);
            header('Location: types.php');
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function read()
    {
        $sql = "SELECT * FROM typevehicule";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    public function findOne($id)
    {
        $sql = "SELECT * FROM typevehicule WHERE id = '$id'";
        $db = config::getConnexion();
        try {
            $res = $db->query($sql);
            return $res->fetch();
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    public function update($typeVehicule, $id)
    {
        $sql = "UPDATE typevehicule SET 
                type = :type,
                capacite = :capacite,
                categorie = :categorie
                WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'type' => $typeVehicule->getType(),
                'capacite' => $typeVehicule->getCapacite(),
                'categorie' => $typeVehicule->getCategorie(),
                'id' => $id
            ]);
            header("Location: typeVehicules.php");
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function delete()
    {
        if (isset($_GET['delete'])) {
            $id = $_GET['delete'];
            $sql = "DELETE FROM typevehicule WHERE id = '$id'";
            $db = config::getConnexion();
            try {
                $db->prepare($sql)->execute();
                header("Location: types.php");
            } catch (Exception $e) {
                die('Erreur:' . $e->getMessage());
            }
        }
    }
}

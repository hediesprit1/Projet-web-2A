<?php
require_once('C:/xampp/htdocs/gblog/config.php');
include 'C:/xampp/htdocs/gblog/model/Category.php';

class categoryC
{
    public function create($category)
    {
        $sql = "INSERT INTO category (name, description) 
                VALUES (:name, :description)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'name' => $category->getName(),
                'description' => $category->getDescription(),
            ]);
            header('Location: categories.php');
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function read()
    {
        $sql = "SELECT * FROM category";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    public function findOne($id)
    {
        $sql = "SELECT * FROM category WHERE id = '$id'";
        $db = config::getConnexion();
        try {
            $res = $db->query($sql);
            return $res->fetch();
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    public function update($category, $id)
    {
        $sql = "UPDATE category SET 
                name = :name,
                description = :description
                WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'name' => $category->getName(),
                'description' => $category->getDescription(),
                'id' => $id
            ]);
            header("Location: categories.php");
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function delete()
    {
        if (isset($_GET['delete'])) {
            $id = $_GET['delete'];
            $sql = "DELETE FROM category WHERE id = '$id'";
            $db = config::getConnexion();
            try {
                $db->prepare($sql)->execute();
                header("Location: categories.php");
            } catch (Exception $e) {
                die('Erreur:' . $e->getMessage());
            }
        }
    }
}

<?php
require_once('C:/xampp/htdocs/gblog/config.php');
include 'C:/xampp/htdocs/gblog/model/Blog.php';

class blogC
{
    public function create($blog)
    {
        $sql = "INSERT INTO blog (title, content, category_id) 
                VALUES (:title, :content, :category_id)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'title' => $blog->getTitle(),
                'content' => $blog->getContent(),
                'category_id' => $blog->getCategoryId(),
            ]);
            header('Location: blogs.php');
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function read()
    {
        $sql = "SELECT * FROM blog";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    public function readFront()
    {
        $sql = "SELECT 
                    blog.id AS blog_id,
                    blog.title,
                    blog.content,
                    blog.category_id,
                    category.name AS category_name,
                    category.description AS category_description
                FROM 
                    blog
                LEFT JOIN 
                    category ON blog.category_id = category.id;
                ";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    public function findOne($id)
    {
        $sql = "SELECT * FROM blog WHERE id = '$id'";
        $db = config::getConnexion();
        try {
            $res = $db->query($sql);
            return $res->fetch();
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    public function update($blog, $id)
    {
        $sql = "UPDATE blog SET 
                title = :title,
                content = :content,
                category_id = :category_id
                WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'title' => $blog->getTitle(),
                'content' => $blog->getContent(),
                'category_id' => $blog->getCategoryId(),
                'id' => $id
            ]);
            header("Location: blogs.php");
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function delete()
    {
        if (isset($_GET['delete'])) {
            $id = $_GET['delete'];
            $sql = "DELETE FROM blog WHERE id = '$id'";
            $db = config::getConnexion();
            try {
                $db->prepare($sql)->execute();
                header("Location: blogs.php");
            } catch (Exception $e) {
                die('Erreur:' . $e->getMessage());
            }
        }
    }
}

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

    public function like($idu, $idb)
    {
        $db = config::getConnexion();
    
        try {
            $checkSql = "SELECT COUNT(*) FROM likes WHERE idu = :idu AND idb = :idb";
            $checkQuery = $db->prepare($checkSql);
            $checkQuery->execute(['idu' => $idu, 'idb' => $idb]);
            $exists = $checkQuery->fetchColumn();
    
            if ($exists) {
                // Supprimer le like
                $deleteSql = "DELETE FROM likes WHERE idu = :idu AND idb = :idb";
                $deleteQuery = $db->prepare($deleteSql);
                $deleteQuery->execute(['idu' => $idu, 'idb' => $idb]);
    
                // JS Alert
                echo "<script>alert('Like supprimé !'); window.location.href = 'index.php';</script>";
            } else {
                // Ajouter le like
                $insertSql = "INSERT INTO likes (idu, idb) VALUES (:idu, :idb)";
                $insertQuery = $db->prepare($insertSql);
                $insertQuery->execute(['idu' => $idu, 'idb' => $idb]);
    
                echo "<script>alert('Like ajouté !'); window.location.href = 'index.php';</script>";
            }
        } catch (Exception $e) {
            echo "<script>alert('Erreur: " . addslashes($e->getMessage()) . "'); window.location.href = 'index.php';</script>";
        }
    }
    
    public function search($r)
    {
        $r = "%$r%"; // Adding wildcard for 'LIKE' pattern matching
    
        // Ensure the query string is properly structured and safe
        $sql = "SELECT * FROM blog WHERE title LIKE :r OR id LIKE :r OR content LIKE :r";
    
        $db = config::getConnexion();
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':r', $r, PDO::PARAM_STR); // Use parameter binding for safety
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    

    
    public function sort($r)
    {
        $sql = "SELECT * FROM blog ORDER BY ". $r;
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
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

    public function countBlogsToday()
    {
        $sql = "SELECT COUNT(*) FROM blog WHERE DATE(created_at) = CURDATE()";
        $db = config::getConnexion();
        
        try {
            $stmt = $db->query($sql);
            $count = $stmt->fetchColumn();
            return $count; // Return the count of blogs created today
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function getTopBlogsByLikes()
    {
        $sql = "SELECT blog.id, blog.title, blog.content, blog.category_id, COUNT(likes.idb) AS like_count 
                FROM blog
                LEFT JOIN likes ON blog.id = likes.idb
                GROUP BY blog.id
                ORDER BY like_count DESC
                LIMIT 3";

        $db = config::getConnexion();
        
        try {
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch and return the top 3 blogs
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }


}

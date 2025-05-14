<?php
require_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../model/User.php');

class userC
{
    // Inscrire un nouvel utilisateur
    public function register($user)
    {
        // Hachage du mot de passe
        $hashedPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO user (nom, prenom, email, password, role) 
                VALUES (:nom, :prenom, :email, :password, :role)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'password' => $hashedPassword,
                'role' => $user->getRole()
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return false;
        }
    }

    // Vérifier si un email existe déjà dans la base de données
    public function emailExists($email) {
        $sql = "SELECT * FROM user WHERE email = :email";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['email' => $email]);
            return $query->rowCount() > 0;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return false;
        }
    }

    // Connexion d'un utilisateur
    public function login($email, $password)
    {
        $sql = "SELECT * FROM user WHERE email = :email";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['email' => $email]);
            $user = $query->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Démarrage de la session
                if (!isset($_SESSION)) {
                    session_start();
                }
                
                // Stockage des informations utilisateur en session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirection selon le rôle
                if ($user['role'] == 'admin') {
                    header('Location: ../view/back-office/index.php');
                } else {
                    header('Location: ../view/front-office/index.php');
                }
                return true;
            }
            return false;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return false;
        }
    }

    // Vérifier si l'utilisateur est connecté
    public function isLoggedIn()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    // Vérifier si l'utilisateur est admin
    public function isAdmin()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
    }

    // Déconnexion
    public function logout()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        // Destruction de toutes les variables de session
        $_SESSION = array();
        
        // Destruction de la session
        session_destroy();
        
        // Redirection vers la page de connexion
        header('Location: ../view/login.php');
        exit;
    }

    // Récupérer tous les utilisateurs (pour l'admin)
    public function getAllUsers() {
        $sql = "SELECT * FROM user";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    // Supprimer un utilisateur
    public function deleteUser($id) {
        $sql = "DELETE FROM user WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return true;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return false;
        }
    }
} 
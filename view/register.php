<?php
// Démarrage de la session
if (!isset($_SESSION)) {
    session_start();
}

// Si l'utilisateur est déjà connecté, redirection selon son rôle
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'admin') {
        header('Location: back-office/index.php');
        exit;
    } else {
        header('Location: front-office/index.php');
        exit;
    }
}

// Traitement du formulaire d'inscription
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
        require_once('../controller/userC.php');
        require_once('../model/User.php');
        
        $userC = new userC();
        
        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Vérifications de base
        if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
            $error = "Veuillez remplir tous les champs";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Adresse email invalide";
        } else if ($password != $confirm_password) {
            $error = "Les mots de passe ne correspondent pas";
        } else if (strlen($password) < 6) {
            $error = "Le mot de passe doit contenir au moins 6 caractères";
        } else if ($userC->emailExists($email)) {
            $error = "Cette adresse email est déjà utilisée";
        } else {
            // Création de l'utilisateur
            $user = new User($nom, $prenom, $email, $password, "user");
            
            if ($userC->register($user)) {
                $success = "Compte créé avec succès. Vous pouvez maintenant vous connecter.";
                // Redirection vers la page de connexion après 2 secondes
                header("refresh:2;url=login.php");
            } else {
                $error = "Une erreur est survenue lors de l'inscription";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Inscription - ShareMyRide</title>
    
    <!-- Favicons -->
    <link href="front-office/assets/img/favicon.png" rel="icon">
    <link href="front-office/assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    
    <!-- Vendor CSS Files -->
    <link href="front-office/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="front-office/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Main CSS File -->
    <link href="front-office/assets/css/main.css" rel="stylesheet">
    
    <style>
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }
        .register-form {
            background-color: var(--surface-color);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 550px;
        }
        .register-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-logo h1 {
            font-size: 32px;
            margin: 0;
        }
        .error-message {
            color: #ff3860;
            margin-bottom: 20px;
            text-align: center;
        }
        .success-message {
            color: #23d160;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            height: 50px;
            border-radius: 5px;
        }
        .btn-register {
            background-color: var(--accent-color);
            color: white;
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 15px;
        }
        .btn-register:hover {
            background-color: color-mix(in srgb, var(--accent-color), transparent 15%);
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-form">
            <div class="register-logo">
                <h1>ShareMyRide</h1>
                <p>Créer un compte</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="success-message"><?= $success ?></div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nom">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="prenom">Prénom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn btn-register">S'inscrire</button>
            </form>
            
            <div class="login-link">
                <p>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>
            </div>
        </div>
    </div>
    
    <!-- Vendor JS Files -->
    <script src="front-office/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS File -->
    <script src="front-office/assets/js/main.js"></script>
</body>

</html> 
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

// Traitement du formulaire de connexion
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        require_once('../controller/userC.php');
        $userC = new userC();
        
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];
        
        if ($userC->login($email, $password)) {
            // Redirection gérée dans la méthode login
        } else {
            $error = "Email ou mot de passe incorrect";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Connexion - ShareMyRide</title>
    
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
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }
        .login-form {
            background-color: var(--surface-color);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo h1 {
            font-size: 32px;
            margin: 0;
        }
        .error-message {
            color: #ff3860;
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
        .btn-login {
            background-color: var(--accent-color);
            color: white;
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 15px;
        }
        .btn-login:hover {
            background-color: color-mix(in srgb, var(--accent-color), transparent 15%);
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-form">
            <div class="login-logo">
                <h1>ShareMyRide</h1>
                <p>Connexion à votre compte</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-login">Se connecter</button>
            </form>
            
            <div class="register-link">
                <p>Vous n'avez pas de compte ? <a href="register.php">S'inscrire</a></p>
            </div>
        </div>
    </div>
    
    <!-- Vendor JS Files -->
    <script src="front-office/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS File -->
    <script src="front-office/assets/js/main.js"></script>
</body>

</html> 
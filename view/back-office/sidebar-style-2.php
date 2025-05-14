<?php
// Démarrage de la session
if (!isset($_SESSION)) {
    session_start();
}

// Vérification que l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back Office</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <div class="sidebar-header">
                    <h3>Back Office</h3>
                </div>
                <ul class="sidebar-menu">
                    <li><a href="index.php"><i class="fas fa-home"></i> Accueil</a></li>
                    <li><a href="products.php"><i class="fas fa-box"></i> Produits</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
                    <li><a href="customers.php"><i class="fas fa-users"></i> Clients</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Se déconnecter</a></li>
                </ul>
            </div>
            <div class="col-md-10 content">
                <div class="content-header">
                    <h1>Accueil</h1>
                </div>
                <div class="content-body">
                    <p>Bienvenue dans le back office de votre boutique en ligne.</p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 
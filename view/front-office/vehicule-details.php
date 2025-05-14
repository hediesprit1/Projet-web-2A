<?php
// Démarrage de la session
if (!isset($_SESSION)) {
    session_start();
}

include('../../controller/vehiculeC.php');
$vehiculeC = new vehiculeC();

// Récupérer l'ID du véhicule depuis l'URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Si aucun ID n'est fourni, rediriger vers la page d'accueil
if ($id === 0) {
    header('Location: index.php');
    exit;
}

// Récupérer les détails du véhicule
$vehicule = $vehiculeC->findOne($id);

// Si le véhicule n'existe pas, rediriger vers la page d'accueil
if (!$vehicule) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= htmlspecialchars($vehicule['marque']) ?> <?= htmlspecialchars($vehicule['modele']) ?> - ShareMyRide</title>

  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
  
  <style>
    .vehicle-detail-image {
      height: 400px;
      background-color: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      border-radius: 10px;
      margin-bottom: 30px;
    }
    
    .vehicle-detail-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .vehicle-info {
      background-color: #fff;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    
    .vehicle-title {
      margin-bottom: 30px;
    }
    
    .vehicle-brand {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 5px;
    }
    
    .vehicle-model {
      font-size: 1.8rem;
      color: #6c757d;
    }
    
    .vehicle-specs {
      display: flex;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }
    
    .vehicle-spec {
      display: flex;
      align-items: center;
      margin-right: 30px;
      margin-bottom: 15px;
      font-size: 1.1rem;
      color: #333;
    }
    
    .vehicle-spec i {
      margin-right: 8px;
      color: var(--accent-color);
      font-size: 1.3rem;
    }
    
    .vehicle-type-badge {
      display: inline-block;
      padding: 8px 20px;
      background-color: var(--accent-color);
      color: white;
      border-radius: 50px;
      font-size: 1rem;
      margin-bottom: 20px;
    }
    
    .qr-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-top: 10px;
    }
  </style>
</head>

<body>

  <!-- Header -->
  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">
      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <h1 class="sitename">ShareMyRide</h1>
      </a>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php">Accueil</a></li>
          <li><a href="index.php#vehicles" class="active">Véhicules</a></li>
          <li><a href="index.php#about">À propos</a></li>
          <li><a href="index.php#services">Services</a></li>
          <li><a href="index.php#contact">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
      <?php if (isset($_SESSION['user_id'])): ?>
        <div class="dropdown">
          <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <?= htmlspecialchars($_SESSION['user_prenom']) ?>
          </a>
          <ul class="dropdown-menu" aria-labelledby="userDropdown">
            <?php if ($_SESSION['user_role'] == 'admin'): ?>
              <li><a class="dropdown-item" href="../back-office/index.php">Administration</a></li>
            <?php endif; ?>
            <li><a class="dropdown-item" href="#">Mon profil</a></li>
            <li><a class="dropdown-item" href="../logout.php">Déconnexion</a></li>
          </ul>
        </div>
      <?php else: ?>
        <a class="btn-getstarted" href="../login.php">Connexion</a>
      <?php endif; ?>
    </div>
  </header>

  <!-- Main -->
  <main class="main">
    <section class="section">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
            <div class="vehicle-detail-image">
              <?php if (!empty($vehicule['image'])): ?>
                <img src="../../<?= htmlspecialchars($vehicule['image']) ?>" alt="<?= htmlspecialchars($vehicule['marque']) ?> <?= htmlspecialchars($vehicule['modele']) ?>">
              <?php else: ?>
                <img src="assets/img/car-default.jpg" alt="<?= htmlspecialchars($vehicule['marque']) ?> <?= htmlspecialchars($vehicule['modele']) ?>">
              <?php endif; ?>
            </div>
            
            <div class="vehicle-info">
              <div class="vehicle-title">
                <h2 class="vehicle-brand"><?= htmlspecialchars($vehicule['marque']) ?></h2>
                <h3 class="vehicle-model"><?= htmlspecialchars($vehicule['modele']) ?></h3>
              </div>
              
              <?php if (!empty($vehicule['type'])): ?>
                <div class="vehicle-type-badge">
                  <?= htmlspecialchars($vehicule['type']) ?>
                </div>
              <?php endif; ?>
              
              <div class="vehicle-specs">
                <div class="vehicle-spec">
                  <i class="bi bi-palette"></i>
                  <span><strong>Couleur:</strong> <?= htmlspecialchars($vehicule['couleur']) ?></span>
                </div>
                
                <?php if (!empty($vehicule['capacite'])): ?>
                  <div class="vehicle-spec">
                    <i class="bi bi-person"></i>
                    <span><strong>Capacité:</strong> <?= htmlspecialchars($vehicule['capacite']) ?> places</span>
                  </div>
                <?php endif; ?>
                
                <?php if (!empty($vehicule['categorie'])): ?>
                  <div class="vehicle-spec">
                    <i class="bi bi-tag"></i>
                    <span><strong>Catégorie:</strong> <?= htmlspecialchars($vehicule['categorie']) ?></span>
                  </div>
                <?php endif; ?>
                
                <div class="vehicle-spec">
                  <i class="bi bi-card-text"></i>
                  <span><strong>Matricule:</strong> <?= htmlspecialchars($vehicule['matricule']) ?></span>
                </div>
              </div>
              
              <div class="d-flex gap-2 mt-3">
                <button class="btn btn-outline-primary qr-btn" onclick="openQRCode(event, <?= $vehicule['id'] ?>)">
                  <i class="bi bi-qr-code"></i> Générer QR Code
                </button>
                <button class="btn btn-outline-success" onclick="openPrintPage(event, <?= $vehicule['id'] ?>)">
                  <i class="bi bi-printer"></i> Imprimer
                </button>
              </div>
            </div>
          </div>
          
          <div class="col-lg-4">
            <div class="vehicle-info">
              <h4 class="mb-4">Réserver ce véhicule</h4>
              <p>Ce véhicule est disponible à la location. Contactez-nous pour plus d'informations ou pour réserver.</p>
              <a href="#" class="btn btn-primary w-100 mb-3">Réserver maintenant</a>
              <a href="index.php#vehicles" class="btn btn-outline-secondary w-100">Retour aux véhicules</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer id="footer" class="footer light-background">
    <div class="container text-center mt-4">
      <p>© Copyright <strong class="px-1 sitename">ShareMyRide</strong>. Tous droits réservés.</p>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>
  
  <script>
    // Fonction pour ouvrir le QR code dans une popup
    function openQRCode(event, vehiculeId) {
      event.preventDefault(); // Empêcher le comportement par défaut du bouton
      
      // Vérifier si c'est un appareil mobile
      const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
      
      if (isMobile) {
        // Sur mobile, ouvrir dans un nouvel onglet
        window.open('qr-code.php?id=' + vehiculeId, '_blank');
      } else {
        // Sur desktop, ouvrir dans une popup
        const width = 500;
        const height = 600;
        const left = (window.innerWidth - width) / 2;
        const top = (window.innerHeight - height) / 2;
        
        window.open(
          'qr-code.php?id=' + vehiculeId, 
          'QRCode', 
          `width=${width},height=${height},top=${top},left=${left},resizable=yes,scrollbars=yes,status=yes`
        );
      }
      
      return false; // Empêcher la propagation de l'événement
    }
    
    // Fonction pour ouvrir la page d'impression
    function openPrintPage(event, vehiculeId) {
      event.preventDefault();
      window.open('print-vehicule.php?id=' + vehiculeId, '_blank');
      return false;
    }
  </script>

</body>

</html> 
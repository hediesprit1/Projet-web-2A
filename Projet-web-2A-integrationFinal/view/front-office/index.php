<?php
// Démarrage de la session
if (!isset($_SESSION)) {
  session_start();
}

include('../../controller/vehiculeC.php');
$vehiculeC = new vehiculeC();

// Vérifier si c'est une requête AJAX
$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == 1;

// Recherche/filtre
$searchMarque = isset($_GET['searchMarque']) ? trim($_GET['searchMarque']) : '';
$filterCouleur = isset($_GET['filterCouleur']) ? trim($_GET['filterCouleur']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$vehiclesPerPage = 3; // Nombre de véhicules par page

// Récupérer toutes les couleurs distinctes pour le filtre
$db = config::getConnexion();
$couleurs = $db->query('SELECT DISTINCT couleur FROM vehicule')->fetchAll();

// Construire la requête filtrée pour compter le total
$countSql = "SELECT COUNT(*) as total
        FROM vehicule
        LEFT JOIN typevehicule ON vehicule.typevehicule_id = typevehicule.id WHERE 1";
if ($searchMarque !== '') {
  $countSql .= " AND vehicule.marque LIKE ?";
}
if ($filterCouleur !== '') {
  $countSql .= " AND vehicule.couleur = ?";
}
$countQuery = $db->prepare($countSql);

// Lier les paramètres pour la requête de comptage
$paramIndex = 1;
if ($searchMarque !== '') {
  $countQuery->bindValue($paramIndex++, "%$searchMarque%", PDO::PARAM_STR);
}
if ($filterCouleur !== '') {
  $countQuery->bindValue($paramIndex++, $filterCouleur, PDO::PARAM_STR);
}

$countQuery->execute();
$totalCount = $countQuery->fetch()['total'];

// Calcul des pages
$totalPages = ceil($totalCount / $vehiclesPerPage);
$page = max(1, min($page, $totalPages)); // Assurer que la page est valide
$offset = ($page - 1) * $vehiclesPerPage;

// Construire la requête filtrée avec pagination
$sql = "SELECT vehicule.*, typevehicule.type, typevehicule.capacite, typevehicule.categorie
        FROM vehicule
        LEFT JOIN typevehicule ON vehicule.typevehicule_id = typevehicule.id WHERE 1";

if ($searchMarque !== '') {
  $sql .= " AND vehicule.marque LIKE ?";
}
if ($filterCouleur !== '') {
  $sql .= " AND vehicule.couleur = ?";
}
$sql .= " ORDER BY vehicule.id DESC LIMIT ?, ?";
$query = $db->prepare($sql);

// Lier tous les paramètres explicitement
$paramIndex = 1;
if ($searchMarque !== '') {
  $query->bindValue($paramIndex++, "%$searchMarque%", PDO::PARAM_STR);
}
if ($filterCouleur !== '') {
  $query->bindValue($paramIndex++, $filterCouleur, PDO::PARAM_STR);
}
// Utiliser bindValue avec le bon type pour les paramètres de pagination
$query->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
$query->bindValue($paramIndex, $vehiclesPerPage, PDO::PARAM_INT);

// Exécution de la requête
$query->execute();
$vehicules = $query;
$totalVehicules = $vehiculeC->countVehicules();

// Si c'est une requête AJAX, renvoyer seulement le HTML des véhicules et de la pagination
if ($isAjax) {
  ob_start();
  if ($vehicules && $vehicules->rowCount() > 0):
    foreach ($vehicules as $vehicule): ?>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="card vehicle-card">
          <div class="vehicle-image">
            <?php if (!empty($vehicule['image'])): ?>
              <img src="../../<?= htmlspecialchars($vehicule['image']) ?>" alt="<?= htmlspecialchars($vehicule['marque']) ?> <?= htmlspecialchars($vehicule['modele']) ?>">
            <?php else: ?>
              <img src="assets/img/car-default.jpg" alt="<?= htmlspecialchars($vehicule['marque']) ?> <?= htmlspecialchars($vehicule['modele']) ?>">
            <?php endif; ?>
            <a href="javascript:void(0);" class="qr-icon" onclick="openQRCode(event, <?= $vehicule['id'] ?>)" title="Afficher le QR Code">
              <i class="bi bi-qr-code"></i>
            </a>
          </div>
          <a href="vehicule-details.php?id=<?= $vehicule['id'] ?>" class="card-link">
            <div class="card-body">
              <h3 class="vehicle-brand"><?= htmlspecialchars($vehicule['marque']) ?></h3>
              <h4 class="vehicle-model"><?= htmlspecialchars($vehicule['modele']) ?></h4>

              <?php if (!empty($vehicule['type'])): ?>
                <div class="vehicle-type">
                  <?= htmlspecialchars($vehicule['type']) ?>
                </div>
              <?php endif; ?>

              <div class="vehicle-details">
                <div class="vehicle-detail">
                  <i class="bi bi-palette"></i>
                  <?= htmlspecialchars($vehicule['couleur']) ?>
                </div>

                <?php if (!empty($vehicule['capacite'])): ?>
                  <div class="vehicle-detail">
                    <i class="bi bi-person"></i>
                    <?= htmlspecialchars($vehicule['capacite']) ?> places
                  </div>
                <?php endif; ?>

                <?php if (!empty($vehicule['categorie'])): ?>
                  <div class="vehicle-detail">
                    <i class="bi bi-tag"></i>
                    <?= htmlspecialchars($vehicule['categorie']) ?>
                  </div>
                <?php endif; ?>
              </div>

              <p class="vehicle-matricule">
                <i class="bi bi-card-text"></i>
                Matricule: <?= htmlspecialchars($vehicule['matricule']) ?>
              </p>
            </div>
          </a>
        </div>
      </div>
    <?php endforeach;
  else: ?>
    <div class="col-12 text-center">
      <p>Aucun véhicule disponible pour le moment.</p>
    </div>
  <?php endif; ?>

  <!-- Pagination dans la réponse AJAX -->
  <div class="col-12">
    <nav aria-label="Pagination des véhicules" class="mt-4">
      <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
          <li class="page-item">
            <a class="page-link" href="javascript:void(0)" onclick="loadPage(<?= $page - 1 ?>)">
              <i class="bi bi-chevron-left"></i>
            </a>
          </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= $i === $page ? 'active' : '' ?>">
            <a class="page-link" href="javascript:void(0)" onclick="loadPage(<?= $i ?>)"><?= $i ?></a>
          </li>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
          <li class="page-item">
            <a class="page-link" href="javascript:void(0)" onclick="loadPage(<?= $page + 1 ?>)">
              <i class="bi bi-chevron-right"></i>
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
<?php
  echo ob_get_clean();
  exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>ShareMyRide - Location de véhicules</title>

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
    .vehicle-card {
      height: 100%;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border: none;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      position: relative;
    }

    .vehicle-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .vehicle-image {
      height: 200px;
      background-color: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      position: relative;
    }

    .vehicle-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .vehicle-card:hover .vehicle-image img {
      transform: scale(1.05);
    }

    .card-body {
      padding: 1.5rem;
    }

    .vehicle-brand {
      font-size: 1.6rem;
      font-weight: 700;
      color: var(--heading-color);
      margin-bottom: 0.5rem;
    }

    .vehicle-model {
      font-size: 1.1rem;
      color: #6c757d;
      margin-bottom: 1rem;
    }

    .vehicle-details {
      display: flex;
      flex-wrap: wrap;
      margin-bottom: 1rem;
    }

    .vehicle-detail {
      display: flex;
      align-items: center;
      margin-right: 1rem;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
      color: #6c757d;
    }

    .vehicle-detail i {
      margin-right: 0.25rem;
      color: var(--accent-color);
    }

    .vehicle-type {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      background-color: var(--accent-color);
      color: white;
      border-radius: 50px;
      font-size: 0.8rem;
      margin-bottom: 1rem;
    }

    .vehicle-matricule {
      font-size: 0.9rem;
      color: #6c757d;
      margin-bottom: 0;
    }

    .section-counter {
      padding: 50px 0;
      background-color: var(--accent-color);
      color: white;
      margin-bottom: 50px;
    }

    .counter-box {
      text-align: center;
      padding: 20px;
    }

    .counter-number {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .counter-text {
      font-size: 1.1rem;
      opacity: 0.9;
    }

    .section-title h2 {
      position: relative;
      padding-bottom: 15px;
    }

    .section-title h2:after {
      content: '';
      position: absolute;
      display: block;
      width: 50px;
      height: 3px;
      background: var(--accent-color);
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
    }

    .card-link {
      text-decoration: none;
      color: inherit;
      display: block;
    }

    .qr-icon {
      position: absolute;
      top: 10px;
      right: 10px;
      background-color: rgba(255, 255, 255, 0.9);
      color: var(--accent-color);
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 10;
      transition: all 0.3s ease;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .qr-icon:hover {
      transform: scale(1.1);
      background-color: var(--accent-color);
      color: white;
    }

    .qr-icon i {
      font-size: 1.3rem;
    }
  </style>
</head>

<body class="index-page">

  <!-- Header -->
  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">
      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <h1 class="sitename">ShareMyRide</h1>
      </a>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php" class="active">Home<br></a></li>
          <li><a href="index.php#vehicles">Vehicules</a></li>
          <li><a href="blogs.php">Blogs</a></li>
          <li><a href="offre.php">Offres</a></li>
          <li><a href="myReservations.php">Reservation</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
      <?php if (isset($_SESSION['user_id'])): ?>
        <div class="dropdown" style="margin-left: auto;">
          <a class="btn btn-light p-0 border-0" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle" style="font-size: 1.5rem; color: #007bff;"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <?php if ($_SESSION['user_role'] == 'admin'): ?>
              <li><a class="dropdown-item" href="../back-office/index.php"><i class="bi bi-gear"></i> Administration</a></li>
            <?php endif; ?>
            <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Mon profil</a></li>
            <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Déconnexion</a></li>
          </ul>
        </div>
      <?php else: ?>
        <a class="btn-getstarted" href="../login.php">Connexion</a>
      <?php endif; ?>
    </div>
  </header>

  <!-- Main -->
  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero d-flex align-items-center text-center">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 mx-auto" data-aos="fade-up">
            <h2>Bienvenue sur ShareMyRide</h2>
            <p>La solution de partage de véhicules qui simplifie vos déplacements</p>
            <a href="#vehicles" class="btn-get-started">Découvrir nos véhicules</a>
          </div>
        </div>
      </div>
    </section><!-- End Hero Section -->

    <!-- Counter Section -->
    <section class="section-counter">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <div class="counter-box" data-aos="fade-up">
              <div class="counter-number"><?= $totalVehicules ?></div>
              <div class="counter-text">Véhicules disponibles</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="counter-box" data-aos="fade-up" data-aos-delay="100">
              <div class="counter-number">24/7</div>
              <div class="counter-text">Service client</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="counter-box" data-aos="fade-up" data-aos-delay="200">
              <div class="counter-number">100%</div>
              <div class="counter-text">Satisfaction client</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Vehicles Section -->
    <section id="vehicles" class="section light-background">
      <div class="container section-title" data-aos="fade-up">
        <h2>Nos véhicules</h2>
        <p>Découvrez notre flotte de véhicules disponibles à la location</p>
      </div>

      <div class="container">
        <!-- Recherche et filtres -->
        <div class="row mb-4">
          <div class="col-md-6 mb-3">
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
              <input type="text" class="form-control" id="searchMarque" placeholder="Rechercher par marque..." value="<?= htmlspecialchars($searchMarque) ?>">
              <button type="button" class="btn btn-primary" id="searchBtn">Rechercher</button>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-palette"></i></span>
              <select class="form-select" id="filterCouleur">
                <option value="">Filtrer par couleur</option>
                <?php foreach ($couleurs as $couleur): ?>
                  <option value="<?= htmlspecialchars($couleur['couleur']) ?>" <?= $filterCouleur === $couleur['couleur'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($couleur['couleur']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <button type="button" class="btn btn-primary" id="filterBtn">Filtrer</button>
            </div>
          </div>
          <div class="col-md-2 mb-3">
            <button id="resetBtn" class="btn btn-outline-secondary w-100" <?= (empty($searchMarque) && empty($filterCouleur)) ? 'style="display: none;"' : '' ?>>
              <i class="bi bi-x-circle"></i> Réinitialiser
            </button>
          </div>
        </div>

        <div class="row gy-4" id="vehicles-container">
          <?php if ($vehicules && $vehicules->rowCount() > 0): ?>
            <?php foreach ($vehicules as $vehicule): ?>
              <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card vehicle-card">
                  <div class="vehicle-image">
                    <?php if (!empty($vehicule['image'])): ?>
                      <img src="../../<?= htmlspecialchars($vehicule['image']) ?>" alt="<?= htmlspecialchars($vehicule['marque']) ?> <?= htmlspecialchars($vehicule['modele']) ?>">
                    <?php else: ?>
                      <img src="assets/img/car-default.jpg" alt="<?= htmlspecialchars($vehicule['marque']) ?> <?= htmlspecialchars($vehicule['modele']) ?>">
                    <?php endif; ?>
                    <a href="javascript:void(0);" class="qr-icon" onclick="openQRCode(event, <?= $vehicule['id'] ?>)" title="Afficher le QR Code">
                      <i class="bi bi-qr-code"></i>
                    </a>
                  </div>
                  <a href="vehicule-details.php?id=<?= $vehicule['id'] ?>" class="card-link">
                    <div class="card-body">
                      <h3 class="vehicle-brand"><?= htmlspecialchars($vehicule['marque']) ?></h3>
                      <h4 class="vehicle-model"><?= htmlspecialchars($vehicule['modele']) ?></h4>

                      <?php if (!empty($vehicule['type'])): ?>
                        <div class="vehicle-type">
                          <?= htmlspecialchars($vehicule['type']) ?>
                        </div>
                      <?php endif; ?>

                      <div class="vehicle-details">
                        <div class="vehicle-detail">
                          <i class="bi bi-palette"></i>
                          <?= htmlspecialchars($vehicule['couleur']) ?>
                        </div>

                        <?php if (!empty($vehicule['capacite'])): ?>
                          <div class="vehicle-detail">
                            <i class="bi bi-person"></i>
                            <?= htmlspecialchars($vehicule['capacite']) ?> places
                          </div>
                        <?php endif; ?>

                        <?php if (!empty($vehicule['categorie'])): ?>
                          <div class="vehicle-detail">
                            <i class="bi bi-tag"></i>
                            <?= htmlspecialchars($vehicule['categorie']) ?>
                          </div>
                        <?php endif; ?>
                      </div>

                      <p class="vehicle-matricule">
                        <i class="bi bi-card-text"></i>
                        Matricule: <?= htmlspecialchars($vehicule['matricule']) ?>
                      </p>
                    </div>
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-12 text-center">
              <p>Aucun véhicule disponible pour le moment.</p>
            </div>
          <?php endif; ?>

          <!-- Pagination -->
          <div class="col-12">
            <nav aria-label="Pagination des véhicules" class="mt-4">
              <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                  <li class="page-item">
                    <a class="page-link" href="javascript:void(0)" onclick="loadPage(<?= $page - 1 ?>)">
                      <i class="bi bi-chevron-left"></i>
                    </a>
                  </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                  <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="javascript:void(0)" onclick="loadPage(<?= $i ?>)"><?= $i ?></a>
                  </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                  <li class="page-item">
                    <a class="page-link" href="javascript:void(0)" onclick="loadPage(<?= $page + 1 ?>)">
                      <i class="bi bi-chevron-right"></i>
                    </a>
                  </li>
                <?php endif; ?>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- Footer -->
  <footer id="footer" class="footer light-background">
    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-5 col-md-12 footer-about">
          <a href="index.php" class="logo d-flex align-items-center">
            <span class="sitename">ShareMyRide</span>
          </a>
          <p>ShareMyRide - Solution de mobilité moderne.</p>
          <div class="social-links d-flex mt-4">
            <a href="#"><i class="bi bi-twitter-x"></i></a>
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-instagram"></i></a>
            <a href="#"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-6 footer-links">
          <h4>Liens utiles</h4>
          <ul>
            <li><a href="#">Accueil</a></li>
            <li><a href="#">À propos</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Mentions légales</a></li>
            <li><a href="#">Confidentialité</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-6 footer-links">
          <h4>Nos Services</h4>
          <ul>
            <li><a href="#">Location</a></li>
            <li><a href="#">Réservation</a></li>
            <li><a href="#">Assistance</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
          <h4>Nous contacter</h4>
          <p>A108 Rue de la Mobilité</p>
          <p>Paris, France</p>
          <p><strong>Téléphone:</strong> +33 6 12 34 56 78</p>
          <p><strong>Email:</strong> contact@sharemyride.fr</p>
        </div>
      </div>
    </div>

    <div class="container text-center mt-4">
      <p>© Copyright <strong class="px-1 sitename">ShareMyRide</strong>. Tous droits réservés.</p>
      <div class="credits">Design basé sur <a href="https://bootstrapmade.com/">BootstrapMade</a></div>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
    // Fonction pour ouvrir le QR code dans une popup
    function openQRCode(event, vehiculeId) {
      event.preventDefault(); // Empêcher le comportement par défaut du lien

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

    // Fonctions pour la recherche et le filtrage AJAX avec pagination
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('searchMarque');
      const filterSelect = document.getElementById('filterCouleur');
      const searchBtn = document.getElementById('searchBtn');
      const filterBtn = document.getElementById('filterBtn');
      const resetBtn = document.getElementById('resetBtn');
      const vehiclesContainer = document.getElementById('vehicles-container');

      let currentPage = <?= $page ?>;

      // Fonction pour charger une page spécifique
      window.loadPage = function(page) {
        currentPage = page;
        loadVehicles();
      }

      // Fonction pour charger les véhicules via AJAX
      function loadVehicles() {
        const searchValue = searchInput.value.trim();
        const filterValue = filterSelect.value;

        // Afficher ou masquer le bouton de réinitialisation
        resetBtn.style.display = (searchValue || filterValue) ? 'block' : 'none';

        // Mise à jour de l'URL sans recharger la page
        const url = new URL(window.location.href);
        url.searchParams.set('searchMarque', searchValue);
        url.searchParams.set('filterCouleur', filterValue);
        url.searchParams.set('page', currentPage);
        window.history.pushState({}, '', url);

        // Afficher un indicateur de chargement
        vehiclesContainer.innerHTML = '<div class="col-12 text-center"><p>Chargement...</p></div>';

        // Faire la requête AJAX
        fetch(`index.php?searchMarque=${encodeURIComponent(searchValue)}&filterCouleur=${encodeURIComponent(filterValue)}&page=${currentPage}&ajax=1`)
          .then(response => response.text())
          .then(html => {
            vehiclesContainer.innerHTML = html;
            // Réinitialiser AOS pour les nouveaux éléments
            if (typeof AOS !== 'undefined') {
              AOS.refresh();
            }
            // Défiler vers le haut de la section
            document.getElementById('vehicles').scrollIntoView({
              behavior: 'smooth'
            });
          })
          .catch(error => {
            console.error('Erreur lors du chargement des véhicules:', error);
            vehiclesContainer.innerHTML = '<div class="col-12 text-center"><p>Une erreur est survenue. Veuillez réessayer.</p></div>';
          });
      }

      // Événements pour les boutons
      searchBtn.addEventListener('click', function() {
        currentPage = 1; // Revenir à la première page lors d'une nouvelle recherche
        loadVehicles();
      });

      filterBtn.addEventListener('click', function() {
        currentPage = 1; // Revenir à la première page lors d'un nouveau filtrage
        loadVehicles();
      });

      // Événement pour la touche Entrée dans le champ de recherche
      searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          currentPage = 1; // Revenir à la première page
          loadVehicles();
        }
      });

      // Réinitialisation des filtres
      resetBtn.addEventListener('click', function() {
        searchInput.value = '';
        filterSelect.value = '';
        currentPage = 1; // Revenir à la première page
        loadVehicles();
      });
    });
  </script>

</body>

</html>
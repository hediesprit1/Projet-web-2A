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

include('../../controller/typeVehiculeC.php');

$typeVehiculeC = new typeVehiculeC();

// Vérifier si c'est une requête AJAX
$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == 1;

if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $typeVehiculeC->delete($id);
  // Si c'est une suppression, rediriger vers la même page sans le paramètre delete
  if (!$isAjax) {
    header('Location: types.php');
    exit;
  }
}

// Récupérer les paramètres de recherche et filtre
$searchCategorie = isset($_GET['searchCategorie']) ? trim($_GET['searchCategorie']) : '';
$filterCapacite = isset($_GET['filterCapacite']) ? intval($_GET['filterCapacite']) : 0;

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$typesPerPage = 5; // Nombre de types par page

// Récupérer tous les types de véhicule
$db = config::getConnexion();

// Construire la requête pour compter le total avec les filtres
$countSql = "SELECT COUNT(*) as total FROM typevehicule WHERE 1";
if ($searchCategorie !== '') {
    $countSql .= " AND categorie LIKE :categorie";
}
if ($filterCapacite > 0) {
    $countSql .= " AND capacite = :capacite";
}
$countQuery = $db->prepare($countSql);

// Lier les paramètres pour la requête de comptage
if ($searchCategorie !== '') {
    $countQuery->bindValue(':categorie', "%$searchCategorie%", PDO::PARAM_STR);
}
if ($filterCapacite > 0) {
    $countQuery->bindValue(':capacite', $filterCapacite, PDO::PARAM_INT);
}

$countQuery->execute();
$totalCount = $countQuery->fetch()['total'];

// Calcul des pages
$totalPages = ceil($totalCount / $typesPerPage);
$page = max(1, min($page, $totalPages > 0 ? $totalPages : 1)); // Assurer que la page est valide
$offset = ($page - 1) * $typesPerPage;

// Construire la requête avec pagination et filtres
$sql = "SELECT * FROM typevehicule WHERE 1";
if ($searchCategorie !== '') {
    $sql .= " AND categorie LIKE :categorie";
}
if ($filterCapacite > 0) {
    $sql .= " AND capacite = :capacite";
}
$sql .= " ORDER BY id LIMIT :offset, :limit";
$query = $db->prepare($sql);

// Lier les paramètres pour la requête principale
if ($searchCategorie !== '') {
    $query->bindValue(':categorie', "%$searchCategorie%", PDO::PARAM_STR);
}
if ($filterCapacite > 0) {
    $query->bindValue(':capacite', $filterCapacite, PDO::PARAM_INT);
}
$query->bindValue(':offset', $offset, PDO::PARAM_INT);
$query->bindValue(':limit', $typesPerPage, PDO::PARAM_INT);
$query->execute();
$types = $query->fetchAll();

// Récupérer toutes les capacités distinctes pour le filtre
$capacites = $db->query('SELECT DISTINCT capacite FROM typevehicule ORDER BY capacite')->fetchAll();

// Si c'est une requête AJAX, renvoyer seulement le HTML des types
if ($isAjax) {
    ob_start();
    foreach ($types as $type): ?>
      <tr>
        <td><?= htmlspecialchars($type['type']) ?></td>
        <td><?= htmlspecialchars($type['capacite']) ?></td>
        <td><?= htmlspecialchars($type['categorie']) ?></td>
        <td>
          <a href="?delete=<?= $type['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce type de véhicule ?');" class="text-danger">Supprimer</a> || 
          <a href="updateTypeVehicule.php?update=<?= $type['id'] ?>" class="text-primary">Modifier</a>
        </td>
      </tr>
    <?php endforeach; ?>
    
    <!-- Pagination -->
    <tr>
      <td colspan="4">
        <nav aria-label="Pagination des types de véhicule" class="mt-3">
          <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
            <li class="page-item">
              <a class="page-link" href="javascript:void(0)" onclick="loadPage(<?= $page - 1 ?>)">
                <i class="fa fa-chevron-left"></i>
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
                <i class="fa fa-chevron-right"></i>
              </a>
            </li>
            <?php endif; ?>
          </ul>
        </nav>
      </td>
    </tr>
    <?php
    echo ob_get_clean();
    exit;
}

$categories = $typeVehiculeC->read();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Click&Go</title>
  <meta
    content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
    name="viewport" />
  <link
    rel="icon"
    href="assets/img/kaiadmin/logo_light.svg"
    type="image/x-icon" />

  <!-- Fonts and icons -->
  <script src="assets/js/plugin/webfont/webfont.min.js"></script>
  <script>
    WebFont.load({
      google: {
        families: ["Public Sans:300,400,500,600,700"]
      },
      custom: {
        families: [
          "Font Awesome 5 Solid",
          "Font Awesome 5 Regular",
          "Font Awesome 5 Brands",
          "simple-line-icons",
        ],
        urls: ["assets/css/fonts.min.css"],
      },
      active: function() {
        sessionStorage.fonts = true;
      },
    });
  </script>

  <!-- CSS Files -->
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/css/plugins.min.css" />
  <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />

  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link rel="stylesheet" href="assets/css/demo.css" />

  <style>
    /* Style moderne pour la pagination */
    .pagination {
      display: flex;
      padding-left: 0;
      list-style: none;
      border-radius: 0.25rem;
    }
    
    .page-item:first-child .page-link {
      margin-left: 0;
      border-top-left-radius: 50%;
      border-bottom-left-radius: 50%;
    }
    
    .page-item:last-child .page-link {
      border-top-right-radius: 50%;
      border-bottom-right-radius: 50%;
    }
    
    .page-item.active .page-link {
      z-index: 3;
      color: #fff;
      background-color: #007bff;
      border-color: #007bff;
    }
    
    .page-link {
      position: relative;
      display: block;
      padding: 0.5rem 0.75rem;
      margin: 0 3px;
      line-height: 1.25;
      color: #007bff;
      text-decoration: none;
      background-color: #fff;
      border: 1px solid #dee2e6;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
    }
    
    .page-link:hover {
      z-index: 2;
      color: #0056b3;
      text-decoration: none;
      background-color: #e9ecef;
      border-color: #dee2e6;
    }
    
    .page-item.active .page-link {
      background-color: #007bff;
      border-color: #007bff;
    }
  </style>
</head>

<body>
  <div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar" data-background-color="dark">
      <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">

          <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar">
              <i class="gg-menu-right"></i>
            </button>
            <button class="btn btn-toggle sidenav-toggler">
              <i class="gg-menu-left"></i>
            </button>
          </div>
          <button class="topbar-toggler more">
            <i class="gg-more-vertical-alt"></i>
          </button>
        </div>
        <!-- End Logo Header -->
      </div>
      <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
          <ul class="nav nav-secondary">
            <li class="nav-item active">
              <a
                href="index.php">
                <p>Dashboard</p>
                <span class="caret"></span>
              </a>

            </li>
            <li class="nav-item active">
              <a
                href="vehicules.php">
                <p>Gestion des vehicules</p>
                <span class="caret"></span>
              </a>

            </li>
            <li class="nav-item active">
              <a
                href="types.php">
                <p>Gestion des type vehicule</p>
                <span class="caret"></span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!-- End Sidebar -->

    <div class="main-panel">
      <div class="main-header">
        <div class="main-header-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="index.html" class="logo">
              <img
                src="assets/img/kaiadmin/logo_light.svg"
                alt="navbar brand"
                class="navbar-brand"
                height="40" />
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt"></i>
            </button>
          </div>
          <!-- End Logo Header -->
        </div>
        <!-- Navbar Header -->
        <nav
          class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
          <div class="container-fluid">
            <nav
              class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
              <div class="input-group">
                <div class="input-group-prepend">
                  <button type="submit" class="btn btn-search pe-1">
                    <i class="fa fa-search search-icon"></i>
                  </button>
                </div>
                <input
                  type="text"
                  placeholder="Search ..."
                  class="form-control" />
              </div>
            </nav>

            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
              <li
                class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                <a
                  class="nav-link dropdown-toggle"
                  data-bs-toggle="dropdown"
                  href="#"
                  role="button"
                  aria-expanded="false"
                  aria-haspopup="true">
                  <i class="fa fa-search"></i>
                </a>
                <ul class="dropdown-menu dropdown-search animated fadeIn">
                  <form class="navbar-left navbar-form nav-search">
                    <div class="input-group">
                      <input
                        type="text"
                        placeholder="Search ..."
                        class="form-control" />
                    </div>
                  </form>
                </ul>
              </li>
              <li class="nav-item topbar-icon dropdown hidden-caret">
                <a
                  class="nav-link dropdown-toggle"
                  href="#"
                  id="messageDropdown"
                  role="button"
                  data-bs-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false">
                  <i class="fa fa-envelope"></i>
                </a>
                <ul
                  class="dropdown-menu messages-notif-box animated fadeIn"
                  aria-labelledby="messageDropdown">
                  <li>
                    <div
                      class="dropdown-title d-flex justify-content-between align-items-center">
                      Messages
                      <a href="#" class="small">Mark all as read</a>
                    </div>
                  </li>
                  <li>

                  </li>
                  <li>
                    <a class="see-all" href="javascript:void(0);">See all messages<i class="fa fa-angle-right"></i>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item topbar-icon dropdown hidden-caret">
                <a
                  class="nav-link dropdown-toggle"
                  href="#"
                  id="notifDropdown"
                  role="button"
                  data-bs-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false">
                  <i class="fa fa-bell"></i>
                  <span class="notification">4</span>
                </a>
                <ul
                  class="dropdown-menu notif-box animated fadeIn"
                  aria-labelledby="notifDropdown">




              </li>
            </ul>
          </div>
        </nav>
        <!-- End Navbar -->
      </div>

      <div class="container">
        <div class="page-inner">
          <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-5 pb-9">
            <div>
              <h3 class="fw-bold mb-3">Gestion des types de vehicule</h3>
            </div>

          </div>

          <div class="container mt-4 mb-4">
            <div class="row g-3 align-items-end">
              <div class="col-md-4">
                <label for="searchCategorie" class="form-label">Recherche par catégorie</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fa fa-search"></i></span>
                  <input type="text" class="form-control" id="searchCategorie" placeholder="Ex: Urbaine, Sport..." value="<?= htmlspecialchars($searchCategorie) ?>">
                </div>
              </div>
              <div class="col-md-4">
                <label for="filterCapacite" class="form-label">Filtrer par capacité</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fa fa-filter"></i></span>
                  <select class="form-select" id="filterCapacite">
                    <option value="0">Toutes les capacités</option>
                    <?php foreach ($capacites as $c): ?>
                      <option value="<?= htmlspecialchars($c['capacite']) ?>" <?= ($filterCapacite == $c['capacite']) ? 'selected' : '' ?>><?= htmlspecialchars($c['capacite']) ?> places</option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <button id="resetBtn" class="btn btn-outline-secondary w-100" <?= (empty($searchCategorie) && $filterCapacite == 0) ? 'style="display: none;"' : '' ?>>
                  <i class="fa fa-times-circle"></i> Réinitialiser les filtres
                </button>
              </div>
            </div>
            
            <!-- Formulaires cachés pour la soumission automatique -->
            <form id="searchCategorieForm" method="get" style="display: none;">
              <input type="hidden" name="searchCategorie" id="searchCategorieHidden">
              <?php if ($filterCapacite > 0): ?>
                <input type="hidden" name="filterCapacite" value="<?= $filterCapacite ?>">
              <?php endif; ?>
            </form>
            <form id="filterCapaciteForm" method="get" style="display: none;">
              <input type="hidden" name="filterCapacite" id="filterCapaciteHidden">
              <?php if ($searchCategorie): ?>
                <input type="hidden" name="searchCategorie" value="<?= htmlspecialchars($searchCategorie) ?>">
              <?php endif; ?>
            </form>
          </div>

          <div class="col-md-15">
            <div class="card card-round">
              <div class="card-header">
                <div class="card-head-row card-tools-still-right">
                  <div class="card-title">Liste des types de vehicule</div>
                  <div class="card-tools">
                  </div>
                </div>
              </div>
            </div>
            <table class="table mt-5">
              <thead>
                <tr>
                  <th scope="col">TYPE</th>
                  <th scope="col">CAPACITÉ</th>
                  <th scope="col">CATÉGORIE</th>
                  <th scope="col">OPTIONS</th>
                </tr>
              </thead>
              <tbody id="types-table-body">
                <?php foreach ($types as $type): ?>
                  <tr>
                    <td><?= htmlspecialchars($type['type']) ?></td>
                    <td><?= htmlspecialchars($type['capacite']) ?></td>
                    <td><?= htmlspecialchars($type['categorie']) ?></td>
                    <td>
                      <a href="?delete=<?= $type['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce type de véhicule ?');" class="text-danger">Supprimer</a> || 
                      <a href="updateTypeVehicule.php?update=<?= $type['id'] ?>" class="text-primary">Modifier</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
                
                <!-- Pagination -->
                <tr>
                  <td colspan="4">
                    <nav aria-label="Pagination des types de véhicule" class="mt-3">
                      <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                          <a class="page-link" href="javascript:void(0)" onclick="loadPage(<?= $page - 1 ?>)">
                            <i class="fa fa-chevron-left"></i>
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
                            <i class="fa fa-chevron-right"></i>
                          </a>
                        </li>
                        <?php endif; ?>
                      </ul>
                    </nav>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <a href="addtype.php" class="btn btn-success">Créer type</a>
        </div>
      </div>
    </div>

    <footer class="footer">
      <div class="container-fluid d-flex justify-content-between">
        <nav class="pull-left">
          <ul class="nav">
            <li class="nav-item">
              <a class="nav-link" href="http://www.themekita.com">
                ThemeKita
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#"> Help </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#"> Licenses </a>
            </li>
          </ul>
        </nav>
        <div class="copyright">
          2024, made with <i class="fa fa-heart heart text-danger"></i> by
          <a href="http://www.themekita.com">ThemeKita</a>
        </div>
        <div>
          Distributed by
          <a target="_blank" href="https://themewagon.com/">ThemeWagon</a>.
        </div>
      </div>
    </footer>
  </div>

  <!-- Custom template | don't include it in your project! -->
  <div class="custom-template">
    <div class="title">Settings</div>
    <div class="custom-content">
      <div class="switcher">
        <div class="switch-block">
          <h4>Logo Header</h4>
          <div class="btnSwitch">
            <button
              type="button"
              class="selected changeLogoHeaderColor"
              data-color="dark"></button>
            <button
              type="button"
              class="changeLogoHeaderColor"
              data-color="blue"></button>
            <button
              type="button"
              class="changeLogoHeaderColor"
              data-color="purple"></button>
            <button
              type="button"
              class="changeLogoHeaderColor"
              data-color="light-blue"></button>
            <button
              type="button"
              class="changeLogoHeaderColor"
              data-color="green"></button>
            <button
              type="button"
              class="changeLogoHeaderColor"
              data-color="orange"></button>
            <button
              type="button"
              class="changeLogoHeaderColor"
              data-color="red"></button>
            <button
              type="button"
              class="changeLogoHeaderColor"
              data-color="white"></button>
            <br />
            <button
              type="button"
              class="changeLogoHeaderColor"
              data-color="dark2"></button>
            <button
              type="button"
              class="changeLogoHeaderColor"
              data-color="blue2"></button>
            <button
              type="button"
              class="changeLogoHeaderColor"
              data-color="purple2"></button>
            <button
              type="button"
              class="changeLogoHeaderColor"
              data-color="light-blue2"></button>
            <button
              type="button"
              class="changeLogoHeaderColor"
              data-color="green2"></button>
            <button
              type="button"
              class="changeLogoHeaderColor"
              data-color="orange2"></button>
            <button
              type="button"
              class="changeLogoHeaderColor"
              data-color="red2"></button>
          </div>
        </div>
        <div class="switch-block">
          <h4>Navbar Header</h4>
          <div class="btnSwitch">
            <button
              type="button"
              class="changeTopBarColor"
              data-color="dark"></button>
            <button
              type="button"
              class="changeTopBarColor"
              data-color="blue"></button>
            <button
              type="button"
              class="changeTopBarColor"
              data-color="purple"></button>
            <button
              type="button"
              class="changeTopBarColor"
              data-color="light-blue"></button>
            <button
              type="button"
              class="changeTopBarColor"
              data-color="green"></button>
            <button
              type="button"
              class="changeTopBarColor"
              data-color="orange"></button>
            <button
              type="button"
              class="changeTopBarColor"
              data-color="red"></button>
            <button
              type="button"
              class="selected changeTopBarColor"
              data-color="white"></button>
            <br />
            <button
              type="button"
              class="changeTopBarColor"
              data-color="dark2"></button>
            <button
              type="button"
              class="changeTopBarColor"
              data-color="blue2"></button>
            <button
              type="button"
              class="changeTopBarColor"
              data-color="purple2"></button>
            <button
              type="button"
              class="changeTopBarColor"
              data-color="light-blue2"></button>
            <button
              type="button"
              class="changeTopBarColor"
              data-color="green2"></button>
            <button
              type="button"
              class="changeTopBarColor"
              data-color="orange2"></button>
            <button
              type="button"
              class="changeTopBarColor"
              data-color="red2"></button>
          </div>
        </div>
        <div class="switch-block">
          <h4>Sidebar</h4>
          <div class="btnSwitch">
            <button
              type="button"
              class="changeSideBarColor"
              data-color="white"></button>
            <button
              type="button"
              class="selected changeSideBarColor"
              data-color="dark"></button>
            <button
              type="button"
              class="changeSideBarColor"
              data-color="dark2"></button>
          </div>
        </div>
      </div>
    </div>
    <div class="custom-toggle">
      <i class="icon-settings"></i>
    </div>
  </div>
  <!-- End Custom template -->
  </div>
  <!--   Core JS Files   -->
  <script src="assets/js/core/jquery-3.7.1.min.js"></script>
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>

  <!-- jQuery Scrollbar -->
  <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

  <!-- Chart JS -->
  <script src="assets/js/plugin/chart.js/chart.min.js"></script>

  <!-- jQuery Sparkline -->
  <script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

  <!-- Chart Circle -->
  <script src="assets/js/plugin/chart-circle/circles.min.js"></script>

  <!-- Datatables -->
  <script src="assets/js/plugin/datatables/datatables.min.js"></script>

  <!-- Bootstrap Notify -->
  <script src="assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

  <!-- jQuery Vector Maps -->
  <script src="assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
  <script src="assets/js/plugin/jsvectormap/world.js"></script>

  <!-- Sweet Alert -->
  <script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>

  <!-- Core Scripts -->
  <script src="assets/js/kaiadmin.min.js"></script>

  <script>
    // Fonctions pour la pagination AJAX et filtrage
    document.addEventListener('DOMContentLoaded', function() {
      const tableBody = document.getElementById('types-table-body');
      const searchInput = document.getElementById('searchCategorie');
      const filterSelect = document.getElementById('filterCapacite');
      const resetBtn = document.getElementById('resetBtn');
      
      let currentPage = <?= $page ?>;
      let currentSearch = '<?= addslashes($searchCategorie) ?>';
      let currentFilter = <?= $filterCapacite ?>;
      
      // Fonction pour charger une page spécifique
      window.loadPage = function(page) {
        currentPage = page;
        loadTypes();
      }
      
      // Gérer la recherche par catégorie
      searchInput.addEventListener('input', function() {
        currentSearch = this.value;
        currentPage = 1; // Revenir à la première page
        loadTypes(); // Charger immédiatement les résultats
      });
      
      // Gérer le filtre par capacité
      filterSelect.addEventListener('change', function() {
        currentFilter = this.value;
        currentPage = 1; // Revenir à la première page
        loadTypes(); // Charger immédiatement les résultats
      });
      
      // Réinitialisation des filtres
      resetBtn.addEventListener('click', function() {
        searchInput.value = '';
        filterSelect.value = '0';
        currentSearch = '';
        currentFilter = 0;
        currentPage = 1;
        loadTypes();
      });
      
      // Fonction pour charger les types via AJAX
      function loadTypes() {
        // Mise à jour de l'URL sans recharger la page
        const url = new URL(window.location.href);
        url.searchParams.set('page', currentPage);
        
        if (currentSearch) {
          url.searchParams.set('searchCategorie', currentSearch);
        } else {
          url.searchParams.delete('searchCategorie');
        }
        
        if (currentFilter > 0) {
          url.searchParams.set('filterCapacite', currentFilter);
        } else {
          url.searchParams.delete('filterCapacite');
        }
        
        window.history.pushState({}, '', url);
        
        // Afficher ou masquer le bouton de réinitialisation
        resetBtn.style.display = (currentSearch || currentFilter > 0) ? 'block' : 'none';
        
        // Afficher un indicateur de chargement
        tableBody.innerHTML = '<tr><td colspan="4" class="text-center">Chargement...</td></tr>';
        
        // Faire la requête AJAX
        fetch(`types.php?page=${currentPage}&searchCategorie=${encodeURIComponent(currentSearch)}&filterCapacite=${currentFilter}&ajax=1`)
          .then(response => response.text())
          .then(html => {
            tableBody.innerHTML = html || '<tr><td colspan="4" class="text-center">Aucun type de véhicule trouvé</td></tr>';
          })
          .catch(error => {
            console.error('Erreur lors du chargement des types:', error);
            tableBody.innerHTML = '<tr><td colspan="4" class="text-center">Une erreur est survenue. Veuillez réessayer.</td></tr>';
          });
      }
    });
  </script>
</body>

</html>
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

include('../../controller/vehiculeC.php');

$vehiculeC = new vehiculeC();

// Vérifier si c'est une requête AJAX
$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == 1;

// Récupérer les valeurs de recherche/filtre
$searchMarque = isset($_GET['searchMarque']) ? trim($_GET['searchMarque']) : '';
$filterCouleur = isset($_GET['filterCouleur']) ? trim($_GET['filterCouleur']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$vehiclesPerPage = 3; // Nombre de véhicules par page

// Récupérer toutes les couleurs distinctes pour le filtre
$db = config::getConnexion();
$couleurs = $db->query('SELECT DISTINCT couleur FROM vehicule')->fetchAll();

// Construire la requête filtrée pour compter le total
$countSql = "SELECT COUNT(*) as total FROM vehicule LEFT JOIN typevehicule ON vehicule.typevehicule_id = typevehicule.id WHERE 1";
if ($searchMarque !== '') {
    $countSql .= " AND marque LIKE ?";
}
if ($filterCouleur !== '') {
    $countSql .= " AND couleur = ?";
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
$sql = "SELECT vehicule.*, typevehicule.type AS nom_type FROM vehicule LEFT JOIN typevehicule ON vehicule.typevehicule_id = typevehicule.id WHERE 1";
if ($searchMarque !== '') {
    $sql .= " AND marque LIKE ?";
}
if ($filterCouleur !== '') {
    $sql .= " AND couleur = ?";
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
$vehicules = $query->fetchAll();

// Statistiques véhicules ajoutés par mois
$data1 = $db->query("
    SELECT DATE_FORMAT(date_ajout, '%Y-%m') AS mois, COUNT(*) AS total
    FROM vehicule
    GROUP BY mois
    ORDER BY mois
")->fetchAll(PDO::FETCH_ASSOC);

// Statistiques véhicules ajoutés par mois et par type
$data2 = $db->query("
    SELECT DATE_FORMAT(date_ajout, '%Y-%m') AS mois, typevehicule.type, COUNT(*) AS total
    FROM vehicule
    LEFT JOIN typevehicule ON vehicule.typevehicule_id = typevehicule.id
    GROUP BY mois, typevehicule.type
    ORDER BY mois, typevehicule.type
")->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $vehiculeC->delete($id);
  // Si c'est une suppression, rediriger vers la même page sans le paramètre delete
  header('Location: vehicules.php');
  exit;
}

// Si c'est une requête AJAX, renvoyer seulement le HTML des véhicules
if ($isAjax) {
    ob_start();
    foreach ($vehicules as $v): ?>
      <tr>
        <td><?php if (!empty($v['image'])): ?><img src="../../<?= htmlspecialchars($v['image']) ?>" alt="Vehicle Image" style="width: 50px; height: 50px; object-fit: cover;"><?php else: ?><span class="text-muted">Aucune image</span><?php endif; ?></td>
        <td><?= htmlspecialchars($v['matricule']) ?></td>
        <td><?= htmlspecialchars($v['couleur']) ?></td>
        <td><?= htmlspecialchars($v['modele']) ?></td>
        <td><?= htmlspecialchars($v['marque']) ?></td>
        <td><?= htmlspecialchars($v['nom_type']) ?></td>
        <td><a href="?delete=<?= $v['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?');">Supprimer</a> || <a href="updateVehicule.php?update=<?= $v['id'] ?>">Modifier</a></td>
      </tr>
    <?php endforeach; ?>
    
    <!-- Pagination dans la réponse AJAX -->
    <tr>
      <td colspan="7">
        <nav aria-label="Pagination des véhicules" class="mt-3">
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
              <h3 class="fw-bold mb-3">Gestion des vehicules</h3>
            </div>
            <div class="ms-md-auto py-2 py-md-0">
              <a href="addvehicule.php" class="btn btn-primary mr-2">Ajouter un véhicule</a>
            </div>
          </div>

          <?php if (isset($_SESSION['email_notification'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong>Succès!</strong> Un nouvel véhicule a été ajouté et une notification par email a été envoyée à l'administrateur.
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['email_notification']); ?>
          <?php endif; ?>

          <div class="container mt-4 mb-4">
            <div class="row g-3 align-items-end">
              <div class="col-md-4">
                <label for="searchMarque" class="form-label">Recherche par marque</label>
                <div class="input-group">
                  <input type="text" class="form-control" id="searchMarque" placeholder="Ex: Kia, BMW..." value="<?= htmlspecialchars($searchMarque) ?>">
                  <button type="button" class="btn btn-primary" id="searchBtn">Rechercher</button>
                </div>
              </div>
              <div class="col-md-4">
                <label for="filterCouleur" class="form-label">Filtrer par couleur</label>
                <div class="input-group">
                  <select class="form-select" id="filterCouleur">
                    <option value="">Toutes les couleurs</option>
                    <?php foreach ($couleurs as $c): ?>
                      <option value="<?= htmlspecialchars($c['couleur']) ?>" <?= ($filterCouleur === $c['couleur']) ? 'selected' : '' ?>><?= htmlspecialchars(ucfirst($c['couleur'])) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button type="button" class="btn btn-primary" id="filterBtn">Filtrer</button>
                </div>
              </div>
              <div class="col-md-4">
                <button id="resetBtn" class="btn btn-outline-secondary w-100" <?= (empty($searchMarque) && empty($filterCouleur)) ? 'style="display: none;"' : '' ?>>
                  <i class="bi bi-x-circle"></i> Réinitialiser les filtres
                </button>
              </div>
            </div>
          </div>

          <div class="col-md-15">
            <div class="card card-round">
              <div class="card-header">
                <div class="card-head-row card-tools-still-right">
                  <div class="card-title">Liste des vehicules</div>
                  <div class="card-tools">
                  </div>
                </div>
              </div>
            </div>
            <table class="table mt-5">
              <thead>
                <tr>
                  <th scope="col">Image</th>
                  <th scope="col">Matricule</th>
                  <th scope="col">Couleur</th>
                  <th scope="col">Modèle</th>
                  <th scope="col">Marque</th>
                  <th scope="col">Type Véhicule</th>
                  <th scope="col">Options</th>
                </tr>
              </thead>
              <tbody id="vehicles-table-body">
                <?php foreach ($vehicules as $v): ?>
                  <tr>
                    <td>
                      <?php if (!empty($v['image'])): ?>
                        <img src="../../<?= htmlspecialchars($v['image']) ?>" alt="Vehicle Image" style="width: 50px; height: 50px; object-fit: cover;">
                      <?php else: ?>
                        <span class="text-muted">Aucune image</span>
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($v['matricule']) ?></td>
                    <td><?= htmlspecialchars($v['couleur']) ?></td>
                    <td><?= htmlspecialchars($v['modele']) ?></td>
                    <td><?= htmlspecialchars($v['marque']) ?></td>
                    <td><?= htmlspecialchars($v['nom_type']) ?></td>
                    <td>
                      <a href="?delete=<?= $v['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?');">Supprimer</a> ||
                      <a href="updateVehicule.php?update=<?= $v['id'] ?>">Modifier</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
                
                <!-- Pagination -->
                <tr>
                  <td colspan="7">
                    <nav aria-label="Pagination des véhicules" class="mt-3">
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
        </div>
      </div>
    </div>
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
    $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
      type: "line",
      height: "70",
      width: "100%",
      lineWidth: "2",
      lineColor: "#177dff",
      fillColor: "rgba(23, 125, 255, 0.14)",
    });

    $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
      type: "line",
      height: "70",
      width: "100%",
      lineWidth: "2",
      lineColor: "#f3545d",
      fillColor: "rgba(243, 84, 93, .14)",
    });

    $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
      type: "line",
      height: "70",
      width: "100%",
      lineWidth: "2",
      lineColor: "#ffa534",
      fillColor: "rgba(255, 165, 52, .14)",
    });

    // Fonctions pour la recherche et le filtrage AJAX avec pagination
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('searchMarque');
      const filterSelect = document.getElementById('filterCouleur');
      const searchBtn = document.getElementById('searchBtn');
      const filterBtn = document.getElementById('filterBtn');
      const resetBtn = document.getElementById('resetBtn');
      const tableBody = document.getElementById('vehicles-table-body');
      
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
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center">Chargement...</td></tr>';
        
        // Faire la requête AJAX
        fetch(`vehicules.php?searchMarque=${encodeURIComponent(searchValue)}&filterCouleur=${encodeURIComponent(filterValue)}&page=${currentPage}&ajax=1`)
          .then(response => response.text())
          .then(html => {
            tableBody.innerHTML = html || '<tr><td colspan="7" class="text-center">Aucun véhicule trouvé</td></tr>';
          })
          .catch(error => {
            console.error('Erreur lors du chargement des véhicules:', error);
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center">Une erreur est survenue. Veuillez réessayer.</td></tr>';
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

    const data1 = <?= json_encode($data1) ?>;
    const data2 = <?= json_encode($data2) ?>;
    // 1. Courbe véhicules par mois
    const ctx1 = document.getElementById('vehiculesParMois').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: data1.map(d => d.mois),
            datasets: [{
                label: 'Véhicules ajoutés',
                data: data1.map(d => d.total),
                borderColor: 'blue',
                fill: false
            }]
        }
    });
    // 2. Courbe véhicules par mois et par type
    const types = [...new Set(data2.map(d => d.type))];
    const mois = [...new Set(data2.map(d => d.mois))];
    const datasets = types.map((type, i) => ({
        label: type,
        data: mois.map(m => {
            const found = data2.find(d => d.type === type && d.mois === m);
            return found ? found.total : 0;
        }),
        fill: false,
        borderColor: `hsl(${i * 360 / types.length}, 70%, 50%)`
    }));
    const ctx2 = document.getElementById('vehiculesParMoisType').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: mois,
            datasets: datasets
        }
    });
  </script>
</body>

</html>
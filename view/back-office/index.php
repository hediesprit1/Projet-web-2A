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

// Connexion à la base
require_once('../../config.php');
$db = config::getConnexion();
// Statistiques véhicules ajoutés par mois
$data1 = $db->query("
    SELECT DATE_FORMAT(date_ajout, '%Y-%m') AS mois, COUNT(*) AS total
    FROM vehicule
    GROUP BY mois
    ORDER BY mois
")->fetchAll(PDO::FETCH_ASSOC);

// Statistiques des types de véhicules (directement depuis la table typevehicule)
$data2 = $db->query("
    SELECT t.type, t.categorie, COUNT(v.id) as nb_vehicules 
    FROM typevehicule t
    LEFT JOIN vehicule v ON t.id = v.typevehicule_id
    GROUP BY t.id
    ORDER BY t.type
")->fetchAll(PDO::FETCH_ASSOC);

// Nombre total de types de véhicules
$totalTypes = $db->query("SELECT COUNT(*) as total FROM typevehicule")->fetch()['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>ShareMyRide - Administration</title>
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
                </ul>
              </li>
              
              <!-- Ajout du menu utilisateur -->
              <li class="nav-item topbar-icon dropdown hidden-caret" style="margin-left: auto;">
                <a
                  class="nav-link dropdown-toggle"
                  href="#"
                  id="userDropdown"
                  role="button"
                  data-bs-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false">
                  <i class="fa fa-user-circle" style="font-size: 1.5rem; color: #007bff;"></i>
                </a>
                <ul
                  class="dropdown-menu dropdown-user animated fadeIn"
                  aria-labelledby="userDropdown">
                  <li>
                    <div class="dropdown-user-scroll scrollbar-outer">
                      <div class="user-box">
                        <div class="avatar-lg"><i class="fa fa-user"></i></div>
                        <div class="u-text">
                          <h4><?= htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']) ?></h4>
                          <p class="text-muted"><?= htmlspecialchars($_SESSION['user_email']) ?></p>
                          <a href="../logout.php" class="btn btn-xs btn-danger btn-sm">Déconnexion</a>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
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
              <h3 class="fw-bold mb-3">Dashboard</h3>
            </div>

          </div>

          <!-- STATISTIQUES VEHICULES - HIGHCHARTS -->
          <div class="container mt-4 mb-5">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h5 class="card-title">Statistiques Véhicules</h5>
                  </div>
                  <div class="card-body">
                    <div class="row mb-4">
                      <div class="col-md-6">
                        <div class="card bg-primary text-white">
                          <div class="card-body text-center">
                            <h1 id="total-vehicules" class="display-4"></h1>
                            <h5>Total Véhicules</h5>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="card bg-success text-white">
                          <div class="card-body text-center">
                            <h1 id="total-types" class="display-4"></h1>
                            <h5>Types de Véhicules</h5>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 mb-4">
                        <div id="chart-monthly" style="min-height: 300px;"></div>
                      </div>
                      <div class="col-md-6 mb-4">
                        <div id="chart-types" style="min-height: 300px;"></div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <table class="table table-striped" id="data-table">
                          <thead>
                            <tr>
                              <th>Mois</th>
                              <th>Nombre de véhicules</th>
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>


                  <!-- Projects table -->
                </div>
              </div>
            </div>
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

  <!-- Kaiadmin JS -->
  <script src="assets/js/kaiadmin.min.js"></script>

  <!-- Kaiadmin DEMO methods, don't include it in your project! -->
  <script src="assets/js/setting-demo.js"></script>
  <script src="assets/js/demo.js"></script>
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Convertir les données PHP pour Highcharts
      const vehiculeData = <?= json_encode($data1) ?>;
      const typeVehiculeData = <?= json_encode($data2) ?>;
      const totalTypesNumber = <?= $totalTypes ?>;

      // Préparer les données pour Highcharts
      // 1. Données véhicules par mois
      const moisLabels = vehiculeData.map(d => d.mois);
      const totalVehicules = vehiculeData.map(d => parseInt(d.total));
      const totalVehiculesSum = totalVehicules.reduce((a, b) => a + b, 0);

      // 2. Données par type de véhicule (directement de la table typevehicule)
      const typesData = typeVehiculeData.map(type => {
        return { 
          name: type.type, 
          y: parseInt(type.nb_vehicules),
          custom: { categorie: type.categorie }
        };
      });

      // Afficher les KPIs
      document.getElementById('total-vehicules').textContent = totalVehiculesSum;
      document.getElementById('total-types').textContent = totalTypesNumber;

      // Graphique mensuel
      Highcharts.chart('chart-monthly', {
        chart: {
          type: 'column'
        },
        title: {
          text: 'Véhicules ajoutés par mois'
        },
        xAxis: {
          categories: moisLabels,
          title: {
            text: 'Mois'
          }
        },
        yAxis: {
          title: {
            text: 'Nombre de véhicules'
          }
        },
        series: [{
          name: 'Véhicules',
          data: totalVehicules,
          color: '#3498db'
        }],
        credits: {
          enabled: false
        }
      });

      // Graphique par type (mise à jour)
      Highcharts.chart('chart-types', {
        chart: {
          type: 'pie'
        },
        title: {
          text: 'Répartition par type de véhicule'
        },
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
              enabled: true,
              format: '<b>{point.name}</b>: {point.percentage:.1f} %'
            },
            showInLegend: true
          }
        },
        tooltip: {
          pointFormat: '{point.name}: <b>{point.y}</b> véhicule(s)<br>Catégorie: {point.custom.categorie}'
        },
        series: [{
          name: 'Véhicules',
          colorByPoint: true,
          data: typesData
        }],
        credits: {
          enabled: false
        }
      });

      // Remplir le tableau de données
      const tableBody = document.querySelector('#data-table tbody');
      vehiculeData.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${item.mois}</td>
          <td>${item.total}</td>
        `;
        tableBody.appendChild(row);
      });
    });
  </script>
</body>

</html>
<?php
include('../../controller/reservationC.php');
$reservationC = new ReservationC();
$reservations = $reservationC->read();
if (isset($_GET['reservation_id'])) {
  $reservationC->generatePDF($_GET['reservation_id']);
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
                href="blogs.php">
                <p>Gestion des Blog</p>
                <span class="caret"></span>
              </a>

            </li>
            <li class="nav-item active">
              <a
                href="categories.php">
                <p>Gestion des Categories</p>
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
            <li class="nav-item active">
              <a
                href="offres.php">
                <p>Gestion des offres</p>
                <span class="caret"></span>
              </a>

            </li>
            <li class="nav-item active">
              <a
                href="reservations.php">
                <p>Gestion des reservations</p>
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
              <h3 class="fw-bold mb-3">Gestion des offres</h3>
            </div>

          </div>


          <div class="col-md-15">
            <div class="card card-round">
              <div class="card-header">
                <div class="card-head-row card-tools-still-right">
                  <div class="card-title">Liste des offres</div>
                  <div class="card-tools">
                  </div>
                </div>
              </div>
            </div>
            <table class="table table-bordered table-striped">
              <thead class="table-dark">
                <tr>
                  <th>#</th>
                  <th>Date de réservation</th>
                  <th>ID Offre</th>
                  <th>ID Véhicule</th>
                  <th>ID Utilisateur</th>
                  <th>Option</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($reservations)): ?>
                  <?php foreach ($reservations as $r): ?>
                    <tr>
                      <td><?= htmlspecialchars($r['id']) ?></td>
                      <td><?= htmlspecialchars($r['date_reservation']) ?></td>
                      <td><?= htmlspecialchars($r['id_offre']) ?></td>
                      <td><?= htmlspecialchars($r['id_vehicule']) ?></td>
                      <td><?= htmlspecialchars($r['user_id']) ?></td>
                      <td>
                        <!-- Updated button to trigger PDF generation using JavaScript -->
                        <button class="download-pdf" data-id="<?= htmlspecialchars($r['id']) ?>" data-date="<?= htmlspecialchars($r['date_reservation']) ?>" data-offer="<?= htmlspecialchars($r['id_offre']) ?>" data-vehicle="<?= htmlspecialchars($r['id_vehicule']) ?>" data-user="<?= htmlspecialchars($r['user_id']) ?>">Download PDF</button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center">Aucune réservation trouvée.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
              <!-- jsPDF Library -->
              <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
              <!-- jsPDF autoTable Plugin -->
              <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.10/jspdf.plugin.autotable.min.js"></script>

              <script>
                document.addEventListener('DOMContentLoaded', function() {
                  const buttons = document.querySelectorAll('.download-pdf');
                  buttons.forEach(function(button) {
                    button.addEventListener('click', function() {
                      // Get reservation data from button's data-* attributes
                      const reservationId = button.getAttribute('data-id');
                      const dateReservation = button.getAttribute('data-date');
                      const offer = button.getAttribute('data-offer');
                      const vehicle = button.getAttribute('data-vehicle');
                      const userId = button.getAttribute('data-user');

                      // Create PDF using jsPDF
                      const {
                        jsPDF
                      } = window.jspdf;

                      // Create a new jsPDF instance
                      const doc = new jsPDF();

                      // Add title
                      doc.setFontSize(18);
                      doc.text('Réservation Détails', 10, 10);

                      // Define table data
                      const reservationData = [
                        ['ID', reservationId],
                        ['Date de Réservation', dateReservation],
                        ['Offre', offer],
                        ['Véhicule', vehicle],
                        ['Utilisateur', userId]
                      ];

                      // Set table positioning
                      const startX = 10;
                      const startY = 20;
                      const rowHeight = 10;
                      const columnWidth = 50;

                      // Draw table header
                      doc.setFontSize(12);
                      doc.setFillColor(200, 220, 255); // Light blue background for header
                      doc.rect(startX, startY, columnWidth, rowHeight, 'F'); // Header background
                      doc.text('Détail', startX + 2, startY + 6); // Column Title
                      doc.rect(startX + columnWidth, startY, columnWidth, rowHeight, 'F'); // Header background
                      doc.text('Information', startX + columnWidth + 2, startY + 6); // Column Title

                      // Draw the table content
                      reservationData.forEach((row, index) => {
                        const rowY = startY + (index + 1) * rowHeight;

                        doc.rect(startX, rowY, columnWidth, rowHeight); // Left column border
                        doc.text(row[0], startX + 2, rowY + 6); // Left column text

                        doc.rect(startX + columnWidth, rowY, columnWidth, rowHeight); // Right column border
                        doc.text(row[1], startX + columnWidth + 2, rowY + 6); // Right column text
                      });

                      // Save the generated PDF
                      doc.save(`reservation_${reservationId}.pdf`);
                    });
                  });
                });
              </script>




            </table>
          </div>
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

  <!-- Kaiadmin JS -->
  <script src="assets/js/kaiadmin.min.js"></script>

  <!-- Kaiadmin DEMO methods, don't include it in your project! -->
  <script src="assets/js/setting-demo.js"></script>
  <script src="assets/js/demo.js"></script>
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
  </script>
</body>

</html>
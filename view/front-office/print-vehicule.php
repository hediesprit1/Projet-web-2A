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
  <meta charset="UTF-8">
  <title>Détails du véhicule - <?= htmlspecialchars($vehicule['marque']) ?> <?= htmlspecialchars($vehicule['modele']) ?></title>
  <style>
    body {
      font-family: 'Roboto', Arial, sans-serif;
      line-height: 1.6;
      color: #333;
      max-width: 800px;
      margin: 0 auto;
      padding: 20px;
    }
    .print-header {
      text-align: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 1px solid #ccc;
    }
    .logo {
      font-size: 28px;
      font-weight: bold;
      color: #0d6efd;
    }
    .vehicle-image {
      text-align: center;
      margin-bottom: 20px;
    }
    .vehicle-image img {
      max-width: 100%;
      max-height: 300px;
      border-radius: 8px;
    }
    .vehicle-title {
      margin-bottom: 20px;
    }
    .vehicle-brand {
      font-size: 32px;
      font-weight: bold;
      margin-bottom: 5px;
    }
    .vehicle-model {
      font-size: 24px;
      color: #666;
    }
    .vehicle-type {
      display: inline-block;
      padding: 5px 15px;
      background-color: #0d6efd;
      color: white;
      border-radius: 20px;
      font-size: 14px;
      margin-bottom: 20px;
    }
    .vehicle-details {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      margin-bottom: 30px;
    }
    .detail-item {
      padding: 10px;
      background-color: #f8f9fa;
      border-radius: 5px;
    }
    .detail-label {
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
    }
    .footer {
      margin-top: 40px;
      font-size: 12px;
      text-align: center;
      color: #666;
    }
    @media print {
      body {
        font-size: 12pt;
      }
      .print-header {
        margin-bottom: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="print-header">
    <div class="logo">ShareMyRide</div>
    <div>Fiche détaillée du véhicule</div>
  </div>
  
  <div class="vehicle-image">
    <?php if (!empty($vehicule['image'])): ?>
      <img src="../../<?= htmlspecialchars($vehicule['image']) ?>" alt="<?= htmlspecialchars($vehicule['marque']) ?> <?= htmlspecialchars($vehicule['modele']) ?>">
    <?php else: ?>
      <img src="assets/img/car-default.jpg" alt="<?= htmlspecialchars($vehicule['marque']) ?> <?= htmlspecialchars($vehicule['modele']) ?>">
    <?php endif; ?>
  </div>
  
  <div class="vehicle-title">
    <div class="vehicle-brand"><?= htmlspecialchars($vehicule['marque']) ?></div>
    <div class="vehicle-model"><?= htmlspecialchars($vehicule['modele']) ?></div>
  </div>
  
  <?php if (!empty($vehicule['type'])): ?>
    <div class="vehicle-type"><?= htmlspecialchars($vehicule['type']) ?></div>
  <?php endif; ?>
  
  <div class="vehicle-details">
    <div class="detail-item">
      <span class="detail-label">Couleur:</span>
      <?= htmlspecialchars($vehicule['couleur']) ?>
    </div>
    
    <?php if (!empty($vehicule['capacite'])): ?>
      <div class="detail-item">
        <span class="detail-label">Capacité:</span>
        <?= htmlspecialchars($vehicule['capacite']) ?> places
      </div>
    <?php endif; ?>
    
    <?php if (!empty($vehicule['categorie'])): ?>
      <div class="detail-item">
        <span class="detail-label">Catégorie:</span>
        <?= htmlspecialchars($vehicule['categorie']) ?>
      </div>
    <?php endif; ?>
    
    <div class="detail-item">
      <span class="detail-label">Matricule:</span>
      <?= htmlspecialchars($vehicule['matricule']) ?>
    </div>
  </div>
  
  <div class="footer">
    <p>Document généré le <?= date('d/m/Y') ?> à <?= date('H:i:s') ?></p>
    <p>© ShareMyRide - Tous droits réservés</p>
  </div>
  
  <script>
    // Imprimer automatiquement quand la page est chargée
    window.onload = function() {
      window.print();
    };
  </script>
</body>
</html> 
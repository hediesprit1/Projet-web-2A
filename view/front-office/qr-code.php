<?php
// Démarrage de la session
if (!isset($_SESSION)) {
    session_start();
}

// Vérification des paramètres
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('HTTP/1.0 400 Bad Request');
    echo 'ID de véhicule invalide';
    exit;
}

// Récupérer les infos du véhicule pour les afficher
include('../../controller/vehiculeC.php');
$vehiculeC = new vehiculeC();
$vehicule = $vehiculeC->findOne($id);

// Si aucun véhicule n'est trouvé avec cet ID
if (!$vehicule) {
    header('Location: index.php');
    exit;
}

// Construction de l'URL de recherche Google pour marque et modèle
$searchTerm = urlencode($vehicule['marque'] . ' ' . $vehicule['modele']);
$googleSearchUrl = "https://www.google.com/search?q=" . $searchTerm;

// Titre de la page basé sur les informations du véhicule
$pageTitle = "QR Code - " . $vehicule['marque'] . " " . $vehicule['modele'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?= $pageTitle ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
        }
        .qr-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            max-width: 100%;
            width: 400px;
        }
        .vehicle-info {
            margin-bottom: 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        h1 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #333;
        }
        canvas {
            max-width: 100%;
            height: auto !important;
            margin: 15px auto;
            display: block;
        }
        p.help-text {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 15px;
        }
        .btn-group {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .brand-model {
            font-weight: bold;
            font-size: 1.2rem;
            color: #0d6efd;
        }
        .vehicle-details {
            margin-top: 10px;
            font-size: 0.9rem;
        }
        .detail-item {
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }
        .detail-item i {
            margin-right: 8px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <div class="vehicle-info">
            <div class="brand-model">
                <i class="bi bi-car-front"></i> 
                <?= htmlspecialchars($vehicule['marque']) ?> <?= htmlspecialchars($vehicule['modele']) ?>
            </div>
            <div class="vehicle-details">
                <div class="detail-item">
                    <i class="bi bi-palette"></i> Couleur: <?= htmlspecialchars($vehicule['couleur']) ?>
                </div>
                <div class="detail-item">
                    <i class="bi bi-card-text"></i> Matricule: <?= htmlspecialchars($vehicule['matricule']) ?>
                </div>
                <?php if (!empty($vehicule['type'])): ?>
                <div class="detail-item">
                    <i class="bi bi-tag"></i> Type: <?= htmlspecialchars($vehicule['type']) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <h1>QR Code de Recherche</h1>
        
        <canvas id="qrCode"></canvas>
        
        <p class="help-text">Scannez ce code QR pour rechercher <strong><?= htmlspecialchars($vehicule['marque']) ?> <?= htmlspecialchars($vehicule['modele']) ?></strong> sur Google.</p>
        
        <div class="btn-group">
            <button class="btn btn-primary" onclick="downloadQR()">
                <i class="bi bi-download"></i> Télécharger
            </button>
            <button class="btn btn-secondary" onclick="window.close()">
                <i class="bi bi-x-circle"></i> Fermer
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Créer le QR code qui pointe vers la recherche Google
            var qr = new QRious({
                element: document.getElementById('qrCode'),
                value: '<?= $googleSearchUrl ?>',
                size: 300,
                level: 'H' // Niveau de correction d'erreur plus élevé pour une meilleure lisibilité
            });
            
            // Ajuster la taille du QR code pour les écrans mobiles
            function adjustQRSize() {
                var container = document.querySelector('.qr-container');
                var canvas = document.getElementById('qrCode');
                var maxSize = Math.min(container.clientWidth - 40, 300);
                qr.size = maxSize;
            }
            
            // Ajuster la taille au chargement et au redimensionnement
            adjustQRSize();
            window.addEventListener('resize', adjustQRSize);
        });
        
        // Fonction pour télécharger le QR code
        function downloadQR() {
            var canvas = document.getElementById('qrCode');
            var link = document.createElement('a');
            link.download = 'qrcode-recherche-<?= htmlspecialchars($vehicule['marque']) ?>-<?= htmlspecialchars($vehicule['modele']) ?>.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        }
    </script>
</body>
</html> 
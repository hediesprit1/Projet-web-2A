<?php 
include_once 'includes/header.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Create reclamation object to get statistics
$reclamation = new Reclamation($db);
$en_cours_count = $reclamation->countByStatus('en_cours');
$valider_count = $reclamation->countByStatus('valider');
$total_count = $en_cours_count + $valider_count;

// Create user object to get count
$user = new User($db);
$user_count = $user->countAll();
?>

<div class="dashboard">
    <div class="dashboard-header">
        <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
        <?php if($_SESSION['role'] == 'user'): ?>
        <a href="index.php?action=reclamation_create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle Réclamation
        </a>
        <?php endif; ?>
    </div>
    
    <div class="dashboard-cards">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Total des Réclamations</h3>
            </div>
            <div class="card-value"><?php echo $total_count; ?></div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Réclamations en cours</h3>
            </div>
            <div class="card-value"><?php echo $en_cours_count; ?></div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Réclamations validées</h3>
            </div>
            <div class="card-value"><?php echo $valider_count; ?></div>
        </div>
        
        <?php if($_SESSION['role'] == 'admin'): ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Utilisateurs</h3>
            </div>
            <div class="card-value"><?php echo $user_count; ?></div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="dashboard-recent">
        <h3><i class="fas fa-history"></i> Réclamations récentes</h3>
        
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Get recent reclamations (limited to 5)
                $stmt = $reclamation->readAll();
                $count = 0;
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if($count >= 5) break; // Limit to 5 rows
                    
                    $status_badge = ($row['status'] == 'en_cours') 
                        ? '<span class="badge badge-warning">En cours</span>' 
                        : '<span class="badge badge-success">Validée</span>';
                    
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . substr($row['description'], 0, 50) . "...</td>";
                    echo "<td>" . $status_badge . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['creation_date'])) . "</td>";
                    echo "<td class='table-actions'>";
                    echo "<a href='index.php?action=reclamation_read&id=" . $row['id'] . "' class='btn btn-primary'><i class='fas fa-eye'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                    
                    $count++;
                }
                
                if($count == 0) {
                    echo "<tr><td colspan='5' style='text-align: center;'>Aucune réclamation trouvée</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px; text-align: right;">
            <a href="index.php?action=reclamations" class="btn btn-primary">Voir toutes les réclamations</a>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?> 
<?php include_once 'includes/frontoffice_header.php'; ?>

<div class="dashboard">
    <div class="dashboard-header">
        <h2><i class="fas fa-tachometer-alt"></i> Tableau de bord</h2>
        <a href="index.php?action=frontoffice_reclamation_create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle Réclamation
        </a>
    </div>
    
    <?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; ?>
    </div>
    <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; ?>
    </div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <div class="dashboard-cards">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">Total de mes réclamations</h3>
            </div>
            <div class="card-value"><?php echo $total_count; ?></div>
        </div>
        
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h3 class="card-title">En cours</h3>
            </div>
            <div class="card-value"><?php echo $en_cours_count; ?></div>
        </div>
        
        <div class="card">
            <div class="card-header bg-success text-white">
                <h3 class="card-title">Validées</h3>
            </div>
            <div class="card-value"><?php echo $valider_count; ?></div>
        </div>
    </div>
    
    <div class="recent-section">
        <h3><i class="fas fa-history"></i> Réclamations récentes</h3>
        
        <div class="mb-3">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Information : Les réclamations avec l'état <span class="badge badge-success">Validée</span> ne peuvent pas être modifiées ou supprimées.
            </div>
        </div>
        
        <?php if(count($recent_reclamations) > 0): ?>
        <div class="table-container">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Objet</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($recent_reclamations as $row): 
                    $status_badge = ($row['status'] == 'en_cours') 
                        ? '<span class="badge badge-warning">En cours</span>' 
                        : '<span class="badge badge-success">Validée</span>';
                    
                    $row_class = ($row['status'] == 'valider') ? 'table-success' : '';
                ?>
                    <tr class="<?php echo $row_class; ?>">
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo isset($row['object']) ? substr($row['object'], 0, 30) . (strlen($row['object']) > 30 ? '...' : '') : ''; ?></td>
                        <td><?php echo $status_badge; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['creation_date'])); ?></td>
                        <td class="table-actions">
                            <a href="index.php?action=frontoffice_reclamation_read&id=<?php echo $row['id']; ?>" class="btn btn-primary action-btn" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <?php if($row['status'] == 'en_cours'): ?>
                            <a href="index.php?action=frontoffice_reclamation_update&id=<?php echo $row['id']; ?>" class="btn btn-success action-btn" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="index.php?action=frontoffice_reclamation_delete&id=<?php echo $row['id']; ?>" class="btn btn-danger action-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation?');" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php else: ?>
                            <span class="btn btn-secondary action-btn disabled" title="Réclamation validée, modification impossible">
                                <i class="fas fa-lock"></i>
                            </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Vous n'avez pas encore créé de réclamation. 
            <a href="index.php?action=frontoffice_reclamation_create">Créer votre première réclamation</a>.
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .dashboard-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .card {
        flex: 1;
        min-width: 200px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
    }
    
    .card-header {
        padding: 15px;
        font-weight: bold;
    }
    
    .card-value {
        font-size: 2.5rem;
        text-align: center;
        padding: 20px 0;
        font-weight: bold;
        color: #333;
    }
    
    .table-actions {
        display: flex;
        gap: 5px;
        justify-content: center;
    }
    
    .action-btn {
        width: 36px;
        height: 36px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s;
    }
    
    .action-btn i {
        font-size: 1rem;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .thead-dark th {
        background-color: #343a40;
        color: white;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.05);
    }
    
    .recent-section {
        margin-top: 30px;
    }
    
    .recent-section h3 {
        margin-bottom: 15px;
    }
</style>

<?php include_once 'includes/frontoffice_footer.php'; ?> 
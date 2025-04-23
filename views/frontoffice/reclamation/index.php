<?php include_once 'includes/frontoffice_header.php'; ?>

<div class="dashboard">
    <div class="dashboard-header">
        <h2><i class="fas fa-exclamation-circle"></i> Mes Réclamations</h2>
        <a href="index.php?action=frontoffice_reclamation_create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle Réclamation
        </a>
    </div>
    
    <div class="dashboard-cards">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">Total de mes réclamations</h3>
            </div>
            <div class="card-value"><?php echo count($reclamations); ?></div>
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
    
    <!-- Formulaire de recherche  -->
    <div class="search-container">
        <form action="index.php" method="GET" class="search-form">
            <input type="hidden" name="action" value="frontoffice_reclamations">
            
            <div class="form-group">
                <label for="search"><i class="fas fa-search"></i> Rechercher par objet</label>
                <input type="text" id="search" name="search" class="form-control" placeholder="Mot-clé dans l'objet de la réclamation" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="date_from"><i class="far fa-calendar-alt"></i> Date de début</label>
                    <input type="date" id="date_from" name="date_from" class="form-control" value="<?php echo isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : ''; ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="date_to"><i class="far fa-calendar-alt"></i> Date de fin</label>
                    <input type="date" id="date_to" name="date_to" class="form-control" value="<?php echo isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Rechercher</button>
                <?php if(isset($_GET['search']) || isset($_GET['date_from']) || isset($_GET['date_to'])): ?>
                    <a href="index.php?action=frontoffice_reclamations" class="btn btn-secondary"><i class="fas fa-times"></i> Réinitialiser</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
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
    
    <!-- Informations de recherche -->
    <?php if(isset($_GET['search']) || isset($_GET['date_from']) || isset($_GET['date_to'])): ?>
        <div class="search-info">
            <p>
                <i class="fas fa-filter"></i> Résultats filtrés 
                <?php if(!empty($searchTerm)): ?>
                    par objet contenant: <strong><?php echo htmlspecialchars($searchTerm); ?></strong>
                <?php endif; ?>
                
                <?php if(!empty($dateFrom) || !empty($dateTo)): ?>
                    pour la période: 
                    <?php if(!empty($dateFrom)): ?>
                        <strong>du <?php echo date('d/m/Y', strtotime($dateFrom)); ?></strong>
                    <?php endif; ?>
                    
                    <?php if(!empty($dateTo)): ?>
                        <strong>au <?php echo date('d/m/Y', strtotime($dateTo)); ?></strong>
                    <?php endif; ?>
                <?php endif; ?>
                
                (<?php echo count($reclamations); ?> résultat<?php echo count($reclamations) > 1 ? 's' : ''; ?>)
            </p>
        </div>
    <?php endif; ?>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Information : Les réclamations avec l'état <span class="badge badge-success">Validée</span> ne peuvent pas être modifiées ou supprimées.
    </div>
    
    <?php if(count($reclamations) > 0): ?>
    <div class="table-container">
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Objet</th>
                    <th>Description</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($reclamations as $row): 
                $status_badge = ($row['status'] == 'en_cours') 
                    ? '<span class="badge badge-warning">En cours</span>' 
                    : '<span class="badge badge-success">Validée</span>';
                
                $row_class = ($row['status'] == 'valider') ? 'table-success' : '';
            ?>
                <tr class="<?php echo $row_class; ?>">
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo isset($row['object']) ? substr($row['object'], 0, 30) . (strlen($row['object']) > 30 ? '...' : '') : ''; ?></td>
                    <td><?php echo substr($row['description'], 0, 50) . '...'; ?></td>
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
    
    .search-container {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .search-form {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .form-group {
        flex: 1;
        min-width: 200px;
    }
    
    .form-row {
        display: flex;
        flex-wrap: wrap;
        width: 100%;
        gap: 15px;
    }
    
    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .form-buttons {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }
    
    .btn {
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        border: none;
    }
    
    .btn-primary {
        background-color: #4a69bd;
        color: white;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }
    
    .search-info {
        background-color: #e9ecef;
        padding: 10px 15px;
        border-radius: 4px;
        margin-bottom: 15px;
        font-size: 14px;
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
    
    @media (max-width: 768px) {
        .search-form {
            flex-direction: column;
        }
        
        .form-row {
            flex-direction: column;
        }
    }
</style>

<?php include_once 'includes/frontoffice_footer.php'; ?> 
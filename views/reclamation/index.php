<?php include_once 'includes/header.php'; ?>

<div class="dashboard">
    <div class="dashboard-header">
        <h2><i class="fas fa-exclamation-circle"></i> Réclamations</h2>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
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
            <div class="card-value"><?php echo count($reclamations); ?></div>
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
    </div>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>Objet</th>
                    <th>Description</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(count($reclamations) > 0):
                foreach($reclamations as $row):
                    $status_badge = ($row['status'] == 'en_cours') 
                        ? '<span class="badge badge-warning">En cours</span>' 
                        : '<span class="badge badge-success">Validée</span>';
            ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></td>
                    <td><?php echo isset($row['object']) ? substr($row['object'], 0, 30) . (strlen($row['object']) > 30 ? '...' : '') : ''; ?></td>
                    <td><?php echo substr($row['description'], 0, 50) . '...'; ?></td>
                    <td><?php echo $status_badge; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['creation_date'])); ?></td>
                    <td class="table-actions">
                        <a href="index.php?action=reclamation_read&id=<?php echo $row['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                        
                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <a href="index.php?action=reclamation_update&id=<?php echo $row['id']; ?>" class="btn btn-success">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="index.php?action=reclamation_delete&id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation?');">
                            <i class="fas fa-trash"></i>
                        </a>
                        <?php elseif(isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
                        <a href="index.php?action=reclamation_update&id=<?php echo $row['id']; ?>" class="btn btn-success">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="index.php?action=reclamation_delete&id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation?');">
                            <i class="fas fa-trash"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php 
                endforeach;
            else:
            ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Aucune réclamation trouvée</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?> 
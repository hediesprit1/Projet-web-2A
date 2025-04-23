<?php include_once 'includes/frontoffice_header.php'; ?>

<div class="dashboard">
    <div class="dashboard-header">
        <h2><i class="fas fa-file-alt"></i> Détail de ma Réclamation</h2>
        <div>
            <a href="index.php?action=frontoffice_reclamations" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Retour à mes réclamations
            </a>
            
            <?php if($reclamation->status == 'en_cours'): ?>
            <a href="index.php?action=frontoffice_reclamation_update&id=<?php echo $reclamation->id; ?>" class="btn btn-success">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="index.php?action=frontoffice_reclamation_delete&id=<?php echo $reclamation->id; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation?');">
                <i class="fas fa-trash"></i> Supprimer
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="reclamation-detail">
        <h3>Réclamation #<?php echo $reclamation->id; ?></h3>
        
        <div class="reclamation-meta">
            <div>
                <strong>Statut:</strong>
                <?php if($reclamation->status == 'en_cours'): ?>
                    <span class="badge badge-warning">En cours</span>
                <?php else: ?>
                    <span class="badge badge-success">Validée</span>
                <?php endif; ?>
            </div>
            <div>
                <strong>Utilisateur concerné:</strong> <?php echo isset($reclamation->firstname) ? $reclamation->firstname . ' ' . $reclamation->lastname : 'Non spécifié'; ?>
            </div>
            <div>
                <strong>Date de création:</strong> <?php echo date('d/m/Y H:i', strtotime($reclamation->creation_date)); ?>
            </div>
        </div>
        
        <div class="reclamation-object">
            <h4>Objet</h4>
            <p><?php echo $reclamation->object; ?></p>
        </div>
        
        <div class="reclamation-description">
            <p><?php echo nl2br($reclamation->description); ?></p>
        </div>
        
        <?php if(isset($responses) && count($responses) > 0): ?>
        <div class="responses">
            <h4><i class="fas fa-reply"></i> Réponses</h4>
            
            <?php foreach($responses as $response): ?>
            <div class="response-item">
                <div class="response-meta">
                    <strong><?php echo $response['firstname'] . ' ' . $response['lastname']; ?></strong>
                    <small><?php echo date('d/m/Y H:i', strtotime($response['creation_date'])); ?></small>
                </div>
                <div class="response-content">
                    <p><?php echo nl2br($response['description']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Aucune réponse pour le moment. Notre équipe traite votre réclamation.
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'includes/frontoffice_footer.php'; ?> 
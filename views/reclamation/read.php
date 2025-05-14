<?php include_once 'includes/header.php'; ?>

<div class="dashboard">
    <div class="dashboard-header">
        <h2><i class="fas fa-file-alt"></i> Détail de la Réclamation</h2>
        <div>
            <a href="index.php?action=reclamations" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Retour aux Réclamations
            </a>
            
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <a href="index.php?action=reclamation_update&id=<?php echo $this->reclamation->id; ?>" class="btn btn-success">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="index.php?action=reclamation_delete&id=<?php echo $this->reclamation->id; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation?');">
                <i class="fas fa-trash"></i> Supprimer
            </a>
            <?php elseif(isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
            <a href="index.php?action=reclamation_update&id=<?php echo $this->reclamation->id; ?>" class="btn btn-success">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="index.php?action=reclamation_delete&id=<?php echo $this->reclamation->id; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation?');">
                <i class="fas fa-trash"></i> Supprimer
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="reclamation-detail">
        <h3>Réclamation #<?php echo $this->reclamation->id; ?></h3>
        
        <div class="reclamation-meta">
            <div>
                <strong>Statut:</strong>
                <?php if($this->reclamation->status == 'en_cours'): ?>
                    <span class="badge badge-warning">En cours</span>
                <?php else: ?>
                    <span class="badge badge-success">Validée</span>
                <?php endif; ?>
            </div>
            <div>
                <strong>Utilisateur concerné:</strong> <?php echo isset($this->reclamation->firstname) ? $this->reclamation->firstname . ' ' . $this->reclamation->lastname : 'Non spécifié'; ?>
            </div>
            <div>
                <strong>Date de création:</strong> <?php echo date('d/m/Y H:i', strtotime($this->reclamation->creation_date)); ?>
            </div>
        </div>
        
        <div class="reclamation-object">
            <h4>Objet</h4>
            <p><?php echo $this->reclamation->object; ?></p>
        </div>
        
        <div class="reclamation-description">
            <p><?php echo nl2br($this->reclamation->description); ?></p>
        </div>
        
        <?php
        // Check if there are responses to this reclamation
        $response_query = "SELECT r.id, r.description, r.creation_date, u.firstname, u.lastname FROM response r
                          LEFT JOIN user u ON r.admin_id = u.id
                          WHERE r.reclamation_id = ?
                          ORDER BY r.creation_date ASC";
        
        $stmt = $this->db->prepare($response_query);
        $stmt->bindParam(1, $this->reclamation->id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0):
        ?>
        <div class="responses">
            <h4><i class="fas fa-reply"></i> Réponses</h4>
            
            <?php while($response = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="response-item card">
                <div class="response-meta">
                    <strong><?php echo $response['firstname'] . ' ' . $response['lastname']; ?></strong>
                    <small><?php echo date('d/m/Y H:i', strtotime($response['creation_date'])); ?></small>
                </div>
                <div class="response-content">
                    <p><?php echo nl2br($response['description']); ?></p>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin' && $this->reclamation->status == 'en_cours'): ?>
        <div class="response-form card">
            <h4><i class="fas fa-reply"></i> Ajouter une réponse</h4>
            
            <form action="index.php?action=response_create" method="POST">
                <input type="hidden" name="reclamation_id" value="<?php echo $this->reclamation->id; ?>">
                <input type="hidden" name="admin_id" value="<?php echo $_SESSION['id']; ?>">
                
                <div class="form-group">
                    <textarea class="form-control" name="description" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="validate" name="validate" value="1">
                        <label class="form-check-label" for="validate">
                            Marquer cette réclamation comme validée
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-paper-plane"></i> Envoyer la réponse
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?> 
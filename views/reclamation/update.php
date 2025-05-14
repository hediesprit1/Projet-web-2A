<?php include_once 'includes/header.php'; ?>

<div class="dashboard">
    <div class="dashboard-header">
        <h2><i class="fas fa-edit"></i> Modifier la Réclamation</h2>
        <div>
            <a href="index.php?action=reclamations" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Retour aux Réclamations
            </a>
            <a href="index.php?action=reclamation_read&id=<?php echo $this->reclamation->id; ?>" class="btn btn-primary">
                <i class="fas fa-eye"></i> Voir les détails
            </a>
        </div>
    </div>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="card">
        <form action="index.php?action=reclamation_update&id=<?php echo $this->reclamation->id; ?>" method="POST">
            <?php if(isset($users) && count($users) > 0): ?>
            <div class="form-group">
                <label for="user_id">Utilisateur concerné</label>
                <select class="form-control" id="user_id" name="user_id" required>
                    <option value="">-- Sélectionner un utilisateur --</option>
                    <?php foreach($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>" <?php echo ($this->reclamation->user_id == $user['id']) ? 'selected' : ''; ?>>
                            <?php echo $user['lastname'] . ' ' . $user['firstname']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="object">Objet</label>
                <input type="text" class="form-control" id="object" name="object" required value="<?php echo $this->reclamation->object; ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="6" required><?php echo $this->reclamation->description; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="status">Statut</label>
                <select class="form-control" id="status" name="status">
                    <option value="en_cours" <?php echo ($this->reclamation->status == 'en_cours') ? 'selected' : ''; ?>>En cours</option>
                    <option value="valider" <?php echo ($this->reclamation->status == 'valider') ? 'selected' : ''; ?>>Validée</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
        </form>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?> 
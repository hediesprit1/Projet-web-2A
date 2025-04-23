<?php include_once 'includes/header.php'; ?>

<div class="dashboard">
    <div class="dashboard-header">
        <h2><i class="fas fa-plus-circle"></i> Nouvelle Réclamation</h2>
        <a href="index.php?action=reclamations" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Retour aux Réclamations
        </a>
    </div>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="card">
        <form action="index.php?action=reclamation_create" method="POST">
            <div class="form-group">
                <label for="user_id">Utilisateur concerné par la réclamation</label>
                <select class="form-control" id="user_id" name="user_id" required>
                    <option value="">-- Sélectionner un utilisateur --</option>
                    <?php foreach($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>"><?php echo $user['lastname'] . ' ' . $user['firstname']; ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-muted">Sélectionnez l'utilisateur pour qui vous créez cette réclamation.</small>
            </div>
            
            <div class="form-group">
                <label for="object">Objet de la réclamation</label>
                <input type="text" class="form-control" id="object" name="object" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description de votre réclamation</label>
                <textarea class="form-control" id="description" name="description" rows="6" required></textarea>
                <small>Veuillez décrire votre problème en détail pour nous aider à mieux comprendre votre situation.</small>
            </div>
            
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Soumettre la réclamation
            </button>
        </form>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?> 
<?php include_once 'includes/frontoffice_header.php'; ?>

<div class="dashboard">
    <div class="dashboard-header">
        <h2><i class="fas fa-edit"></i> Modifier la réclamation</h2>
        <div class="btn-group">
            <a href="index.php?action=frontoffice_reclamations" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <a href="index.php?action=frontoffice_reclamation_details&id=<?php echo isset($reclamation['id']) ? $reclamation['id'] : ''; ?>" class="btn btn-info">
                <i class="fas fa-eye"></i> Voir détails
            </a>
        </div>
    </div>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Formulaire de modification</h3>
        </div>
        <div class="card-body">
            <form id="updateForm" action="index.php?action=frontoffice_reclamation_update&id=<?php echo isset($reclamation['id']) ? $reclamation['id'] : ''; ?>" method="POST" novalidate>
                <div class="form-group">
                    <label for="user_id">Utilisateur concerné par la réclamation <span class="text-danger">*</span></label>
                    <select class="form-control" id="user_id" name="user_id" required>
                        <option value="">-- Sélectionner un utilisateur --</option>
                        <?php foreach($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>" <?php echo (isset($reclamation['user_id']) && $reclamation['user_id'] == $user['id']) ? 'selected' : ''; ?>>
                                <?php echo $user['lastname'] . ' ' . $user['firstname']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Veuillez sélectionner un utilisateur.</div>
                    <small class="form-text text-muted">Sélectionnez l'utilisateur concerné par cette réclamation.</small>
                </div>
                
                <div class="form-group">
                    <label for="object">Objet de la réclamation <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="object" name="object" required 
                           minlength="5" maxlength="100"
                           value="<?php echo isset($reclamation['object']) ? htmlspecialchars($reclamation['object']) : ''; ?>"
                           placeholder="Ex: Problème de réservation, Erreur de facturation...">
                    <div class="invalid-feedback">L'objet doit contenir au moins 5 caractères.</div>
                    <small class="form-text text-muted">Minimum 5 caractères, maximum 100 caractères.</small>
                </div>
                
                <div class="form-group">
                    <label for="description">Description détaillée <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="6" required 
                              minlength="20" maxlength="1000"
                              placeholder="Décrivez votre problème en détail..."><?php echo isset($reclamation['description']) ? htmlspecialchars($reclamation['description']) : ''; ?></textarea>
                    <div class="invalid-feedback">La description doit contenir au moins 20 caractères.</div>
                    <small class="form-text text-muted">Minimum 20 caractères, maximum 1000 caractères. Veuillez décrire votre problème en détail pour nous aider à mieux comprendre votre situation. <span id="charCount" class="text-info">0/1000</span></small>
                </div>
                
                <input type="hidden" name="status" value="en_cours">
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Tous les champs marqués d'un <span class="text-danger">*</span> sont obligatoires.
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmation" required>
                        <label class="form-check-label" for="confirmation">
                            Je confirme que les informations modifiées sont exactes.
                        </label>
                        <div class="invalid-feedback">
                            Vous devez confirmer pour soumettre les modifications.
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </form>
        </div>
    </div>
</div>

<script src="js/reclamation-validation.js"></script>

<style>
.card {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 20px;
}

.card-header {
    padding: 15px;
    font-weight: bold;
}

.card-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.invalid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 80%;
    color: #dc3545;
}

.was-validated .form-control:invalid,
.form-control.is-invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.was-validated .form-control:valid,
.form-control.is-valid {
    border-color: #28a745;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.was-validated .form-control:invalid ~ .invalid-feedback,
.form-control.is-invalid ~ .invalid-feedback {
    display: block;
}

.btn-group {
    display: flex;
    gap: 10px;
}
</style>

<?php include_once 'includes/frontoffice_footer.php'; ?> 
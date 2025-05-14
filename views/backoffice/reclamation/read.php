<?php include_once 'includes/backoffice_header.php'; ?>

<div class="dashboard">
    <div class="dashboard-header">
        <h2><i class="fas fa-file-alt"></i> Détail de la Réclamation</h2>
        <div>
            <a href="index.php?action=backoffice_reclamations" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Retour aux réclamations
            </a>
            
            <?php if($reclamation->status == 'en_cours'): ?>
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#validateModal">
                <i class="fas fa-check"></i> Valider la réclamation
            </button>
            <?php endif; ?>
            
            <?php if($reclamation->status == 'en_cours'): ?>
            <a href="index.php?action=backoffice_reclamation_update&id=<?php echo $reclamation->id; ?>" class="btn btn-success">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <?php endif; ?>
            <a href="index.php?action=backoffice_reclamation_delete&id=<?php echo $reclamation->id; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation?');">
                <i class="fas fa-trash"></i> Supprimer
            </a>
        </div>
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
    
    <div class="reclamation-detail card">
        <div class="card-header <?php echo ($reclamation->status == 'en_cours') ? 'bg-warning' : 'bg-success'; ?> text-white">
            <h3 class="mb-0">Réclamation #<?php echo $reclamation->id; ?> 
                <?php if($reclamation->status == 'en_cours'): ?>
                    <span class="badge badge-warning">En cours</span>
                <?php else: ?>
                    <span class="badge badge-success">Validée</span>
                <?php endif; ?>
            </h3>
        </div>
        
        <div class="card-body">
            <div class="reclamation-meta mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <strong><i class="fas fa-user"></i> Utilisateur concerné:</strong><br>
                        <?php echo isset($reclamation->firstname) ? $reclamation->firstname . ' ' . $reclamation->lastname : 'Non spécifié'; ?>
                    </div>
                    <div class="col-md-4">
                        <strong><i class="fas fa-calendar-alt"></i> Date de création:</strong><br>
                        <?php echo date('d/m/Y H:i', strtotime($reclamation->creation_date)); ?>
                    </div>
                    <div class="col-md-4">
                        <strong><i class="fas fa-info-circle"></i> Statut:</strong><br>
                        <?php if($reclamation->status == 'en_cours'): ?>
                            <span class="badge badge-warning">En cours</span>
                        <?php else: ?>
                            <span class="badge badge-success">Validée</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="reclamation-object mb-3">
                <h4 class="border-bottom pb-2"><i class="fas fa-heading"></i> Objet</h4>
                <p class="lead"><?php echo $reclamation->object; ?></p>
            </div>
            
            <div class="reclamation-description">
                <h4 class="border-bottom pb-2"><i class="fas fa-align-left"></i> Description</h4>
                <div class="p-3 bg-light rounded">
                    <?php echo nl2br($reclamation->description); ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php if(isset($responses) && count($responses) > 0): ?>
    <div class="responses card mt-4">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="fas fa-reply"></i> Réponses (<?php echo count($responses); ?>)</h4>
        </div>
        <div class="card-body">
            <?php foreach($responses as $response): ?>
            <div class="response-item mb-3 p-3 <?php echo ($response['admin_id'] == $_SESSION['id']) ? 'border-left border-info' : 'border-left border-secondary'; ?>">
                <div class="response-meta d-flex justify-content-between mb-2">
                    <strong><i class="fas fa-user-shield"></i> <?php echo $response['firstname'] . ' ' . $response['lastname']; ?></strong>
                    <small class="text-muted"><i class="fas fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($response['creation_date'])); ?></small>
                </div>
                <div class="response-content">
                    <p><?php echo nl2br($response['description']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if($reclamation->status == 'en_cours'): ?>
    <div class="response-form card mt-4">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-reply"></i> Ajouter une réponse</h4>
        </div>
        <div class="card-body">
            <form action="index.php?action=backoffice_response_create" method="POST">
                <input type="hidden" name="reclamation_id" value="<?php echo $reclamation->id; ?>">
                <input type="hidden" name="admin_id" value="<?php echo $_SESSION['id']; ?>">
                
                <div class="form-group">
                    <label for="description">Votre réponse</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required minlength="10"></textarea>
                    <small class="form-text text-muted">Le message doit contenir au moins 10 caractères.</small>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="validate" name="validate" value="1">
                        <label class="form-check-label" for="validate">
                            Marquer cette réclamation comme validée
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-paper-plane"></i> Envoyer la réponse
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal de validation -->
<?php if($reclamation->status == 'en_cours'): ?>
<div class="modal fade" id="validateModal" tabindex="-1" role="dialog" aria-labelledby="validateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="validateModalLabel">
                    <i class="fas fa-check-circle"></i> Validation de la réclamation #<?php echo $reclamation->id; ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="index.php?action=backoffice_reclamation_validate&id=<?php echo $reclamation->id; ?>" method="post">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Attention :</strong> Une fois validée, cette réclamation ne pourra plus être modifiée par l'utilisateur.
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <strong>Informations de la réclamation</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Utilisateur :</strong> <?php echo isset($reclamation->firstname) ? $reclamation->firstname . ' ' . $reclamation->lastname : 'Non spécifié'; ?></p>
                                    <p><strong>Date :</strong> <?php echo date('d/m/Y H:i', strtotime($reclamation->creation_date)); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Objet :</strong> <?php echo $reclamation->object; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="validation_message">
                            <i class="fas fa-envelope"></i> Message de validation :
                            <small class="text-muted">(Ce message sera envoyé à l'utilisateur)</small>
                        </label>
                        <textarea class="form-control" id="validation_message" name="validation_message" rows="5" required minlength="10"
                            placeholder="Entrez votre message de validation ici..."></textarea>
                        <small class="form-text text-muted">Le message doit contenir au moins 10 caractères.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-check"></i> Valider la réclamation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
    .reclamation-detail, .responses, .response-form {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 0.25rem;
        overflow: hidden;
    }
    
    .reclamation-meta {
        color: #666;
    }
    
    .response-item {
        border-radius: 0.25rem;
        border-left-width: 4px !important;
    }
    
    .response-meta strong {
        color: #333;
    }
    
    .dashboard-header {
        margin-bottom: 1.5rem;
    }
</style>

<?php include_once 'includes/backoffice_footer.php'; ?> 

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire de validation de réclamation
    const validateForm = document.querySelector('form[action*="backoffice_reclamation_validate"]');
    if (validateForm) {
        const messageField = validateForm.querySelector('textarea[name="validation_message"]');
        
        validateForm.addEventListener('submit', function(event) {
            // Vérifier si le message a au moins 10 caractères
            if (messageField && messageField.value.trim().length < 10) {
                event.preventDefault(); // Empêcher la soumission du formulaire
                alert('Le message de validation doit contenir au moins 10 caractères.');
                messageField.focus();
                messageField.classList.add('is-invalid');
            } else if (messageField) {
                messageField.classList.remove('is-invalid');
                messageField.classList.add('is-valid');
            }
        });
        
        // Validation en temps réel du champ de validation
        if (messageField) {
            messageField.addEventListener('input', function() {
                const charCount = this.value.trim().length;
                
                // Afficher un message de feedback
                let feedbackEl = this.nextElementSibling;
                if (!feedbackEl || !feedbackEl.classList.contains('char-count-feedback')) {
                    feedbackEl = document.createElement('div');
                    feedbackEl.className = 'char-count-feedback mt-1 small';
                    this.parentNode.insertBefore(feedbackEl, this.nextSibling);
                }
                
                if (charCount < 10) {
                    feedbackEl.textContent = `Il manque ${10 - charCount} caractère(s). Minimum: 10.`;
                    feedbackEl.className = 'char-count-feedback mt-1 small text-danger';
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                } else {
                    feedbackEl.textContent = `Longueur valide (${charCount} caractères)`;
                    feedbackEl.className = 'char-count-feedback mt-1 small text-success';
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });
            
            // Déclencher l'événement input pour initialiser le compteur
            const inputEvent = new Event('input');
            messageField.dispatchEvent(inputEvent);
        }
    }
    
    // Validation du formulaire d'ajout de réponse
    const responseForm = document.querySelector('form[action*="backoffice_response_create"]');
    if (responseForm) {
        const descriptionField = responseForm.querySelector('textarea[name="description"]');
        
        responseForm.addEventListener('submit', function(event) {
            // Vérifier si le message a au moins 10 caractères
            if (descriptionField && descriptionField.value.trim().length < 10) {
                event.preventDefault();
                alert('La réponse doit contenir au moins 10 caractères.');
                descriptionField.focus();
                descriptionField.classList.add('is-invalid');
            } else if (descriptionField) {
                descriptionField.classList.remove('is-invalid');
                descriptionField.classList.add('is-valid');
            }
        });
        
        // Validation en temps réel du champ de réponse
        if (descriptionField) {
            descriptionField.addEventListener('input', function() {
                const charCount = this.value.trim().length;
                
                // Afficher un message de feedback
                let feedbackEl = this.nextElementSibling;
                if (!feedbackEl || !feedbackEl.classList.contains('char-count-feedback')) {
                    feedbackEl = document.createElement('div');
                    feedbackEl.className = 'char-count-feedback mt-1 small';
                    this.parentNode.insertBefore(feedbackEl, this.nextSibling);
                }
                
                if (charCount < 10) {
                    feedbackEl.textContent = `Il manque ${10 - charCount} caractère(s). Minimum: 10.`;
                    feedbackEl.className = 'char-count-feedback mt-1 small text-danger';
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                } else {
                    feedbackEl.textContent = `Longueur valide (${charCount} caractères)`;
                    feedbackEl.className = 'char-count-feedback mt-1 small text-success';
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });
            
            // Déclencher l'événement input pour initialiser le compteur
            const inputEvent = new Event('input');
            descriptionField.dispatchEvent(inputEvent);
        }
    }
});
</script> 
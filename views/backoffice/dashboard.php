<?php include_once 'includes/backoffice_header.php'; ?>

<div class="dashboard">
    <div class="dashboard-header">
        <h2><i class="fas fa-tachometer-alt"></i> Tableau de bord administrateur</h2>
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
                <h3 class="card-title">Total réclamations</h3>
            </div>
            <div class="card-value"><?php echo $total_count; ?></div>
            <div class="card-footer">
                <a href="index.php?action=backoffice_reclamation_list">Voir toutes les réclamations</a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h3 class="card-title">En cours</h3>
            </div>
            <div class="card-value"><?php echo $en_cours_count; ?></div>
            <div class="card-footer">
                <a href="index.php?action=backoffice_reclamation_list&status=en_cours">Voir les réclamations en cours</a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-success text-white">
                <h3 class="card-title">Validées</h3>
            </div>
            <div class="card-value"><?php echo $valider_count; ?></div>
            <div class="card-footer">
                <a href="index.php?action=backoffice_reclamation_list&status=valider">Voir les réclamations validées</a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-info text-white">
                <h3 class="card-title">Utilisateurs</h3>
            </div>
            <div class="card-value"><?php echo $users_count; ?></div>
            <div class="card-footer">
                <a href="index.php?action=backoffice_user_list">Gérer les utilisateurs</a>
            </div>
        </div>
    </div>
    
    <!-- Statistique de validation -->
    <div class="stats-section">
        <div class="stats-card">
            <div class="stats-header">
                <h3><i class="fas fa-chart-pie"></i> Statistique de validation</h3>
            </div>
            <div class="stats-content">
                <div class="donut-chart-container">
                    <div class="donut-chart-wrapper">
                        <div class="donut-chart">
                            <svg width="200" height="200" viewBox="0 0 200 200">
                                <!-- Cercle de fond gris -->
                                <circle class="donut-background" cx="100" cy="100" r="70" fill="transparent" stroke="#e9ecef" stroke-width="30" />
                                <!-- Cercle d'animation coloré -->
                                <circle class="donut-segment" cx="100" cy="100" r="70" fill="transparent" stroke="#6c5ce7" stroke-width="30" 
                                    stroke-dasharray="0 440" transform="rotate(-90, 100, 100)" />
                            </svg>
                            <div class="donut-text">
                                <div class="donut-percent"><?php echo number_format($validation_rate, 1); ?>%</div>
                                <div class="donut-label">Validées</div>
                            </div>
                        </div>
                        <div class="donut-ratio">(<?php echo $valider_count; ?>/<?php echo $total_count; ?>)</div>
                    </div>
                    <div class="stats-details">
                        <div class="stats-detail">
                            <div class="detail-title">En cours</div>
                            <div class="detail-value"><?php echo $en_cours_count; ?></div>
                            <div class="detail-percent"><?php echo ($total_count > 0) ? number_format(($en_cours_count / $total_count) * 100, 1) : 0; ?>%</div>
                        </div>
                        <div class="stats-detail">
                            <div class="detail-title">Validées</div>
                            <div class="detail-value"><?php echo $valider_count; ?></div>
                            <div class="detail-percent"><?php echo number_format($validation_rate, 1); ?>%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="recent-section">
        <h3><i class="fas fa-history"></i> Réclamations récentes</h3>
        
        <?php if(count($recent_reclamations) > 0): ?>
        <div class="table-container">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
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
                        <td><?php echo isset($row['username']) ? $row['username'] : 'N/A'; ?></td>
                        <td><?php echo isset($row['object']) ? substr($row['object'], 0, 30) . (strlen($row['object']) > 30 ? '...' : '') : ''; ?></td>
                        <td><?php echo $status_badge; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['creation_date'])); ?></td>
                        <td class="table-actions">
                            <a href="index.php?action=backoffice_reclamation_read&id=<?php echo $row['id']; ?>" class="btn btn-primary action-btn" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <?php if($row['status'] == 'en_cours'): ?>
                            <button 
                               class="btn btn-info action-btn validate-btn" 
                               data-toggle="modal" 
                               data-target="#validateModal<?php echo $row['id']; ?>"
                               title="Valider">
                                <i class="fas fa-check"></i>
                            </button>
                            <?php endif; ?>
                            
                            <?php if($row['status'] != 'valider'): ?>
                            <a href="index.php?action=backoffice_reclamation_update&id=<?php echo $row['id']; ?>" class="btn btn-success action-btn" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php endif; ?>
                            <a href="index.php?action=backoffice_reclamation_delete&id=<?php echo $row['id']; ?>" class="btn btn-danger action-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation?');" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Modal de validation pour la réclamation <?php echo $row['id']; ?> -->
                    <?php if($row['status'] == 'en_cours'): ?>
                    <div class="modal fade" id="validateModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="validateModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="validateModalLabel<?php echo $row['id']; ?>">Valider la réclamation #<?php echo $row['id']; ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="index.php?action=backoffice_reclamation_validate&id=<?php echo $row['id']; ?>" method="post">
                                    <div class="modal-body">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Attention : Une fois validée, cette réclamation ne pourra plus être modifiée par l'utilisateur.
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="validation_message<?php echo $row['id']; ?>">Message de validation:</label>
                                            <textarea class="form-control" id="validation_message<?php echo $row['id']; ?>" name="validation_message" rows="4" required minlength="10"
                                                placeholder="Saisissez un message qui sera envoyé à l'utilisateur concernant la validation de sa réclamation"></textarea>
                                            <small class="form-text text-muted">Le message doit contenir au moins 10 caractères.</small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label><strong>Objet:</strong> <?php echo isset($row['object']) ? $row['object'] : 'N/A'; ?></label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ANNULER</button>
                                        <button type="submit" class="btn btn-primary">VALIDER LA RÉCLAMATION</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Aucune réclamation n'a été enregistrée.
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
    
    .card-footer {
        padding: 10px;
        text-align: center;
        border-top: 1px solid rgba(0,0,0,0.1);
    }
    
    .card-footer a {
        color: #007bff;
        text-decoration: none;
    }
    
    .stats-section {
        margin-bottom: 30px;
    }
    
    .stats-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .stats-header {
        background-color: #6c5ce7;
        color: white;
        padding: 15px;
        font-weight: bold;
    }
    
    .stats-header h3 {
        margin: 0;
        font-size: 1.2rem;
    }
    
    .stats-content {
        padding: 20px;
    }
    
    .donut-chart-container {
        display: flex;
        align-items: center;
        justify-content: space-around;
        flex-wrap: wrap;
    }
    
    .donut-chart-wrapper {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .donut-chart {
        position: relative;
        width: 200px;
        height: 200px;
        margin-bottom: 10px;
    }
    
    .donut-chart svg {
        position: relative;
        z-index: 1;
    }
    
    .donut-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        z-index: 2;
        color: #333;
    }
    
    .donut-percent {
        font-size: 2.5rem;
        font-weight: bold;
        line-height: 1;
        color: #6c5ce7;
    }
    
    .donut-label {
        font-size: 0.9rem;
        font-weight: normal;
        margin-top: 5px;
    }
    
    .donut-ratio {
        text-align: center;
        font-size: 1rem;
        font-weight: bold;
        color: #6c5ce7;
        margin-top: 5px;
    }
    
    .donut-segment {
        animation: donut-fill 1.5s ease-out forwards;
        animation-delay: 0.5s;
    }
    
    @keyframes donut-fill {
        to {
            stroke-dasharray: <?php echo $validation_rate * 4.4; ?> 440;
        }
    }
    
    .stats-details {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        width: 100%;
    }
    
    .stats-detail {
        text-align: center;
        flex: 0 0 150px;
        padding: 15px;
        border-radius: 8px;
        margin: 0 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .stats-detail:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .stats-detail:first-child {
        background-color: rgba(255, 193, 7, 0.2);
    }
    
    .stats-detail:last-child {
        background-color: rgba(40, 167, 69, 0.2);
    }
    
    .detail-title {
        font-weight: bold;
        margin-bottom: 5px;
        font-size: 1rem;
    }
    
    .detail-value {
        font-size: 2rem;
        font-weight: bold;
    }
    
    .detail-percent {
        font-size: 1rem;
        font-weight: normal;
        margin-top: 5px;
    }
    
    .recent-section {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .recent-section h3 {
        margin-top: 0;
        margin-bottom: 20px;
        color: #333;
        font-size: 1.2rem;
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table th, .table td {
        padding: 12px 15px;
        text-align: left;
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,0.02);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.05);
    }
    
    .table thead th {
        background-color: #343a40;
        color: white;
        border: none;
    }
    
    .table-actions {
        white-space: nowrap;
    }
    
    .badge {
        padding: 5px 10px;
        border-radius: 50px;
        font-weight: normal;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .badge-success {
        background-color: #28a745;
        color: white;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        margin-right: 5px;
    }
    
    .table-success {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }
    
    @media (max-width: 768px) {
        .dashboard-cards {
            flex-direction: column;
        }
        
        .card {
            margin-bottom: 15px;
        }
        
        .table-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
    }
</style>

<?php include_once 'includes/backoffice_footer.php'; ?>

<script>
// Validation des formulaires de validation des réclamations
document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les formulaires de validation
    const validateForms = document.querySelectorAll('form[action*="backoffice_reclamation_validate"]');
    
    validateForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            // Trouver le champ de texte dans ce formulaire spécifique
            const messageField = this.querySelector('textarea[name="validation_message"]');
            
            // Vérifier si le message a au moins 10 caractères
            if (messageField && messageField.value.trim().length < 10) {
                event.preventDefault(); // Empêcher la soumission du formulaire
                alert('Le message de validation doit contenir au moins 10 caractères.');
                messageField.focus(); // Mettre le focus sur le champ
                messageField.classList.add('is-invalid'); // Ajouter une classe pour signaler l'erreur visuellement
            } else if (messageField) {
                messageField.classList.remove('is-invalid');
                messageField.classList.add('is-valid');
            }
        });
        
        // Validation en temps réel lors de la frappe
        const messageField = form.querySelector('textarea[name="validation_message"]');
        if (messageField) {
            messageField.addEventListener('input', function() {
                const charCount = this.value.trim().length;
                
                // Afficher un message de feedback sous le champ
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
    });
});
</script> 
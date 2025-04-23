<?php include_once 'includes/header.php'; ?>

<div class="auth-container">
    <h2><i class="fas fa-user-plus"></i> Inscription</h2>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form action="index.php?action=register" method="POST">
        <div class="form-group">
            <label for="firstname">Prénom</label>
            <input type="text" class="form-control" id="firstname" name="firstname" required>
        </div>
        
        <div class="form-group">
            <label for="lastname">Nom</label>
            <input type="text" class="form-control" id="lastname" name="lastname" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirmer le mot de passe</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
    </form>
    
    <div style="margin-top: 20px; text-align: center;">
        <p>Vous avez déjà un compte? <a href="index.php?action=login">Se connecter</a></p>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?> 
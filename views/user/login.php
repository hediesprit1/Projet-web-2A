<?php include_once 'includes/header.php'; ?>

<div class="auth-container">
    <h2><i class="fas fa-sign-in-alt"></i> Connexion</h2>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form action="index.php?action=login" method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
    </form>
    
    <div style="margin-top: 20px; text-align: center;">
        <p>Vous n'avez pas de compte? <a href="index.php?action=register">S'inscrire</a></p>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?> 
<?php
session_start();
require_once(__DIR__ . '/../includes/icons.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — OLDating</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="auth-page">
    <form class="auth-box" action="../controller/connexion.php" method="post">
        <h1 class="auth-logo">OLDating 💕</h1>
        <p class="auth-sub">Rencontres amicales entre seniors</p>

        <?php if (isset($_GET['erreur'])): ?>
            <div class="error-msg" style="margin-bottom:16px;">❌ Identifiants incorrects</div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="success-msg" style="margin-bottom:16px;">✅ Inscription réussie, connectez-vous.</div>
        <?php endif; ?>
        <?php if (isset($_GET['compte']) && $_GET['compte']==='supprime'): ?>
            <div class="success-msg" style="margin-bottom:16px;">✅ Votre compte a été supprimé.</div>
        <?php endif; ?>

        <label for="mail">Email</label>
        <input type="email" name="mail" id="mail" required placeholder="votre@email.fr">

        <label for="mot_de_passe">Mot de passe</label>
        <div class="password-container">
            <input type="password" name="mot_de_passe" id="mot_de_passe" required placeholder="••••••••">
            <span class="toggle-password" onclick="togglePassword()"><?= icon('eye',20) ?></span>
        </div>
        <div class="forgot-password"><a href="mdp_oublie.php">Mot de passe oublié ?</a></div>

        <input type="submit" value="Se connecter →">
        <p style="text-align:center;margin-top:12px;font-size:.9em;">
            Pas encore de compte ? <a href="inscription.php">S'inscrire</a>
        </p>
    </form>
</div>
<script>
function togglePassword(){
    const i=document.getElementById('mot_de_passe');
    i.type = i.type==='password'?'text':'password';
}
</script>
</body>
</html>

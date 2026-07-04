<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
 <meta charset="UTF-8">
 <title>Inscription</title>
 <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
 <div class="box">
 <h2> Créer mon compte senior</h2>

 <?php if (isset($_GET['erreur']) && $_GET['erreur'] === 'mail'): ?>
 <div class="error-message"> Cet email est déjà utilisé.</div>
 <?php elseif (isset($_GET['erreur']) && $_GET['erreur'] === 'champs'): ?>
 <div class="error-message"> Veuillez remplir tous les champs obligatoires.</div>
 <?php endif; ?>

 <form action="../controller/inscription.php" method="POST">
 <label>Nom :</label>
 <input type="text" name="nom" required>
 <label>Prénom :</label>
 <input type="text" name="prenom" required>
 <label>Date de naissance :</label>
 <input type="date" name="date_naissance" required>
 <label>Email :</label>
 <input type="email" name="mail" required>
 <label>Mot de passe :</label>
 <input type="password" name="mot_de_passe" required>
 <label>Ville :</label>
 <input type="text" name="ville" required>
 <input type="submit" value="S'inscrire">
 </form>
 <p style="text-align:center; margin-top:15px;">
 Déjà inscrit ? <a href="authentification.php">Se connecter</a>
 </p>
 </div>
</div>
</body>
</html>

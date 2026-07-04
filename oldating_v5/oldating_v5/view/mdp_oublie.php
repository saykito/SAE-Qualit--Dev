<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 $mail = trim($_POST['mail']);

 if (!empty($mail)) {
 $stmt = $pdo->prepare("INSERT INTO notifications (type, contenu, date_creation) VALUES ('mot_de_passe', :contenu, NOW())");
 $contenu = "Demande de réinitialisation de mot de passe pour : " . htmlspecialchars($mail);
 $stmt->execute(['contenu' => $contenu]);
 $message = " Votre demande a été transmise à l'administrateur.";
 } else {
 $erreur = " Veuillez entrer votre adresse mail.";
 }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
 <meta charset="UTF-8">
 <title>Mot de passe oublié</title>
 <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
 <div class="box">
 <h2> Mot de passe oublié</h2>
 <?php if (isset($message)): ?>
 <div class="success-message"><?= $message ?></div>
 <?php elseif (isset($erreur)): ?>
 <div class="error-message"><?= $erreur ?></div>
 <?php endif; ?>

 <form method="POST">
 <label for="mail">Votre email :</label>
 <input type="email" name="mail" required>
 <input type="submit" value="Envoyer la demande">
 </form>
 <a href="authentification.php" class="btn-deconnexion"> Retour à la connexion</a>
 </div>
</div>
</body>
</html>

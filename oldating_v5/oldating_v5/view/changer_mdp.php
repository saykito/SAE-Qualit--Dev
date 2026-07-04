<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');
require_once(__DIR__ . '/../model/utilisateur.php');

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
 header('Location: authentification.php');
 exit();
}

// Liste des utilisateurs
$stmt = $pdo->query("SELECT id_utilisateur, prenom, nom FROM utilisateur ORDER BY prenom");
$utilisateurs = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 $id = $_POST['id_utilisateur'] ?? null;
 $new_mdp = $_POST['new_password'] ?? null;

 if ($id && $new_mdp) {
 $hash = password_hash($new_mdp, PASSWORD_DEFAULT);
 $update = $pdo->prepare("UPDATE utilisateur SET mot_de_passe = :hash WHERE id_utilisateur = :id");
 $update->execute(['hash' => $hash, 'id' => $id]);
 $success = true;
 } else {
 $error = "Tous les champs sont obligatoires.";
 }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
 <meta charset="UTF-8">
 <title>Changer mot de passe</title>
 <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
 <div class="box">
 <h2> Réinitialisation d’un mot de passe</h2>

 <?php if (!empty($success)): ?>
 <div class="success-message"> Mot de passe mis à jour avec succès.</div>
 <?php elseif (!empty($error)): ?>
 <div class="error-message"> <?= $error ?></div>
 <?php endif; ?>

 <form method="POST">
 <label for="id_utilisateur">Choisir un utilisateur :</label>
 <select name="id_utilisateur" required>
 <option value="">-- Sélectionner --</option>
 <?php foreach ($utilisateurs as $u): ?>
 <option value="<?= $u['id_utilisateur'] ?>">
 <?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?>
 </option>
 <?php endforeach; ?>
 </select>

 <label for="new_password">Nouveau mot de passe :</label>
 <input type="password" name="new_password" required>

 <input type="submit" value="Changer le mot de passe">
 </form>

 <a href="espace_admin.php" class="btn-deconnexion"> Retour à l’espace admin</a>
 </div>
</div>
</body>
</html>

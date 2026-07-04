<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');
require_once(__DIR__ . '/../model/utilisateur.php');

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
 header('Location: authentification.php');
 exit();
}

$utilisateurs = getAllUtilisateurs($pdo); // doit retourner uniquement les seniors
?>
<!DOCTYPE html>
<html lang="fr">
<head>
 <meta charset="UTF-8">
 <title>Gestion des utilisateurs</title>
 <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
 <div class="box">
 <h2> Créer un nouvel utilisateur</h2>

 <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
 <div class="success-message"> Utilisateur créé avec succès.</div>
 <?php elseif (isset($_GET['erreur']) && $_GET['erreur'] === 'mail'): ?>
 <div class="error-message"> Cet email est déjà utilisé.</div>
 <?php elseif (isset($_GET['erreur']) && $_GET['erreur'] === 'role'): ?>
 <div class="error-message"> Rôle invalide.</div>
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
 <input type="hidden" name="role" value="senior">
 <input type="submit" value="Créer l'utilisateur">
 </form>
 </div>

 <div class="box">
 <h2> Liste des utilisateurs seniors</h2>
 <?php foreach ($utilisateurs as $u): ?>
 <p>
 <?= htmlspecialchars($u['prenom']) ?> <?= htmlspecialchars($u['nom']) ?> — <?= htmlspecialchars($u['mail']) ?>
 <?php
 // Compte inactif depuis plus de 6 mois (besoin 4)
 $derniere = $u['date_derniere_connexion'] ?? null;
 if (!$derniere || strtotime($derniere) < strtotime('-6 months')):
 ?>
 <span class="inactif"> Inactif &gt; 6 mois</span>
 <?php endif; ?>
 <form action="../controller/supprimer_utilisateur.php" method="POST" style="display:inline;">
 <input type="hidden" name="id_utilisateur" value="<?= $u['id_utilisateur'] ?>">
 <input type="submit" value=" Supprimer">
 </form>
 </p>
 <?php endforeach; ?>
 <a href="espace_admin.php" class="btn-deconnexion"> Retour à l’accueil admin</a>
 </div>
</div>
</body>
</html>

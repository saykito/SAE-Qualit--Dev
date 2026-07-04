<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');

// Sécurité : accessible uniquement aux administrateurs
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
 header('Location: authentification.php');
 exit();
}

// Récupération des notifications récentes
$notifStmt = $pdo->query("SELECT * FROM notifications ORDER BY date_creation DESC LIMIT 5");
$notifications = $notifStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
 <meta charset="UTF-8">
 <title>Espace Admin</title>
 <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
 <div class="box">
 <h1>Espace Administrateur </h1>

 <?php if (isset($_GET['notif']) && $_GET['notif'] === 'ok'): ?>
 <div class="success-message"> Notification supprimée.</div>
 <?php elseif (isset($_GET['notif']) && $_GET['notif'] === 'erreur'): ?>
 <div class="error-message"> Erreur lors de la suppression de la notification.</div>
 <?php endif; ?>

 <?php if (!empty($notifications)): ?>
 <div class="notification-box">
 <h3> Demandes récentes :</h3>
 <ul>
 <?php foreach ($notifications as $n): 
 $contenu = htmlspecialchars($n['contenu'], ENT_QUOTES | ENT_HTML5);
 $date = htmlspecialchars($n['date_creation']);
 $id = urlencode($n['id_notification']);
 ?>
 <li>
 <?= $contenu ?> (<?= $date ?>)
 <a href="../controller/supprimer_notification.php?id=<?= $id ?>"
 class="btn-notif"
 onclick="return confirm('Confirmez-vous avoir traité cette notification ?');">
 Marquer comme traité
 </a>
 </li>
 <?php endforeach; ?>
 </ul>
 </div>
 <?php endif; ?>

 <ul>
 <li><a class="nav-link" href="admin_utilisateurs.php"> Gérer les utilisateurs</a></li>
 <li><a class="nav-link" href="admin_evenements.php"> Gérer les événements du calendrier</a></li>
 <li><a class="nav-link" href="changer_mdp.php"> Réinitialiser un mot de passe</a></li>
 </ul>

 <form action="deconnexion.php" method="post">
 <button type="submit" class="btn-deconnexion"> Se déconnecter</button>
 </form>
 </div>
</div>
</body>
</html>

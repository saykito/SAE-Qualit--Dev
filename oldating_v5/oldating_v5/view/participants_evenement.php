<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
 header('Location: authentification.php');
 exit();
}

$id_evenement = $_GET['id_evenement'] ?? null;

if (!$id_evenement) {
 header("Location: admin_evenements.php?erreur=idmanquant");
 exit();
}

$stmt = $pdo->prepare("
 SELECT u.nom, u.prenom, u.mail, u.ville, p.date_inscription
 FROM utilisateur u
 JOIN participation p ON u.id_utilisateur = p.id_utilisateur
 WHERE p.id_evenement = :id
 ORDER BY u.nom ASC
");
$stmt->execute(['id' => $id_evenement]);
$participants = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
 <meta charset="UTF-8">
 <title>Participants</title>
 <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
 <div class="box">
 <h2> Participants à l'événement</h2>

 <?php if (empty($participants)): ?>
 <p>Aucun participant inscrit.</p>
 <?php else: ?>
 <?php foreach ($participants as $p): ?>
 <p>
 <strong><?= htmlspecialchars($p['prenom']) ?> <?= htmlspecialchars($p['nom']) ?></strong><br>
 <?= htmlspecialchars($p['mail']) ?><br>
 <?= htmlspecialchars($p['ville']) ?><br>
 Inscription le <?= $p['date_inscription'] ?>
 </p>
 <hr>
 <?php endforeach; ?>
 <?php endif; ?>

 <a href="admin_evenements.php" class="btn-deconnexion"> Retour aux événements</a>
 </div>
</div>
</body>
</html>

<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'senior') {
 header('Location: authentification.php');
 exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupération des événements liés à l’utilisateur
$stmt = $pdo->prepare("
 SELECT e.*
 FROM evenement e
 JOIN participation p ON e.id_evenement = p.id_evenement
 WHERE p.id_utilisateur = :id
 ORDER BY e.date_heure ASC
");
$stmt->execute(['id' => $id_utilisateur]);
$evenements = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
 <meta charset="UTF-8">
 <title>Mon agenda</title>
 <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
 <div class="box">
 <h2> Mon agenda personnel</h2>

 <?php if (empty($evenements)): ?>
 <p>Vous n’êtes inscrit à aucun événement.</p>
 <?php else: ?>
 <?php foreach ($evenements as $e): ?>
 <div class="event-card" style="margin-bottom: 20px;">
 <strong><?= htmlspecialchars($e['titre']) ?></strong><br>
 <?= htmlspecialchars($e['description']) ?><br>
 <?= $e['date_heure'] ?> à <?= htmlspecialchars($e['lieu']) ?><br>
 </div>
 <?php endforeach; ?>
 <?php endif; ?>

 <a href="espace_senior.php" class="btn-deconnexion"> Retour</a>
 </div>
</div>
</body>
</html>

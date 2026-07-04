<?php
// Cette vue est incluse depuis controller/agenda.php
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
 <p>Vous n’êtes inscrit à aucun événement pour le moment.</p>
 <?php else: ?>
 <?php foreach ($evenements as $e): ?>
 <div class="event-card" style="margin-bottom: 20px;">
 <strong><?= htmlspecialchars($e['titre']) ?></strong><br>
 <?= htmlspecialchars($e['description']) ?><br>
 <?= htmlspecialchars($e['lieu']) ?><br>
 <?= $e['date_heure'] ?>
 </div>
 <?php endforeach; ?>
 <?php endif; ?>

 <a href="espace_senior.php" class="btn-deconnexion"> Retour</a>
 </div>
</div>
</body>
</html>

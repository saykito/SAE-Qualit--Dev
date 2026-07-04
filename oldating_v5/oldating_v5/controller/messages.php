<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');

if (!isset($_SESSION['id_utilisateur'])) {
 exit(); // Sécurité
}

$id_utilisateur = $_SESSION['id_utilisateur'];

$stmt = $pdo->prepare("
 SELECT m.*, u1.prenom AS emetteur, u2.prenom AS destinataire
 FROM message m
 JOIN utilisateur u1 ON m.id_emetteur = u1.id_utilisateur
 JOIN utilisateur u2 ON m.id_destinataire = u2.id_utilisateur
 WHERE m.id_emetteur = :id OR m.id_destinataire = :id
 ORDER BY m.date_heure DESC
");
$stmt->execute(['id' => $id_utilisateur]);
$messages = $stmt->fetchAll();

foreach ($messages as $msg) {
 echo "<div class='message'>";
 echo "<strong>" . htmlspecialchars($msg['emetteur']) . " " . htmlspecialchars($msg['destinataire']) . "</strong><br>";
 echo htmlspecialchars($msg['contenu']) . "<br>";
 echo "<em style='font-size: 0.9em;'> " . $msg['date_heure'] . "</em>";
 echo "</div><hr>";
}

<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'senior') {
    header('Location: ../view/authentification.php'); exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_evenement   = (int)($_POST['id_evenement'] ?? 0);
$action         = $_POST['action'] ?? 'inscrire';

if (!$id_evenement) { header('Location: ../view/evenement.php?erreur=manquant'); exit(); }

if ($action === 'desinscrire') {
    $stmt = $pdo->prepare("DELETE FROM participation WHERE id_utilisateur = :u AND id_evenement = :e");
    $stmt->execute(['u' => $id_utilisateur, 'e' => $id_evenement]);
    header('Location: ../view/evenement.php?success=desinscrit'); exit();
}

// Inscrire
$check = $pdo->prepare("SELECT 1 FROM participation WHERE id_utilisateur = :u AND id_evenement = :e");
$check->execute(['u' => $id_utilisateur, 'e' => $id_evenement]);
if ($check->fetch()) { header('Location: ../view/evenement.php?erreur=deja_inscrit'); exit(); }

$places = $pdo->prepare("SELECT e.nb_max_participants, COUNT(p.id_utilisateur) AS inscrits FROM evenement e LEFT JOIN participation p ON e.id_evenement = p.id_evenement WHERE e.id_evenement = :e GROUP BY e.id_evenement, e.nb_max_participants");
$places->execute(['e' => $id_evenement]);
$info = $places->fetch();
if ($info && $info['inscrits'] >= $info['nb_max_participants']) { header('Location: ../view/evenement.php?erreur=complet'); exit(); }

$insert = $pdo->prepare("INSERT INTO participation (id_utilisateur, id_evenement) VALUES (:u, :e)");
$insert->execute(['u' => $id_utilisateur, 'e' => $id_evenement]);
header('Location: ../view/evenement.php?success=inscrit'); exit();

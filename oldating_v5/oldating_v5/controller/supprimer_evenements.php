<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../view/authentification.php');
    exit();
}

$id_evenement = $_POST['id_evenement'] ?? null;

if (!$id_evenement) {
    header('Location: ../view/admin_evenements.php?erreur=idmanquant');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM evenement WHERE id_evenement = :id");
$stmt->execute(['id' => $id_evenement]);

if (!$stmt->fetch()) {
    header('Location: ../view/admin_evenements.php?erreur=introuvable');
    exit();
}

$delete = $pdo->prepare("DELETE FROM evenement WHERE id_evenement = :id");
$delete->execute(['id' => $id_evenement]);

header('Location: ../view/admin_evenements.php?success=suppression');
exit();

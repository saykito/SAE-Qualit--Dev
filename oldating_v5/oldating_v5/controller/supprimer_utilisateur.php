<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../view/authentification.php');
    exit();
}

$id_utilisateur = $_POST['id_utilisateur'] ?? null;

if (!$id_utilisateur) {
    header('Location: ../view/admin_utilisateurs.php?erreur=idmanquant');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = :id AND role = 'senior'");
$stmt->execute(['id' => $id_utilisateur]);
$utilisateur = $stmt->fetch();

if (!$utilisateur) {
    header('Location: ../view/admin_utilisateurs.php?erreur=inexistant');
    exit();
}

$delete = $pdo->prepare("DELETE FROM utilisateur WHERE id_utilisateur = :id");
$delete->execute(['id' => $id_utilisateur]);

header('Location: ../view/admin_utilisateurs.php?success=suppression');
exit();

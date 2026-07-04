<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'senior') {
    header('Location: ../view/authentification.php'); exit();
}

$id = $_SESSION['id_utilisateur'];
// Supprime l'utilisateur (CASCADE supprime profil, messages, participations)
$stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id_utilisateur = :id");
$stmt->execute(['id' => $id]);

session_destroy();
header('Location: ../view/authentification.php?compte=supprime'); exit();

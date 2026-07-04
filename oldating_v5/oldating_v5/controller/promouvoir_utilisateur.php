<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');
require_once(__DIR__ . '/../model/utilisateur.php');

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../view/authentification.php');
    exit();
}

$id_utilisateur = $_POST['id_utilisateur'] ?? null;

if (!$id_utilisateur) {
    header('Location: ../view/admin_utilisateurs.php?erreur=idmanquant');
    exit();
}

changerRole($pdo, $id_utilisateur, 'admin');

header('Location: ../view/admin_utilisateurs.php?success=promu');
exit();

<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');
require_once(__DIR__ . '/../model/utilisateur.php');

if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: ../view/authentification.php');
    exit();
}

if (isset($_POST['statut'])) {
    changerStatut($pdo, $_SESSION['id_utilisateur'], $_POST['statut']);
}

header('Location: ../view/messagerie.php');
exit();

<?php
/**
 * Contrôleur de l'agenda personnel d'un senior.
 * Récupère les événements auxquels il est inscrit puis inclut la vue.
 */
session_start();
require_once(__DIR__ . '/../config/bdd.php');
require_once(__DIR__ . '/../model/evenement.php');

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'senior') {
    header('Location: ../view/authentification.php');
    exit();
}

$evenements = getEvenementsInscrits($pdo, $_SESSION['id_utilisateur']);

require_once(__DIR__ . '/../view/agenda.php');

<?php
session_start();
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'senior') {
    header('Location: authentification.php');
    exit();
}
header('Location: decouvrir.php');
exit();

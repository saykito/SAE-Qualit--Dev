<?php
/**
 * Point d'entrée de l'application OLDating.
 * Redirige l'utilisateur déjà connecté vers son espace,
 * sinon vers la page de connexion.
 */
session_start();
if (isset($_SESSION['id_utilisateur'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: view/espace_admin.php');
    } else {
        header('Location: view/espace_senior.php');
    }
} else {
    header('Location: view/authentification.php');
}
exit();

<?php
/**
 * Contrôleur de connexion.
 * Vérifie l'email + mot de passe, ouvre la session et redirige
 * le senior ou l'administrateur vers son espace.
 */
session_start();
require_once(__DIR__ . '/../config/bdd.php');
require_once(__DIR__ . '/../model/utilisateur.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../view/authentification.php');
    exit();
}

$mail        = trim($_POST['mail'] ?? '');
$mot_de_passe = $_POST['mot_de_passe'] ?? '';

$utilisateur = getUtilisateurByMail($pdo, $mail);

// password_verify compare le mot de passe saisi avec le haché stocké
if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
    // Identifiants corrects : on ouvre la session
    $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
    $_SESSION['role']           = $utilisateur['role'];
    $_SESSION['pseudo']         = $utilisateur['prenom'];

    // Mise à jour de la date de dernière connexion (besoin 4 : comptes inactifs)
    $maj = $pdo->prepare("UPDATE utilisateur SET date_derniere_connexion = NOW() WHERE id_utilisateur = :id");
    $maj->execute(['id' => $utilisateur['id_utilisateur']]);

    if ($utilisateur['role'] === 'admin') {
        header('Location: ../view/espace_admin.php');
    } else {
        header('Location: ../view/decouvrir.php');
    }
    exit();
}

// Échec : retour au formulaire avec un message d'erreur
header('Location: ../view/authentification.php?erreur=identifiants');
exit();

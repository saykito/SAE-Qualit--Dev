<?php
/**
 * Contrôleur d'inscription / création de compte.
 * Utilisé soit par le senior lui-même (besoin 2), soit par
 * l'administrateur (besoin 4). Vérifie l'unicité de l'email,
 * crée l'utilisateur + un profil vide, puis simule l'envoi
 * d'un SMS de confirmation (cahier des charges).
 */
session_start();
require_once(__DIR__ . '/../config/bdd.php');
require_once(__DIR__ . '/../model/utilisateur.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../view/inscription.php');
    exit();
}

// Récupération et nettoyage des champs
$data = [
    'nom'            => trim($_POST['nom'] ?? ''),
    'prenom'         => trim($_POST['prenom'] ?? ''),
    'date_naissance' => $_POST['date_naissance'] ?? '',
    'mail'           => trim($_POST['mail'] ?? ''),
    'mot_de_passe'   => $_POST['mot_de_passe'] ?? '',
    'ville'          => trim($_POST['ville'] ?? ''),
    'role'           => 'senior'  // on force le rôle senior par sécurité
];

// La cible de redirection dépend de qui crée le compte
$estAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
$retour   = $estAdmin ? '../view/admin_utilisateurs.php' : '../view/authentification.php';

// Validation minimale
if ($data['nom'] === '' || $data['prenom'] === '' || $data['mail'] === '' || $data['mot_de_passe'] === '') {
    header("Location: $retour?erreur=champs");
    exit();
}

// Vérification de l'unicité de l'email
if (getUtilisateurByMail($pdo, $data['mail'])) {
    header("Location: $retour?erreur=mail");
    exit();
}

// Création de l'utilisateur (le mot de passe est haché dans le modèle)
if (creerUtilisateur($pdo, $data)) {
    // On récupère l'id du nouvel utilisateur pour lui créer un profil vide
    $nouveau = getUtilisateurByMail($pdo, $data['mail']);
    creerProfil($pdo, (int)$nouveau['id_utilisateur'], $data['prenom'], '', '', '', '');

    // Simulation de l'envoi du SMS de confirmation (pas de passerelle SMS réelle)
    $contenu = "SMS simulé envoyé à " . htmlspecialchars($data['prenom'])
             . " : votre inscription au site OLDating est confirmée.";
    $notif = $pdo->prepare("INSERT INTO notifications (type, contenu) VALUES ('inscription', :contenu)");
    $notif->execute(['contenu' => $contenu]);

    header("Location: $retour?success=1");
    exit();
}

header("Location: $retour?erreur=inconnue");
exit();

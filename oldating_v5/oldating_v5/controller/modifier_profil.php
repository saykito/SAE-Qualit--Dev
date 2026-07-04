<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');
require_once(__DIR__ . '/../model/utilisateur.php');

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'senior') {
    header('Location: ../view/authentification.php'); exit();
}

$id              = $_SESSION['id_utilisateur'];
$pseudo          = trim($_POST['pseudo']);
$description     = trim($_POST['description'] ?? '');
$situation       = trim($_POST['situation'] ?? '');
$centres_interet = trim($_POST['centres_interet'] ?? '');
$avatar_emoji    = '😊'; // valeur par défaut, plus de picker

// Photo : fichier uploadé prioritaire, sinon URL saisie
$photo = trim($_POST['photo'] ?? '');

if (!empty($_FILES['photo_fichier']['tmp_name'])) {
    $f = $_FILES['photo_fichier'];
    if (in_array($f['type'], ['image/jpeg','image/png','image/gif','image/webp']) && $f['size'] < 3*1024*1024) {
        $photo = 'data:' . $f['type'] . ';base64,' . base64_encode(file_get_contents($f['tmp_name']));
    }
}
// Si URL http, on la stocke directement comme valeur de photo
// (pas de conversion base64, juste l'URL)

$profil = getProfil($pdo, $id);
if ($profil) {
    $stmt = $pdo->prepare("UPDATE profil SET pseudo=:pseudo, photo=:photo, avatar_emoji=:emoji,
                           description=:desc, situation=:sit, centres_interet=:ci WHERE id_utilisateur=:id");
    $stmt->execute(['pseudo'=>$pseudo,'photo'=>$photo,'emoji'=>$avatar_emoji,'desc'=>$description,'sit'=>$situation,'ci'=>$centres_interet,'id'=>$id]);
} else {
    $stmt = $pdo->prepare("INSERT INTO profil (id_utilisateur,pseudo,photo,avatar_emoji,description,situation,centres_interet)
                           VALUES (:id,:pseudo,:photo,:emoji,:desc,:sit,:ci)");
    $stmt->execute(['id'=>$id,'pseudo'=>$pseudo,'photo'=>$photo,'emoji'=>$avatar_emoji,'desc'=>$description,'sit'=>$situation,'ci'=>$centres_interet]);
}
$_SESSION['pseudo'] = $pseudo;
header('Location: ../view/modifier_profil.php?profil=modifie'); exit();

<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');
require_once(__DIR__ . '/../model/evenement.php');

if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: ../view/authentification.php'); exit();
}

$id = $_SESSION['id_utilisateur'];
$role = $_SESSION['role'];

$titre       = trim($_POST['titre'] ?? '');
$description = trim($_POST['description'] ?? '');
$lieu        = trim($_POST['lieu'] ?? '');
$date_heure  = $_POST['date_heure'] ?? '';
$type        = $_POST['type'] ?? 'autre';
$nb_max      = (int)($_POST['nb_max_participants'] ?? 10);

// Image : fichier ou URL
$image = trim($_POST['image_url'] ?? '');
if (!empty($_FILES['image_fichier']['tmp_name'])) {
    $f = $_FILES['image_fichier'];
    if (in_array($f['type'], ['image/jpeg','image/png','image/gif','image/webp']) && $f['size'] < 3*1024*1024) {
        $image = 'data:' . $f['type'] . ';base64,' . base64_encode(file_get_contents($f['tmp_name']));
    }
}

if ($titre && $date_heure) {
    $stmt = $pdo->prepare("INSERT INTO evenement (titre, description, lieu, date_heure, type, nb_max_participants, image, id_createur)
                           VALUES (:titre, :desc, :lieu, :date, :type, :nb, :image, :createur)");
    $stmt->execute([
        'titre'    => $titre,
        'desc'     => $description,
        'lieu'     => $lieu,
        'date'     => $date_heure,
        'type'     => $type,
        'nb'       => $nb_max,
        'image'    => $image ?: null,
        'createur' => $role === 'admin' ? null : $id
    ]);
    $redirect = $role === 'admin' ? '../view/admin_evenements.php?success=ajout' : '../view/evenement.php?success=inscrit';
    header('Location: ' . $redirect); exit();
}

header('Location: ../view/form_ajouter_evenement.php?erreur=champs'); exit();

<?php
/**
 * Contrôleur d'envoi d'un message privé (chat).
 * Appelé en AJAX depuis messagerie.php.
 */
session_start();
require_once(__DIR__ . '/../config/bdd.php');

if (!isset($_SESSION['id_utilisateur'])) {
    http_response_code(403);
    exit();
}

$id_emetteur     = $_SESSION['id_utilisateur'];
$id_destinataire = (int)($_POST['id_destinataire'] ?? 0);
$contenu         = trim($_POST['contenu'] ?? '');

if ($id_destinataire > 0 && $contenu !== '') {
    $stmt = $pdo->prepare("
        INSERT INTO message (id_emetteur, id_destinataire, contenu)
        VALUES (:emetteur, :destinataire, :contenu)
    ");
    $stmt->execute([
        'emetteur'     => $id_emetteur,
        'destinataire' => $id_destinataire,
        'contenu'      => $contenu
    ]);
}
// Pas de redirection : la page rafraîchit le chat en AJAX.

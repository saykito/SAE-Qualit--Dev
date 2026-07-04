<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');
if (!isset($_SESSION['id_utilisateur'])) { http_response_code(403); exit(); }

$id_utilisateur = $_SESSION['id_utilisateur'];
$avec = (int)($_GET['avec'] ?? 0);
if ($avec <= 0) { echo json_encode([]); exit(); }

$stmt = $pdo->prepare("
    SELECT id_message, contenu, date_heure, id_emetteur
    FROM message
    WHERE (id_emetteur = :moi AND id_destinataire = :lui)
       OR (id_emetteur = :lui AND id_destinataire = :moi)
    ORDER BY date_heure ASC
");
$stmt->execute(['moi' => $id_utilisateur, 'lui' => $avec]);
$msgs = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($msgs);

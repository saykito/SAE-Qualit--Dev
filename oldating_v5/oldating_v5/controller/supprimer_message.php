<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');
if (!isset($_SESSION['id_utilisateur'])) { http_response_code(403); exit(); }

$id_message = (int)($_POST['id_message'] ?? 0);
$id_utilisateur = $_SESSION['id_utilisateur'];

// On ne peut supprimer que ses propres messages
$stmt = $pdo->prepare("DELETE FROM message WHERE id_message = :id AND id_emetteur = :moi");
$stmt->execute(['id' => $id_message, 'moi' => $id_utilisateur]);
http_response_code(200);

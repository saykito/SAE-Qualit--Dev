<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');

if (!isset($_SESSION['id_utilisateur'])) {
    http_response_code(403);
    echo json_encode(['erreur' => 'Non autorisé']);
    exit;
}

$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupère les événements publics + événements personnels de l'utilisateur
$stmt = $pdo->prepare("
    SELECT id_evenement, titre, description, date_heure, id_createur
    FROM evenement
    WHERE id_createur IS NULL OR id_createur = :id_utilisateur
    ORDER BY date_heure
");
$stmt->execute(['id_utilisateur' => $id_utilisateur]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Formatage des événements pour FullCalendar
$formattedEvents = [];
foreach ($events as $e) {
    $formattedEvents[] = [
        'id' => $e['id_evenement'],
        'title' => $e['titre'],
        'start' => $e['date_heure'],
        'description' => $e['description'],
        'type' => $e['id_createur'] == $id_utilisateur ? 'perso' : 'public'
    ];
}

header('Content-Type: application/json');
echo json_encode($formattedEvents);

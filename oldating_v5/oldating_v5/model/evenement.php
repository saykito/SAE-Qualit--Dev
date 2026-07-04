<?php
function ajouterEvenement(PDO $pdo, array $data): void {
    $sql = "INSERT INTO evenement (titre, description, lieu, date_heure, type, nb_max_participants)
            VALUES (:titre, :description, :lieu, :date_heure, :type, :nb_max_participants)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
}

function ajouterEvenementPerso(PDO $pdo, int $id_utilisateur, array $data): void {
    $sql = "INSERT INTO evenement (titre, description, date_heure, type, nb_max_participants, id_createur)
            VALUES (:titre, :description, :date_heure, 'perso', 1, :id_createur)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'titre' => $data['titre'],
        'description' => $data['description'],
        'date_heure' => $data['date_heure'],
        'id_createur' => $id_utilisateur
    ]);
}

function getEvenementsInscrits(PDO $pdo, int $id_utilisateur): array {
    $stmt = $pdo->prepare("SELECT e.*
        FROM evenement e
        JOIN participation p ON e.id_evenement = p.id_evenement
        WHERE p.id_utilisateur = :id
        ORDER BY e.date_heure ASC");
    $stmt->execute(['id' => $id_utilisateur]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
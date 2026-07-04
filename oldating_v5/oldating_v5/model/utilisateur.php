<?php

// === SECTION UTILISATEURS ===

function getUtilisateurByMail(PDO $pdo, string $mail): ?array {
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE mail = :mail");
    $stmt->execute(['mail' => $mail]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function creerUtilisateur(PDO $pdo, array $data): bool {
    $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, prenom, date_naissance, mail, mot_de_passe, ville, role)
                           VALUES (:nom, :prenom, :date_naissance, :mail, :mot_de_passe, :ville, :role)");
    return $stmt->execute([
        'nom' => $data['nom'],
        'prenom' => $data['prenom'],
        'date_naissance' => $data['date_naissance'],
        'mail' => $data['mail'],
        'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
        'ville' => $data['ville'],
        'role' => $data['role']
    ]);
}

function getAllUtilisateurs(PDO $pdo): array {
    $stmt = $pdo->query("SELECT * FROM utilisateur WHERE role = 'senior' ORDER BY nom ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function supprimerUtilisateur(PDO $pdo, int $id_utilisateur): void {
    $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id_utilisateur = :id");
    $stmt->execute(['id' => $id_utilisateur]);
}

// === SECTION PROFIL ===

function getProfil(PDO $pdo, int $id_utilisateur): ?array {
    $stmt = $pdo->prepare("SELECT * FROM profil WHERE id_utilisateur = :id");
    $stmt->execute(['id' => $id_utilisateur]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function mettreAJourProfil(PDO $pdo, int $id, string $pseudo, string $photo, string $description, string $situation, string $centres_interet): void {
    // handled directly in controller now
}

function creerProfil(PDO $pdo, int $id, string $pseudo, string $photo, string $description, string $situation, string $centres_interet): void {
    $stmt = $pdo->prepare("INSERT INTO profil (id_utilisateur, pseudo, photo, description, situation, centres_interet)
                           VALUES (:id, :pseudo, :photo, :description, :situation, :centres_interet)");
    $stmt->execute([
        'id' => $id,
        'pseudo' => $pseudo,
        'photo' => $photo,
        'description' => $description,
        'situation' => $situation,
        'centres_interet' => $centres_interet
    ]);
}

function changerRole(PDO $pdo, int $id_utilisateur, string $nouveau_role): void {
    $stmt = $pdo->prepare("UPDATE utilisateur SET role = :role WHERE id_utilisateur = :id");
    $stmt->execute([
        'id' => $id_utilisateur,
        'role' => $nouveau_role
    ]);
}

function changerStatut(PDO $pdo, int $id, string $statut): void {
    $stmt = $pdo->prepare("UPDATE utilisateur SET statut = :statut WHERE id_utilisateur = :id");
    $stmt->execute(['statut' => $statut, 'id' => $id]);
}

function getStatut(PDO $pdo, int $id): string {
    $stmt = $pdo->prepare("SELECT statut FROM utilisateur WHERE id_utilisateur = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetchColumn();
}


<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');

// Vérifie que l'utilisateur est bien un administrateur
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
 header('Location: ../view/authentification.php');
 exit();
}

// Suppression sécurisée d'une notification
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
 $id = (int) $_GET['id'];

 try {
 // Utilisation correcte de la colonne "id_notification"
 $stmt = $pdo->prepare("DELETE FROM notifications WHERE id_notification = :id");
 $stmt->bindParam(':id', $id, PDO::PARAM_INT);
 $stmt->execute();

 header('Location: ../view/espace_admin.php?notif=ok');
 exit();
 } catch (PDOException $e) {
 error_log("Erreur suppression notification : " . $e->getMessage());
 header('Location: ../view/espace_admin.php?notif=erreur');
 exit();
 }
} else {
 header('Location: ../view/espace_admin.php?notif=erreur');
 exit();
}

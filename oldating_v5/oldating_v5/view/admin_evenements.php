<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
 header('Location: authentification.php');
 exit();
}

// Récupération de tous les événements
$stmt = $pdo->query("SELECT * FROM evenement ORDER BY date_heure ASC");
$evenements = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
 <meta charset="UTF-8">
 <title>Gestion des événements</title>
 <link rel="stylesheet" href="../assets/style.css">
 <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
 <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
 <style>
 #calendar {
 max-width: 1000px;
 margin: 30px auto;
 background-color: #fff;
 border-radius: 8px;
 padding: 10px;
 }
 </style>
</head>
<body>
<div class="container">
 <div class="box">
 <h2> Événements en cours</h2>

 <a href="form_ajouter_evenement.php" class="btn"> Créer un événement</a>

 <?php if (isset($_GET['success']) && $_GET['success'] === 'suppression'): ?>
 <div class="success-message"> Événement supprimé avec succès.</div>
 <?php elseif (isset($_GET['erreur']) && $_GET['erreur'] === 'idmanquant'): ?>
 <div class="error-message"> ID manquant.</div>
 <?php elseif (isset($_GET['erreur']) && $_GET['erreur'] === 'introuvable'): ?>
 <div class="error-message"> Événement introuvable.</div>
 <?php endif; ?>

 <div id="calendar"></div>

 <script>
 document.addEventListener('DOMContentLoaded', function () {
 const calendarEl = document.getElementById('calendar');
 const calendar = new FullCalendar.Calendar(calendarEl, {
 initialView: 'dayGridMonth',
 locale: 'fr',
 height: "auto",
 headerToolbar: {
 left: 'prev,next today',
 center: 'title',
 right: 'dayGridMonth,listWeek'
 },
 events: {
 url: '../controller/load_events.php',
 method: 'GET',
 extraParams: {
 utilisateur_id: 0 // Admin voit tout
 },
 failure: () => alert("Erreur lors du chargement des événements.")
 },
 eventDidMount: function (info) {
 if (info.event.extendedProps.type === 'perso') {
 info.el.style.backgroundColor = '#d4edda';
 info.el.style.borderColor = '#c3e6cb';
 info.el.style.color = '#155724';
 } else {
 info.el.style.backgroundColor = '#cce5ff';
 info.el.style.borderColor = '#b8daff';
 info.el.style.color = '#004085';
 }
 }
 });
 calendar.render();
 });
 </script>

 <a href="espace_admin.php" class="btn-deconnexion"> Retour à l’accueil admin</a>
 </div>
</div>
</body>
</html>

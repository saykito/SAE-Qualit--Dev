<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');
require_once(__DIR__ . '/../includes/icons.php');
if (!isset($_SESSION['id_utilisateur'])) { header('Location: authentification.php'); exit(); }
$actif = 'evenements';
include(__DIR__ . '/../includes/nav_senior.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un événement — OLDating</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>body { padding-top: 62px; } .pc-content { max-width: 700px; margin: 0 auto; padding: 32px 36px; }</style>
</head>
<body>
<div class="pc-content">
    <div style="display:flex;align-items:center;gap:14px;margin-bottom:24px;">
        <a href="evenement.php" class="btn btn-outline btn-sm">← Retour</a>
        <div class="page-title" style="margin:0;">➕ Proposer un événement</div>
    </div>
    <div class="card">
        <form action="../controller/ajouter_evenement.php" method="post" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-full">
                    <label>📌 Titre</label>
                    <input type="text" name="titre" required placeholder="Ex: Balade au parc...">
                </div>
                <div>
                    <label>🏷️ Type</label>
                    <select name="type">
                        <option value="rencontre">💝 Rencontre</option>
                        <option value="jeux">🎲 Jeux</option>
                        <option value="musique">🎵 Musique</option>
                        <option value="balade">🌳 Balade</option>
                        <option value="cuisine">🍳 Cuisine</option>
                        <option value="culture">🎭 Culture</option>
                        <option value="danse">💃 Danse</option>
                        <option value="sport">⚽ Sport</option>
                        <option value="autre">📌 Autre</option>
                    </select>
                </div>
                <div>
                    <label>🪑 Nombre de places</label>
                    <input type="number" name="nb_max_participants" value="10" min="2" max="100">
                </div>
                <div>
                    <label>📅 Date et heure</label>
                    <input type="datetime-local" name="date_heure" required>
                </div>
                <div>
                    <label>📍 Lieu</label>
                    <input type="text" name="lieu" placeholder="Adresse ou nom du lieu">
                </div>
                <div class="form-full">
                    <label>📝 Description</label>
                    <textarea name="description" rows="3" placeholder="Décrivez l'événement..."></textarea>
                </div>
                <div class="form-full">
                    <label>🖼️ Image (optionnel)</label>
                    <div style="display:flex;gap:12px;align-items:flex-start;flex-wrap:wrap;">
                        <div class="photo-upload-zone" style="flex:1;min-width:200px;">
                            <input type="file" name="image_fichier" id="img_fichier" accept="image/*" style="display:none" onchange="previewImg(this)">
                            <button type="button" onclick="document.getElementById('img_fichier').click()" class="btn-upload">📂 Choisir une image</button>
                            <input type="text" name="image_url" placeholder="ou coller une URL : https://..." oninput="previewImgUrl(this.value)">
                        </div>
                        <div id="img-preview-box" style="display:none;width:120px;height:80px;border-radius:10px;overflow:hidden;border:2px solid #f0dde6;flex:none;">
                            <img id="img-preview-src" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                    </div>
                </div>
            </div>
            <div style="margin-top:18px;">
                <input type="submit" value="🎉 Créer l'événement">
            </div>
        </form>
    </div>
</div>
<script>
function previewImg(input) {
    if (input.files && input.files[0]) {
        const r = new FileReader(); r.onload = e => showPrev(e.target.result); r.readAsDataURL(input.files[0]);
    }
}
function previewImgUrl(url) { if (url.startsWith('http')) showPrev(url); }
function showPrev(src) {
    document.getElementById('img-preview-box').style.display='block';
    document.getElementById('img-preview-src').src=src;
}
</script>
</body>
</html>

<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');
require_once(__DIR__ . '/../model/utilisateur.php');
require_once(__DIR__ . '/../includes/icons.php');

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'senior') {
    header('Location: authentification.php'); exit();
}
$id     = $_SESSION['id_utilisateur'];
$profil = getProfil($pdo, $id);
$actif  = 'profil';
include(__DIR__ . '/../includes/nav_senior.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil — OLDating</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>body { padding-top: 62px; } .pc-content { max-width: 900px; margin: 0 auto; padding: 32px 36px; }</style>
</head>
<body>
<div class="pc-content">
    <div class="page-title">👤 Mon profil</div>

    <?php if (isset($_GET['profil']) && $_GET['profil']==='modifie'): ?>
        <div class="success-msg" style="margin-bottom:18px;">✅ Profil enregistré avec succès !</div>
    <?php endif; ?>

    <div class="profil-layout">
        <!-- Aperçu -->
        <div class="profil-sidebar-card">
            <div class="avatar avatar-lg" id="avatar-preview">
                <?php if (!empty($profil['photo'])): ?>
                    <img src="<?= htmlspecialchars($profil['photo']) ?>" alt="">
                <?php else: ?>
                    <span style="font-size:2em;">👤</span>
                <?php endif; ?>
            </div>
            <div style="font-family:'Fredoka',sans-serif;font-size:1.3em;font-weight:700;">
                <?= htmlspecialchars($profil['pseudo'] ?? $_SESSION['pseudo'] ?? '') ?>
            </div>
            <?php if (!empty($profil['situation'])): ?>
                <div style="font-size:.9em;color:var(--gris);">💑 <?= htmlspecialchars($profil['situation']) ?></div>
            <?php endif; ?>
            <?php if (!empty($profil['description'])): ?>
                <div style="font-size:.85em;color:#5a4d54;text-align:center;line-height:1.4;"><?= htmlspecialchars($profil['description']) ?></div>
            <?php endif; ?>

            <div style="margin-top:16px;width:100%;border-top:1px solid #f0dde6;padding-top:16px;display:flex;flex-direction:column;gap:8px;">
                <form action="../controller/supprimer_profil.php" method="post"
                      onsubmit="return confirm('⚠️ Supprimer définitivement votre compte ? Irréversible.');">
                    <button type="submit" class="btn-danger" style="width:100%;">🗑️ Supprimer mon compte</button>
                </form>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="profil-form-card">
            <form action="../controller/modifier_profil.php" method="post" enctype="multipart/form-data">
                <div class="form-grid">
                    <div>
                        <label>👤 Pseudo</label>
                        <input type="text" name="pseudo" value="<?= htmlspecialchars($profil['pseudo'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label>💑 Situation familiale</label>
                        <select name="situation">
                            <?php foreach (['','Célibataire','Marié(e)','Divorcé(e)','Veuf/Veuve','En couple'] as $s): ?>
                                <option value="<?= $s ?>" <?= ($profil['situation']??'') === $s ? 'selected':'' ?>><?= $s ?: '— Choisir —' ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-full">
                        <label>📸 Photo de profil</label>
                        <div style="display:flex;gap:12px;align-items:flex-start;flex-wrap:wrap;">
                            <div class="photo-upload-zone" style="flex:1;min-width:200px;">
                                <input type="file" name="photo_fichier" id="photo_fichier" accept="image/*" style="display:none" onchange="previewPhoto(this)">
                                <button type="button" onclick="document.getElementById('photo_fichier').click()" class="btn-upload">📂 Choisir un fichier</button>
                                <input type="text" name="photo" id="photo-url"
                                       value="<?= htmlspecialchars($profil['photo'] ?? '') ?>"
                                       placeholder="ou coller une URL : https://..."
                                       oninput="previewUrl(this.value)">
                            </div>
                            <div id="photo-preview-box" style="<?= empty($profil['photo'])?'display:none':'display:block' ?>;width:80px;height:80px;border-radius:50%;overflow:hidden;border:2px solid #f0dde6;flex:none;">
                                <img id="photo-preview-img" src="<?= htmlspecialchars($profil['photo'] ?? '') ?>" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                        </div>
                    </div>

                    <div class="form-full">
                        <label>💬 Description</label>
                        <textarea name="description" rows="3" placeholder="Parlez de vous..."><?= htmlspecialchars($profil['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-full">
                        <label>⭐ Centres d'intérêt <small style="font-weight:400">(séparés par des virgules)</small></label>
                        <input type="text" name="centres_interet" value="<?= htmlspecialchars($profil['centres_interet'] ?? '') ?>" placeholder="Jardinage, Belote, Danse...">
                    </div>
                </div>

                <div style="margin-top:18px;">
                    <input type="submit" value="💾 Enregistrer le profil">
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const r = new FileReader();
        r.onload = e => updatePreview(e.target.result);
        r.readAsDataURL(input.files[0]);
        document.getElementById('photo-url').value = '';
    }
}
function previewUrl(url) {
    if (url.startsWith('http') || url.startsWith('data:')) updatePreview(url);
}
function updatePreview(src) {
    const box = document.getElementById('photo-preview-box');
    const img = document.getElementById('photo-preview-img');
    box.style.display = 'block';
    img.src = src;
    // Mettre à jour aussi l'avatar dans la sidebar
    const av = document.getElementById('avatar-preview');
    av.innerHTML = `<img src="${src}" style="width:100%;height:100%;object-fit:cover;">`;
}
</script>
</body>
</html>

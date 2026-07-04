<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');
require_once(__DIR__ . '/../includes/icons.php');

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'senior') {
    header('Location: authentification.php'); exit();
}
$moi = $_SESSION['id_utilisateur'];

$stmt = $pdo->prepare("
    SELECT u.id_utilisateur, u.prenom, u.ville, u.date_naissance,
           COALESCE(p.pseudo, u.prenom) as pseudo,
           p.photo, p.description, p.centres_interet
    FROM utilisateur u
    LEFT JOIN profil p ON u.id_utilisateur = p.id_utilisateur
    WHERE u.role = 'senior' AND u.id_utilisateur != :moi
    ORDER BY RANDOM()
");
$stmt->execute(['moi' => $moi]);
$profils = $stmt->fetchAll();

function age($date) {
    if (!$date) return '';
    return (new DateTime($date))->diff(new DateTime('now'))->y;
}
$actif = 'decouvrir';
include(__DIR__ . '/../includes/nav_senior.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Découvrir — OLDating</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body { padding-top: 62px; background: #f5eef2; }
        .discover-wrap {
            max-width: 1000px; margin: 0 auto; padding: 32px 36px;
            display: grid; grid-template-columns: 420px 1fr; gap: 28px; align-items: start;
        }
        .card-big {
            background: #fff; border-radius: 20px;
            box-shadow: 0 12px 40px rgba(253,38,122,.15);
            overflow: hidden; border: 1px solid #f3e3e9;
        }
        .card-big-photo {
            height: 340px; background: var(--grad);
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; position: relative;
        }
        .card-big-photo img { width: 100%; height: 100%; object-fit: cover; }
        .card-big-photo .no-photo {
            font-size: 5em; color: rgba(255,255,255,.6);
        }
        .card-big-body { padding: 22px 24px 26px; }
        .card-big-name { font-family: 'Fredoka', sans-serif; font-size: 2em; font-weight: 700; }
        .card-big-age  { color: var(--gris); font-size: 1.2em; margin-left: 8px; }
        .card-big-ville { color: var(--gris); font-size: .9em; font-weight: 600; margin: 6px 0 10px; }
        .card-big-desc  { color: #5a4d54; font-size: .95em; line-height: 1.5; margin-bottom: 14px; }
        .card-actions {
            display: flex; gap: 12px; margin-top: 18px; align-items: center;
        }
        .btn-pass {
            background: #fff !important; color: var(--ko) !important;
            border: 2px solid var(--ko) !important; box-shadow: none !important;
        }
        .btn-pass:hover { background: var(--ko-bg) !important; transform: none !important; filter: none !important; }
        .counter {
            font-size: .88em; color: var(--gris); font-weight: 700;
            text-align: center; padding: 8px 0;
        }
        .nav-arrows {
            display: flex; gap: 10px; margin-top: 12px; justify-content: center;
        }
        .btn-nav {
            background: var(--grad-soft) !important; color: var(--rose) !important;
            border: 1px solid #f6cdda !important; box-shadow: none !important;
            padding: 8px 18px !important; font-size: .9em !important;
        }
        .btn-nav:hover { background: #ffeef4 !important; transform: none !important; filter: none !important; }
        .right-panel { display: flex; flex-direction: column; gap: 16px; }
        .mini-cards { display: flex; flex-direction: column; gap: 10px; max-height: 520px; overflow-y: auto; }
        .mini-card {
            display: flex; align-items: center; gap: 12px;
            background: #fff; border-radius: 12px; padding: 10px 14px;
            border: 1px solid #f3e3e9; cursor: pointer;
            transition: background .15s, border-color .15s; box-shadow: var(--ombre);
        }
        .mini-card:hover, .mini-card.active { background: #ffeef4; border-color: var(--rose); }
        .mini-card-photo {
            width: 48px; height: 48px; border-radius: 50%;
            background: var(--grad); overflow: hidden; flex: none;
            display: flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,.7); font-size: 1.4em;
        }
        .mini-card-photo img { width: 100%; height: 100%; object-fit: cover; }
        .empty-state { text-align:center; color:var(--gris); padding:60px 20px; background:#fff; border-radius:20px; }
    </style>
</head>
<body>
<div class="discover-wrap">
    <?php if (empty($profils)): ?>
        <div class="empty-state" style="grid-column:1/-1;">
            <div style="font-size:3em;margin-bottom:12px;">💝</div>
            <div>Aucun autre membre pour le moment.</div>
        </div>
    <?php else: ?>

    <!-- Carte principale -->
    <div>
        <div id="main-card" class="card-big"></div>
        <div class="counter" id="counter"></div>
        <div class="nav-arrows">
            <button class="btn btn-nav" onclick="aller(-1)">← Précédent</button>
            <button class="btn btn-nav" onclick="aller(1)">Suivant →</button>
        </div>
    </div>

    <!-- Liste à droite -->
    <div class="right-panel">
        <div style="font-family:'Fredoka',sans-serif;font-size:1.1em;font-weight:700;color:var(--rose);">
            Tous les membres
        </div>
        <div class="mini-cards" id="mini-cards"></div>
    </div>

    <?php endif; ?>
</div>

<div class="toast" id="toast"></div>

<script>
const profils = <?= json_encode(array_values($profils)) ?>;
let idx = 0;

function age(d) {
    if (!d) return '';
    const diff = Math.floor((new Date() - new Date(d)) / (365.25*24*60*60*1000));
    return diff + ' ans';
}

function renderCard(i) {
    const p = profils[i];
    if (!p) return;
    const nom  = p.pseudo || p.prenom;
    const a    = age(p.date_naissance);
    const ints = (p.centres_interet||'').split(',').filter(s=>s.trim()).slice(0,5);

    const photoHTML = p.photo
        ? `<img src="${p.photo}" alt="" onerror="this.parentElement.innerHTML='<span class=no-photo>👤</span>'">`
        : `<span class="no-photo">👤</span>`;

    document.getElementById('main-card').innerHTML = `
        <div class="card-big-photo">${photoHTML}</div>
        <div class="card-big-body">
            <div>
                <span class="card-big-name">${nom}</span>
                ${a ? `<span class="card-big-age">${a}</span>` : ''}
            </div>
            ${p.ville ? `<div class="card-big-ville">📍 ${p.ville}</div>` : ''}
            ${p.description ? `<div class="card-big-desc">${p.description}</div>` : ''}
            ${ints.length ? `<div class="chips">${ints.map(it=>`<span class="chip">${it.trim()}</span>`).join('')}</div>` : ''}
            <div class="card-actions">
                <button class="btn btn-pass" onclick="passer()">✗ Passer</button>
                <button class="btn" onclick="aimer()">♥ J'aime</button>
                <a href="messagerie.php?avec=${p.id_utilisateur}" class="btn btn-outline">💬 Écrire</a>
            </div>
        </div>`;

    document.getElementById('counter').textContent = `${i+1} / ${profils.length}`;

    // Highlight mini card
    document.querySelectorAll('.mini-card').forEach((el,j) => {
        el.classList.toggle('active', j===i);
    });
    // Scroll mini card into view
    const mc = document.querySelectorAll('.mini-card')[i];
    if (mc) mc.scrollIntoView({block:'nearest', behavior:'smooth'});
}

function renderMiniCards() {
    const container = document.getElementById('mini-cards');
    container.innerHTML = profils.map((p,i) => {
        const nom = p.pseudo || p.prenom;
        const a   = age(p.date_naissance);
        const photoHTML = p.photo
            ? `<img src="${p.photo}" alt="" onerror="this.style.display='none'">`
            : `<span style="font-size:1.2em;color:rgba(255,255,255,.7);">👤</span>`;
        return `<div class="mini-card" onclick="allerA(${i})">
            <div class="mini-card-photo">${photoHTML}</div>
            <div>
                <div style="font-weight:700;font-size:.9em;">${nom}</div>
                ${p.ville ? `<div style="font-size:.78em;color:var(--gris);">📍 ${p.ville}</div>` : ''}
            </div>
        </div>`;
    }).join('');
}

function aller(dir) {
    idx = (idx + dir + profils.length) % profils.length;
    renderCard(idx);
}
function allerA(i) { idx = i; renderCard(i); }

function affToast(html) {
    const t = document.getElementById('toast');
    t.innerHTML = html; t.classList.add('visible');
    clearTimeout(t._t); t._t = setTimeout(()=>t.classList.remove('visible'),3000);
}
function aimer()  {
    const p = profils[idx];
    affToast(`♥ Vous avez aimé <b>${p.pseudo||p.prenom}</b> — <a href="messagerie.php?avec=${p.id_utilisateur}">lui écrire</a>`);
    aller(1);
}
function passer() { aller(1); }

renderMiniCards();
renderCard(0);
</script>
</body>
</html>

<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');
require_once(__DIR__ . '/../model/utilisateur.php');
require_once(__DIR__ . '/../includes/icons.php');

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'senior') {
    header('Location: authentification.php'); exit();
}
$id_utilisateur  = $_SESSION['id_utilisateur'];
$destinataire_id = isset($_GET['avec']) ? (int)$_GET['avec'] : null;

$stmt = $pdo->prepare("
    SELECT u.id_utilisateur, u.prenom, u.statut,
           
           p.photo,
           COALESCE(p.pseudo, u.prenom) as pseudo
    FROM utilisateur u
    LEFT JOIN profil p ON u.id_utilisateur = p.id_utilisateur
    WHERE u.role='senior' AND u.id_utilisateur != :id
    ORDER BY u.prenom
");
$stmt->execute(['id' => $id_utilisateur]);
$utilisateurs = $stmt->fetchAll();

$destinataire = null;
foreach ($utilisateurs as $u) {
    if ($u['id_utilisateur'] == $destinataire_id) { $destinataire = $u; break; }
}
$statut = getStatut($pdo, $id_utilisateur);
function labelStatut($s) { return $s==='en ligne'?'🟢':($s==='absent'?'🟡':'🔴'); }
$actif = 'messagerie';
include(__DIR__ . '/../includes/nav_senior.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages — OLDating</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body { padding-top: 62px; }
        .pc-msg-wrap { max-width: 1100px; margin: 0 auto; padding: 24px 36px; }
    </style>
</head>
<body>
<div class="pc-msg-wrap">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
        <div class="page-title" style="margin:0;">💬 Messages</div>
        <form action="../controller/modifier_statut.php" method="POST" class="statut-form">
            <label style="margin:0;">Mon statut :</label>
            <select name="statut" onchange="this.form.submit()">
                <option value="en ligne" <?= $statut==='en ligne'?'selected':'' ?>>🟢 En ligne</option>
                <option value="absent" <?= $statut==='absent'?'selected':'' ?>>🟡 Absent</option>
                <option value="ne pas déranger" <?= $statut==='ne pas déranger'?'selected':'' ?>>🔴 Ne pas déranger</option>
            </select>
        </form>
    </div>

    <div class="chat-layout">
        <!-- Colonne contacts -->
        <div class="chat-contacts">
            <div class="chat-contacts-header">💬 Contacts</div>
            <?php foreach ($utilisateurs as $u): ?>
            <a href="messagerie.php?avec=<?= $u['id_utilisateur'] ?>"
               class="contact <?= $destinataire_id==$u['id_utilisateur']?'actif':'' ?>">
                <div class="avatar" style="width:38px;height:38px;font-size:1em;">
                    <?php if (!empty($u['photo'])): ?>
                        <img src="<?= htmlspecialchars($u['photo']) ?>" alt="">
                    <?php else: ?>
                        <span style="font-size:.85em;color:rgba(255,255,255,.8);">👤</span>
                    <?php endif; ?>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:700;font-size:.9em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($u['pseudo']) ?></div>
                    <div style="font-size:.78em;color:var(--gris);"><?= labelStatut($u['statut']) ?> <?= htmlspecialchars($u['statut']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Fenêtre chat -->
        <div class="chat-window">
            <?php if ($destinataire): ?>
                <div class="chat-window-header">
                    <div class="avatar" style="width:36px;height:36px;font-size:.95em;">
                        <?php if (!empty($destinataire['photo'])): ?>
                            <img src="<?= htmlspecialchars($destinataire['photo']) ?>" alt="">
                        <?php else: ?>
                            <span style="font-size:.85em;">👤</span>
                        <?php endif; ?>
                    </div>
                    <?= htmlspecialchars($destinataire['pseudo']) ?>
                    <span style="font-weight:400;font-size:.85em;"><?= labelStatut($destinataire['statut']) ?></span>
                </div>
                <div class="messages-area" id="messages_box"></div>
                <div class="chat-input-zone">
                    <textarea id="msg-input" placeholder="Votre message... (Entrée pour envoyer)" rows="1"></textarea>
                    <button id="btn-envoyer">➤</button>
                </div>
            <?php else: ?>
                <div class="chat-empty">
                    <span style="font-size:2.5em;">💬</span>
                    <span>Sélectionnez un contact pour commencer</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($destinataire): ?>
<script>
const avec = <?= $destinataire_id ?>;
const moi  = <?= $id_utilisateur ?>;
let dernierCount = 0;

function formaterDate(d) {
    const dt = new Date(d.replace(' ','T'));
    return dt.toLocaleDateString('fr-FR',{day:'2-digit',month:'2-digit'}) + ' ' +
           dt.toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit'});
}

function rafraichir(force=false) {
    fetch('../controller/fetch_messages.php?avec='+avec+'&t='+Date.now())
        .then(r=>r.json()).then(msgs=>{
            if (!force && msgs.length===dernierCount) return;
            dernierCount = msgs.length;
            const box = document.getElementById('messages_box');
            if (!msgs.length) {
                box.innerHTML = "<div style='text-align:center;color:#aaa;padding:24px;'>Aucun message. Lancez la conversation ! 👋</div>";
                return;
            }
            const wasBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 60;
            box.innerHTML = msgs.map(m => {
                const estMoi = m.id_emetteur == moi;
                const cls = estMoi ? 'message your_message' : 'message others_message';
                const del = estMoi ? `<button class="btn-del-msg" onclick="supprimerMessage(${m.id_message})">🗑️</button>` : '';
                return `<div class="${cls}" id="msg-${m.id_message}">
                    <p>${m.contenu.replace(/&/g,'&amp;').replace(/</g,'&lt;')}</p>
                    <div class="msg-meta"><span class="date">${formaterDate(m.date_heure)}</span>${del}</div>
                </div>`;
            }).join('');
            if (force || wasBottom) box.scrollTop = box.scrollHeight;
        }).catch(()=>{});
}

function supprimerMessage(id) {
    if (!confirm('Supprimer ce message ?')) return;
    fetch('../controller/supprimer_message.php',{method:'POST',body:new URLSearchParams({id_message:id})})
        .then(()=>rafraichir(true));
}

document.getElementById('btn-envoyer').addEventListener('click', envoyer);
document.getElementById('msg-input').addEventListener('keydown', function(e){
    if (e.key==='Enter' && !e.shiftKey) { e.preventDefault(); envoyer(); }
});
document.getElementById('msg-input').addEventListener('input', function(){
    this.style.height='auto';
    this.style.height=Math.min(this.scrollHeight,110)+'px';
});

function envoyer() {
    const input = document.getElementById('msg-input');
    const txt = input.value.trim();
    if (!txt) return;
    input.value=''; input.style.height='auto';
    fetch('../controller/envoyer_message.php',{
        method:'POST',
        body:new URLSearchParams({id_destinataire:avec, contenu:txt})
    }).then(()=>rafraichir(true));
}

rafraichir(true);
setInterval(()=>rafraichir(false), 3000);
</script>
<?php endif; ?>
</body>
</html>

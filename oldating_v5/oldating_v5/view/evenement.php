<?php
session_start();
require_once(__DIR__ . '/../config/bdd.php');
require_once(__DIR__ . '/../includes/icons.php');

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'senior') {
    header('Location: authentification.php'); exit();
}
$id_utilisateur = $_SESSION['id_utilisateur'];

$evenements = $pdo->query("SELECT * FROM evenement WHERE id_createur IS NULL ORDER BY date_heure ASC")->fetchAll();
$stmt_p = $pdo->prepare("SELECT id_evenement FROM participation WHERE id_utilisateur = :id");
$stmt_p->execute(['id' => $id_utilisateur]);
$ids_inscrits = $stmt_p->fetchAll(PDO::FETCH_COLUMN);

$type_emojis = ['rencontre'=>'💝','jeux'=>'🎲','musique'=>'🎵','balade'=>'🌳','cuisine'=>'🍳','culture'=>'🎭','danse'=>'💃','sport'=>'⚽','autre'=>'📌'];
$actif = 'evenements';
include(__DIR__ . '/../includes/nav_senior.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements — OLDating</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>body { padding-top: 62px; } .pc-content { max-width: 1100px; margin: 0 auto; padding: 32px 36px; }</style>
</head>
<body>
<div class="pc-content">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div class="page-title" style="margin:0;">📅 Événements</div>
        <a href="form_ajouter_evenement.php" class="btn">➕ Proposer un événement</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="success-msg" style="margin-bottom:18px;">✅ <?= $_GET['success']==='inscrit' ? 'Inscription réussie !' : 'Désinscription effectuée.' ?></div>
    <?php elseif (isset($_GET['erreur'])): ?>
        <div class="error-msg" style="margin-bottom:18px;">❌ <?= $_GET['erreur']==='deja_inscrit' ? 'Vous êtes déjà inscrit.' : ($_GET['erreur']==='complet' ? 'Événement complet.' : 'Erreur.') ?></div>
    <?php endif; ?>

    <?php if (empty($evenements)): ?>
        <div class="card" style="text-align:center;color:var(--gris);padding:48px;">Aucun événement pour le moment.</div>
    <?php else: ?>
    <div class="events-grid">
        <?php foreach ($evenements as $e):
            $emoji   = $type_emojis[$e['type']] ?? '📌';
            $inscrit = in_array($e['id_evenement'], $ids_inscrits);
            $nb_stmt = $pdo->prepare("SELECT COUNT(*) FROM participation WHERE id_evenement = :id");
            $nb_stmt->execute(['id' => $e['id_evenement']]);
            $nb_inscrits = (int)$nb_stmt->fetchColumn();
            $restantes   = $e['nb_max_participants'] - $nb_inscrits;
        ?>
        <div class="event-card">
            <div class="event-card-img">
                <?php if (!empty($e['image'])): ?>
                    <img src="<?= htmlspecialchars($e['image']) ?>" alt="">
                <?php else: ?>
                    <?= $emoji ?>
                <?php endif; ?>
            </div>
            <div class="event-card-body">
                <span class="event-type-badge"><?= $emoji ?> <?= htmlspecialchars($e['type']) ?></span>
                <div class="event-card-title"><?= htmlspecialchars($e['titre']) ?></div>
                <div class="event-card-desc"><?= htmlspecialchars($e['description']) ?></div>
                <div class="event-meta">
                    <span>📅 <?= date('d/m/Y à H\hi', strtotime($e['date_heure'])) ?></span>
                    <?php if ($e['lieu']): ?><span>📍 <?= htmlspecialchars($e['lieu']) ?></span><?php endif; ?>
                    <span>🪑 <?= $nb_inscrits ?>/<?= $e['nb_max_participants'] ?> inscrits</span>
                </div>
                <div class="event-card-footer">
                    <?php if ($inscrit): ?>
                        <span class="badge-inscrit">✅ Inscrit(e)</span>
                        <form action="../controller/inscription_evenement.php" method="POST" style="margin:0;">
                            <input type="hidden" name="id_evenement" value="<?= $e['id_evenement'] ?>">
                            <input type="hidden" name="action" value="desinscrire">
                            <button type="submit" class="btn-desinscrire">Se désinscrire</button>
                        </form>
                    <?php elseif ($restantes > 0): ?>
                        <form action="../controller/inscription_evenement.php" method="POST" style="margin:0;">
                            <input type="hidden" name="id_evenement" value="<?= $e['id_evenement'] ?>">
                            <input type="hidden" name="action" value="inscrire">
                            <button type="submit" class="btn btn-sm">S'inscrire →</button>
                        </form>
                        <span style="font-size:.8em;color:var(--gris);"><?= $restantes ?> place<?= $restantes>1?'s':'' ?> restante<?= $restantes>1?'s':'' ?></span>
                    <?php else: ?>
                        <span class="badge-complet">🔴 Complet</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
</body>
</html>

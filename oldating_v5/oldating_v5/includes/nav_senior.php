<?php
$actif = $actif ?? '';
$pseudo_nav = $_SESSION['pseudo'] ?? $_SESSION['prenom'] ?? 'Moi';

try {
    $stmt_nav = $pdo->prepare("SELECT photo FROM profil WHERE id_utilisateur = :id");
    $stmt_nav->execute(['id' => $_SESSION['id_utilisateur']]);
    $profil_nav = $stmt_nav->fetch();
    $photo_nav  = $profil_nav['photo'] ?? '';
} catch (Exception $e) { $photo_nav = ''; }

$liens = [
    'decouvrir'  => ['url'=>'decouvrir.php',     'icon'=>'💝', 'label'=>'Découvrir'],
    'evenements' => ['url'=>'evenement.php',      'icon'=>'📅', 'label'=>'Événements'],
    'messagerie' => ['url'=>'messagerie.php',     'icon'=>'💬', 'label'=>'Messages'],
    'profil'     => ['url'=>'modifier_profil.php','icon'=>'👤', 'label'=>'Mon profil'],
];
?>
<header class="pc-header">
    <span class="pc-logo">OLDating 💕</span>
    <nav style="display:flex;gap:4px;margin-left:32px;">
        <?php foreach ($liens as $key => $l): ?>
            <a href="<?= $l['url'] ?>" class="nav-item <?= $actif===$key?'actif':'' ?>" style="padding:8px 16px;">
                <span class="nav-icon"><?= $l['icon'] ?></span>
                <?= $l['label'] ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="pc-header-right">
        <div class="pc-user-badge">
            <div class="pc-avatar-sm">
                <?php if (!empty($photo_nav)): ?>
                    <img src="<?= htmlspecialchars($photo_nav) ?>" alt="">
                <?php else: ?>
                    <span style="font-size:.9em;">👤</span>
                <?php endif; ?>
            </div>
            <?= htmlspecialchars($pseudo_nav) ?>
        </div>
        <form action="deconnexion.php" method="post" style="margin:0;">
            <button type="submit" class="btn btn-outline btn-sm">🚪 Déco</button>
        </form>
    </div>
</header>

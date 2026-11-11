<?php
$me        = $auth->user();
$character = $characters->find((int) ($_GET['id'] ?? 0));
$isOwner   = $character && $character['user_id'] === $me['id'] && $character['deleted_at'] === null;
$staffView = $groupManager->userCan($me, 'view-candidatures', 'manage-candidatures');

if ($isOwner) {
    $history = $characters->candidaturesFor((int) $character['id']);
    // Discussion affichée : la candidature demandée (historique) ou la plus récente
    $candId  = isset($_GET['cand']) && in_array((int) $_GET['cand'], array_map('intval', array_column($history, 'id')), true)
        ? (int) $_GET['cand'] : (int) ($history[0]['id'] ?? 0);
    $cand     = $candId ? $characters->findCandidature($candId) : false;
    $messages = $cand ? $characters->messages($candId) : [];
    $latest   = $history[0] ?? null;

    $job       = CharacterManager::JOBS[$character['job']] ?? CharacterManager::JOBS['Autre'];
    $level     = CharacterManager::LEVELS[$character['level']];
    $validated = $character['sheet'] !== null;
    $color     = $validated ? $job['color'] : '#4b5563';
    $csrf      = Auth::generateCsrfToken();
    $flash     = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    // Version soumise différente de la version validée (modification en attente ou refusée)
    $submitted = $cand && $cand['status'] !== 'accepted' && (int) $cand['id'] === (int) ($latest['id'] ?? 0) ? $cand['sheet'] : null;
}

$e    = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
$date = fn($v) => $v ? date('d/m/Y à H:i', strtotime($v)) : '—';
?>
<style>
    .character-initial {
        width: 64px; height: 64px; flex-shrink: 0;
        background: rgba(34, 211, 238, 0.08);
        border: 1px solid rgba(34, 211, 238, 0.25);
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 1.6rem;
        font-family: 'JetBrains Mono', monospace;
    }
    .cand-step { display: flex; justify-content: space-between; align-items: center; gap: 0.75rem; padding: 0.6rem 0; border-bottom: 1px solid #ffffff0d; font-size: 0.8rem; }
    .cand-step:last-child { border-bottom: none; }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 md:pb-40 fade-in-view">
        <a href="./dashboard?page=personnages" class="inline-flex items-center gap-2 text-gray-400 hover:text-cyan-400 transition-colors mb-6 text-sm uppercase tracking-widest">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour à vos personnages
        </a>

<?php if (!$isOwner): ?>
        <h3 class="pb-4 text-cyan-400">Personnage introuvable</h3>
        <p class="text-sm text-gray-500">Ce personnage n'existe pas ou ne vous appartient pas.</p>
        <?php if ($character && $staffView && ($last = $characters->latestCandidature((int) $character['id']))): ?>
        <a href="./dashboard?page=candidature-staff&id=<?= (int) $last['id'] ?>" class="origin-btn btn--graphic btn--primary mt-6">
            <span>Ouvrir la candidature (staff)</span><i data-lucide="shield" class="btn--icon"></i>
        </a>
        <?php endif; ?>
<?php else: ?>
        <!-- En-tête -->
        <div class="card-glass-panel mb-6" style="display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap; border-left: 4px solid <?= $e($color) ?>;">
            <div class="character-initial" style="color: <?= $e($color) ?>; border-color: <?= $e($color) ?>44;"><?= $e(mb_strtoupper(mb_substr($character['name'], 0, 1))) ?></div>
            <div style="flex: 1; min-width: 200px;">
                <h3 class="text-white mb-2"><?= $e($character['name']) ?></h3>
                <div class="flex flex-wrap gap-2">
                    <div class="badge-glass badge-glass--<?= $job['badge'] ?>"><span class="badge-glass--text"><?= $e($character['job']) ?></span></div>
                    <div class="badge-glass badge-glass--<?= $level['badge'] ?>"><span class="badge-glass--text"><?= $level['label'] ?></span></div>
                    <?php if ($validated): ?>
                    <div class="badge-glass badge-glass--<?= $character['active'] ? 'success' : 'danger' ?>"><span class="badge-glass--text"><?= $character['active'] ? 'Actif' : 'Inactif' ?></span></div>
                    <?php endif; ?>
                </div>
                <p class="text-xs text-gray-500 mt-2">Créé le <?= $date($character['created_at']) ?><?= $validated ? ' — validé le ' . $date($character['validated_at']) : '' ?></p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="./dashboard?page=personnage-form&id=<?= (int) $character['id'] ?>" class="origin-btn btn--graphic btn--primary text-sm">
                    <i data-lucide="pen" class="btn--icon"></i> Modifier
                </a>
                <?php if ($validated): ?>
                <form method="POST" action="assets/handlers/character-action">
                    <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
                    <input type="hidden" name="action" value="set_active">
                    <input type="hidden" name="return" value="character">
                    <input type="hidden" name="character_id" value="<?= (int) $character['id'] ?>">
                    <input type="hidden" name="active" value="<?= $character['active'] ? '0' : '1' ?>">
                    <button type="submit" class="origin-btn btn--graphic btn--secondary text-sm">
                        <i data-lucide="<?= $character['active'] ? 'pause' : 'play' ?>" class="btn--icon"></i> <?= $character['active'] ? 'Passer inactif' : 'Passer actif' ?>
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($flash): ?>
        <p class="text-sm mb-6 <?= $flash['type'] === 'success' ? 'text-cyan-400' : 'text-red-400' ?>"><?= $e($flash['message']) ?></p>
        <?php endif; ?>

        <div class="grid gap-8 xl:grid-cols-3">
            <div class="xl:col-span-2 min-w-0">
                <?php if ($validated): ?>
                <h4 class="pb-4">Fiche validée</h4>
                <?php $sheetData = $character['sheet']; $compareSheet = null; include __DIR__ . '/../elements/character-sheet.php'; ?>
                <?php endif; ?>

                <?php if ($submitted): ?>
                <?php if ($validated): ?>
                <details class="mb-8">
                    <summary class="text-sm text-yellow-400 cursor-pointer pb-4">Voir la version soumise (<?= $cand['status'] === 'refused' ? 'refusée' : 'en cours d\'examen' ?>) — champs modifiés surlignés</summary>
                    <?php $sheetData = $submitted; $compareSheet = $character['sheet']; include __DIR__ . '/../elements/character-sheet.php'; ?>
                </details>
                <?php else: ?>
                <h4 class="pb-4">Fiche soumise</h4>
                <?php $sheetData = $submitted; $compareSheet = null; include __DIR__ . '/../elements/character-sheet.php'; ?>
                <?php endif; ?>
                <?php endif; ?>

                <?php if ($cand): ?>
                <h4 class="pb-4">Discussion avec le staff — candidature <?= CharacterManager::number($cand['id']) ?></h4>
                <?php $discCandidature = $cand; $discMessages = $messages; $discReturn = 'character'; include __DIR__ . '/../elements/candidature-discussion.php'; ?>
                <?php endif; ?>
            </div>

            <aside class="min-w-0">
                <!-- Suivi de la validation -->
                <section class="card-glass-panel mb-6" style="padding: 1.25rem;">
                    <h5 class="flex items-center gap-2 mb-4"><i data-lucide="clipboard-check" class="w-4 h-4 text-cyan-400"></i> Suivi de la validation</h5>
                    <?php if ($latest):
                        $st = CharacterManager::STATUSES[$latest['status']];
                    ?>
                    <div class="flex items-center gap-2 mb-3 flex-wrap">
                        <div class="badge-glass badge-glass--<?= $st['badge'] ?>"><span class="badge-glass--text"><?= $st['label'] ?></span></div>
                        <span class="text-xs text-gray-400"><?= CharacterManager::TYPES[$latest['type']] ?> — déposée le <?= $date($latest['created_at']) ?></span>
                    </div>
                    <?php if ($latest['status'] === 'pending'): ?>
                    <p class="text-xs text-gray-400">Votre candidature est en cours d'examen par le staff. Surveillez la discussion : des questions peuvent vous être posées.</p>
                    <?php elseif ($latest['decided_at']): ?>
                    <p class="text-xs text-gray-400 mb-2">Décision le <?= $date($latest['decided_at']) ?><?= $latest['decided_by'] ? ' par ' . $e($latest['decided_by']) : '' ?>.</p>
                    <?php if ($latest['decision_comment']): ?>
                    <p class="text-sm <?= $latest['status'] === 'refused' ? 'text-red-400' : 'text-green-400' ?>" style="white-space: pre-line;"><?= $e($latest['decision_comment']) ?></p>
                    <?php endif; ?>
                    <?php if ($latest['status'] === 'refused'): ?>
                    <p class="text-xs text-gray-400 mt-3">Vous pouvez corriger la fiche avec « Modifier » puis la soumettre à nouveau.</p>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php endif; ?>
                </section>

                <!-- Historique des candidatures -->
                <section class="card-glass-panel" style="padding: 1.25rem;">
                    <h5 class="flex items-center gap-2 mb-4"><i data-lucide="history" class="w-4 h-4 text-cyan-400"></i> Historique des candidatures</h5>
                    <?php foreach ($history as $h):
                        $hs = CharacterManager::STATUSES[$h['status']];
                    ?>
                    <div class="cand-step">
                        <a href="./dashboard?page=personnage&id=<?= (int) $character['id'] ?>&cand=<?= (int) $h['id'] ?>" class="<?= (int) $h['id'] === $candId ? 'text-cyan-400' : '' ?>">
                            <?= CharacterManager::number($h['id']) ?> — <?= CharacterManager::TYPES[$h['type']] ?>
                            <span class="text-gray-600 block text-xs"><?= date('d/m/Y', strtotime($h['created_at'])) ?></span>
                        </a>
                        <div class="badge-glass badge-glass--<?= $hs['badge'] ?>"><span class="badge-glass--text"><?= $hs['label'] ?></span></div>
                    </div>
                    <?php endforeach; ?>
                </section>
            </aside>
        </div>
<?php endif; ?>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<script>
    lucide.createIcons();
</script>

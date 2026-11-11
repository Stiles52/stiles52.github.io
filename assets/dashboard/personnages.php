<?php
$me      = $auth->user();
$list    = $characters->listForUser($me['id']);
$pre     = $characters->prerequisites($me['id']);
$canAdd  = $pre['count'] < $pre['max'];
$csrf    = Auth::generateCsrfToken();
$flash   = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);

/** Badge de statut d'un personnage (validation + Actif/Inactif). */
$charStatus = function (array $c): array {
    if ($c['sheet'] === null) {
        return $c['cand_status'] === 'refused' ? ['danger', 'Refusée'] : ['warning', 'En validation'];
    }
    return $c['active'] ? ['success', 'Actif'] : ['danger', 'Inactif'];
};
?>
<style>
    .character-card {
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(34, 211, 238, 0.15);
        border-left: 4px solid rgba(34, 211, 238, 0.4);
        padding: 1.25rem 1.5rem;
        display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        transition: border-color 0.2s, background 0.2s;
    }
    .character-card:hover { background: rgba(34, 211, 238, 0.03); border-left-color: #22d3ee; }
    .character-card--info { display: flex; align-items: center; gap: 1.25rem; flex: 1; flex-wrap: wrap; }
    .character-initial {
        width: 48px; height: 48px; flex-shrink: 0;
        background: rgba(34, 211, 238, 0.08);
        border: 1px solid rgba(34, 211, 238, 0.25);
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; color: #22d3ee; font-size: 1.2rem;
        font-family: 'JetBrains Mono', monospace;
    }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 md:pb-40 fade-in-view">

        <div class="flex items-center justify-between pb-12" style="flex-wrap: wrap; gap: 1rem;">
            <h3 class="text-cyan-400">Gestion des personnages</h3>
            <?php if ($canAdd): ?>
            <a href="./dashboard?page=personnage-form" class="origin-btn btn--full-graphic btn--success">
                Ajouter un personnage
                <i data-lucide="plus" class="btn--icon"></i>
            </a>
            <?php else: ?>
            <span class="origin-btn btn--graphic btn--secondary" style="cursor: not-allowed;" title="Limite de personnages atteinte — contactez le staff pour une exception">
                Limite atteinte (<?= $pre['count'] ?> / <?= $pre['max'] ?>)
                <i data-lucide="lock" class="btn--icon"></i>
            </span>
            <?php endif; ?>
        </div>

        <?php if ($flash): ?>
        <p class="text-sm mb-6 <?= $flash['type'] === 'success' ? 'text-cyan-400' : 'text-red-400' ?>"><?= $e($flash['message']) ?></p>
        <?php endif; ?>

        <!-- Barre de recherche -->
        <div class="page-toolbar" style="margin-bottom: 1.5rem;">
            <div class="page-toolbar--search">
                <input type="text" id="char-search" placeholder="Rechercher un personnage...">
                <button onclick="searchChars()" class="origin-btn btn--graphic btn--primary" style="flex-shrink:0;">
                    <i data-lucide="search" class="btn--icon"></i>
                </button>
            </div>
            <span class="text-xs text-gray-500 uppercase tracking-widest"><?= $pre['count'] ?> / <?= $pre['max'] ?> personnage<?= $pre['max'] > 1 ? 's' : '' ?></span>
        </div>

        <!-- Liste -->
        <div id="characters-list" style="display: flex; flex-direction: column; gap: 0.75rem;">
            <?php foreach ($list as $c):
                $job   = CharacterManager::JOBS[$c['job']] ?? CharacterManager::JOBS['Autre'];
                $level = CharacterManager::LEVELS[$c['level']];
                [$stBadge, $stLabel] = $charStatus($c);
                $validated = $c['sheet'] !== null;
                $color     = $validated ? $job['color'] : '#4b5563';
                $viewUrl   = './dashboard?page=personnage&id=' . (int) $c['id'];
            ?>
            <div class="character-card" style="border-left-color: <?= $e($color) ?>;">
                <div class="character-card--info">
                    <div class="character-initial" style="color: <?= $e($color) ?>; border-color: <?= $e($color) ?>44;"><?= $e(mb_strtoupper(mb_substr($c['name'], 0, 1))) ?></div>
                    <div>
                        <a href="<?= $e($viewUrl) ?>" class="text-base font-bold text-white mb-2 block"><?= $e($c['name']) ?></a>
                        <div class="flex flex-wrap gap-2">
                            <div class="badge-glass badge-glass--<?= $job['badge'] ?>"><span class="badge-glass--text"><?= $e($c['job']) ?></span></div>
                            <div class="badge-glass badge-glass--<?= $level['badge'] ?>"><span class="badge-glass--text"><?= $level['label'] ?></span></div>
                            <div class="badge-glass badge-glass--<?= $stBadge ?>"><span class="badge-glass--text"><?= $stLabel ?></span></div>
                            <?php if ($validated && $c['cand_type'] === 'modification' && $c['cand_status'] === 'pending'): ?>
                            <div class="badge-glass badge-glass--warning"><span class="badge-glass--text">Modification en attente</span></div>
                            <?php elseif ($validated && $c['cand_type'] === 'modification' && $c['cand_status'] === 'refused'): ?>
                            <div class="badge-glass badge-glass--danger"><span class="badge-glass--text">Modification refusée</span></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs text-gray-600">Créé le <?= date('d/m/Y', strtotime($c['created_at'])) ?></span>
                    <button type="button" class="dropdown">
                        <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                        <div class="dropdown-container dropdown-container--popup hidden">
                            <a href="<?= $e($viewUrl) ?>"><i data-lucide="eye" class="btn--icon"></i>Voir la fiche</a>
                            <a href="./dashboard?page=personnage-form&id=<?= (int) $c['id'] ?>"><i data-lucide="pen" class="btn--icon"></i>Modifier</a>
                            <?php if ($validated): ?>
                            <a href="#" onclick="charAction('set_active', <?= (int) $c['id'] ?>, '<?= $c['active'] ? '0' : '1' ?>'); return false;">
                                <i data-lucide="<?= $c['active'] ? 'pause' : 'play' ?>" class="btn--icon"></i><?= $c['active'] ? 'Passer inactif' : 'Passer actif' ?>
                            </a>
                            <?php endif; ?>
                            <a href="#" onclick="deleteChar(<?= (int) $c['id'] ?>, <?= $e(json_encode($c['name'])) ?>); return false;" class="text-red-400"><i data-lucide="trash-2" class="btn--icon"></i>Supprimer</a>
                        </div>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div id="empty-state" class="<?= $list ? 'hidden' : '' ?> text-center py-24">
            <i data-lucide="theater" class="w-14 h-14 text-gray-700 mx-auto mb-5"></i>
            <p class="text-gray-600 text-xs uppercase tracking-widest m-0">Aucun personnage enregistré</p>
        </div>

        <form id="char-action-form" method="POST" action="assets/handlers/character-action" class="hidden">
            <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
            <input type="hidden" name="return" value="list">
            <input type="hidden" name="action">
            <input type="hidden" name="character_id">
            <input type="hidden" name="active">
        </form>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<script>
    lucide.createIcons();

    function charAction(action, id, active) {
        const form = document.getElementById('char-action-form');
        form.elements.action.value = action;
        form.elements.character_id.value = id;
        form.elements.active.value = active ?? '';
        form.submit();
    }

    function deleteChar(id, name) {
        if (!confirm('Supprimer le personnage « ' + name + ' » ? Une candidature en attente sera retirée.')) return;
        charAction('delete', id);
    }

    function searchChars() {
        const query = document.getElementById('char-search').value.toLowerCase();
        document.querySelectorAll('#characters-list .character-card').forEach(card => {
            card.style.display = card.textContent.toLowerCase().includes(query) ? '' : 'none';
        });
    }

    document.getElementById('char-search').addEventListener('keydown', e => {
        if (e.key === 'Enter') searchChars();
    });
</script>

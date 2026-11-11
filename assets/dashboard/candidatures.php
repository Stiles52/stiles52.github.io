<?php
$canView   = $groupManager->userCan($auth->user(), 'view-candidatures', 'manage-candidatures');
$canDecide = $groupManager->userCan($auth->user(), 'manage-candidatures');

if ($canView) {
    $filters = [
        'q'      => trim((string) ($_GET['q'] ?? '')),
        'status' => isset(CharacterManager::STATUSES[$_GET['status'] ?? '']) ? $_GET['status'] : '',
        'type'   => isset(CharacterManager::TYPES[$_GET['type'] ?? '']) ? $_GET['type'] : '',
        'sort'   => in_array($_GET['sort'] ?? '', ['recent', 'oldest', 'alpha'], true) ? $_GET['sort'] : 'recent',
    ];
    $result = $characters->listAll($filters, (int) ($_GET['p'] ?? 1));
    $stats  = $characters->stats();
    $csrf   = Auth::generateCsrfToken();
    $flash  = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    $activeFilters = ($filters['status'] !== '') + ($filters['type'] !== '') + ($filters['sort'] !== 'recent');
    $query         = array_filter($filters, fn($v, $k) => $v !== '' && !($k === 'sort' && $v === 'recent'), ARRAY_FILTER_USE_BOTH);
    $returnQuery   = http_build_query($query + ($result['page'] > 1 ? ['p' => $result['page']] : []));
    $pageUrl       = fn(int $p) => './dashboard?' . http_build_query(['page' => 'candidatures'] + $query + ($p > 1 ? ['p' => $p] : []));
    $e             = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
    $n             = fn(int $v) => number_format($v, 0, ',', ' ');
}
?>
<style>
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.85);
        z-index: 100; display: flex; align-items: center; justify-content: center;
    }
    .modal-box {
        background: rgba(0,0,0,0.95);
        border: 1px solid rgba(34, 211, 238, 0.3);
        border-top: 4px solid #22d3ee;
        padding: 2rem; width: 520px; max-width: 95vw;
    }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 md:pb-40 fade-in-view">
        <h3 class="pb-4 text-cyan-400">Gestion des candidatures</h3>
<?php if (!$canView): ?>
        <p class="text-sm text-red-400 pt-8">Vous n'avez pas la permission de consulter les candidatures.</p>
<?php else: ?>
        <p class="text-gray-500 text-xs uppercase tracking-widest pb-12">Candidatures de personnages — accès au serveur OriginRP</p>

        <?php if ($flash): ?>
        <p class="text-sm mb-6 <?= $flash['type'] === 'success' ? 'text-cyan-400' : 'text-red-400' ?>"><?= $e($flash['message']) ?></p>
        <?php endif; ?>

        <!-- Stats -->
        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 2rem;">
            <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-yellow-400 mb-1"><?= $n($stats['pending']) ?></div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">En attente</div>
            </div>
            <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-green-400 mb-1"><?= $n($stats['accepted']) ?></div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Acceptées</div>
            </div>
            <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-red-400 mb-1"><?= $n($stats['refused']) ?></div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Refusées</div>
            </div>
            <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-cyan-400 mb-1"><?= $n($stats['total']) ?></div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Total</div>
            </div>
        </div>

        <form method="GET" action="./dashboard">
            <input type="hidden" name="page" value="candidatures">
            <!-- Outils -->
            <div class="page-toolbar">
                <div class="page-toolbar--search">
                    <input type="text" name="q" id="cand-search" value="<?= $e($filters['q']) ?>" placeholder="Rechercher un pseudo, un personnage...">
                    <button type="submit" class="origin-btn btn--graphic btn--primary" style="flex-shrink:0;">
                        <i data-lucide="search" class="btn--icon"></i>
                    </button>
                </div>
                <div class="page-toolbar--actions">
                    <button type="button" class="dropdown origin-btn btn--full-graphic btn--primary">
                        Filtres<?= $activeFilters ? " ($activeFilters)" : '' ?>
                        <i data-lucide="sliders-horizontal" class="btn--icon"></i>
                    </button>
                </div>
            </div>
            <div class="dropdown-container <?= $activeFilters ? '' : 'hidden' ?> w-full flex p-8 gap-4">
                <div style="width: 350px;">
                    <label>Statut</label>
                    <select name="status" style="width: 100%;" onchange="this.form.submit()">
                        <option value="">Tous</option>
                        <?php foreach (CharacterManager::STATUSES as $key => $s): ?>
                        <option value="<?= $key ?>" <?= $filters['status'] === $key ? 'selected' : '' ?>><?= $s['label'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="width: 350px;">
                    <label>Type</label>
                    <select name="type" style="width: 100%;" onchange="this.form.submit()">
                        <option value="">Tous</option>
                        <?php foreach (CharacterManager::TYPES as $key => $lbl): ?>
                        <option value="<?= $key ?>" <?= $filters['type'] === $key ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="width: 350px;">
                    <label>Ordre d'apparition</label>
                    <select name="sort" style="width: 100%;" onchange="this.form.submit()">
                        <?php foreach (['recent' => 'Date (récent)', 'oldest' => 'Date (ancien)', 'alpha' => 'Alphabétique'] as $key => $lbl): ?>
                        <option value="<?= $key ?>" <?= $filters['sort'] === $key ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>

        <table id="cand-table">
            <thead>
                <tr>
                    <td width="80px">ID#</td>
                    <td width="180px">Pseudo Minecraft</td>
                    <td width="180px">Pseudo (site)</td>
                    <td width="160px">Date de dépôt</td>
                    <td width="150px">Statut</td>
                    <td>Commentaire</td>
                    <td width="80px">Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$result['rows']): ?>
                <tr>
                    <td colspan="7" class="text-center text-gray-500 py-8">Aucune candidature trouvée.</td>
                </tr>
                <?php endif; ?>

                <?php foreach ($result['rows'] as $c):
                    $st      = CharacterManager::STATUSES[$c['status']];
                    $number  = CharacterManager::number($c['id']);
                    $viewUrl = './dashboard?page=candidature-staff&id=' . (int) $c['id'];
                    $rowData = json_encode(['id' => (int) $c['id'], 'number' => $number, 'name' => $c['character_name'], 'author' => $c['author_name']]);
                ?>
                <tr>
                    <td class="text-xs text-gray-500"><?= $number ?></td>
                    <td class="text-cyan-400 font-mono text-sm"><?= $c['minecraft_pseudo'] ? $e($c['minecraft_pseudo']) : '<span class="text-gray-600">—</span>' ?></td>
                    <td>
                        <?= $e($c['author_name']) ?>
                        <div class="text-xs text-gray-500"><?= $e($c['character_name']) ?><?= $c['character_deleted'] ? ' (supprimé)' : '' ?></div>
                    </td>
                    <td class="text-xs text-gray-400"><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                    <td>
                        <div class="badge-glass badge-glass--<?= $st['badge'] ?>"><span class="badge-glass--text"><?= $st['label'] ?></span></div>
                        <?php if ($c['type'] === 'modification'): ?>
                        <div class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">Modification</div>
                        <?php endif; ?>
                    </td>
                    <td class="text-xs <?= $c['decision_comment'] ? 'text-gray-400' : 'text-gray-500' ?>"><?= $c['decision_comment'] ? $e($c['decision_comment']) : '—' ?></td>
                    <td>
                        <button type="button" class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="<?= $e($viewUrl) ?>">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir la candidature
                                </a>
                                <?php if ($canDecide && $c['status'] === 'pending'): ?>
                                <a href="#" class="text-green-400" onclick="openDecision(this, 'accepted'); return false;" data-cand="<?= $e($rowData) ?>">
                                    <i data-lucide="check" class="btn--icon"></i>
                                    Accepter
                                </a>
                                <a href="#" class="text-red-400" onclick="openDecision(this, 'refused'); return false;" data-cand="<?= $e($rowData) ?>">
                                    <i data-lucide="x" class="btn--icon"></i>
                                    Refuser
                                </a>
                                <?php elseif ($canDecide && in_array($c['status'], ['accepted', 'refused'], true) && $c['is_latest']): ?>
                                <a href="#" class="<?= $c['status'] === 'accepted' ? 'text-red-400' : 'text-green-400' ?>" onclick="cancelDecision(<?= (int) $c['id'] ?>, '<?= $number ?>'); return false;">
                                    <i data-lucide="rotate-ccw" class="btn--icon"></i>
                                    Annuler la décision
                                </a>
                                <?php endif; ?>
                            </div>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php
        $current = $result['page'];
        $last    = $result['pages'];
        include __DIR__ . '/../elements/pagination.php';
        ?>
<?php endif; ?>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<?php if ($canView && $canDecide): ?>
<!-- Modal décision -->
<div id="modal-decision" class="modal-overlay hidden">
    <form class="modal-box" method="POST" action="assets/handlers/character-action">
        <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
        <input type="hidden" name="action" value="decide">
        <input type="hidden" name="return" value="staff_list">
        <input type="hidden" name="return_query" value="<?= $e($returnQuery) ?>">
        <input type="hidden" name="candidature_id" id="decision-id">
        <input type="hidden" name="decision" id="decision-value">

        <h4 class="mb-6" id="decision-title">Décision</h4>
        <div class="w-full mb-8">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2" id="decision-label">Commentaire</label>
            <textarea name="comment" id="decision-comment" rows="3" maxlength="500" style="width: 100%;" class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white focus:border-cyan-500 focus:outline-none"></textarea>
            <p class="text-xs text-gray-500 mt-2">Le commentaire est visible par le joueur.</p>
        </div>
        <div class="flex gap-4">
            <button type="button" onclick="closeDecision()" class="origin-btn btn--graphic btn--secondary flex-1 justify-center">Annuler</button>
            <button type="submit" id="decision-submit" class="origin-btn btn--full-graphic flex-1 justify-center">
                <span>Confirmer</span>
            </button>
        </div>
    </form>
</div>

<form id="cancel-form" method="POST" action="assets/handlers/character-action" class="hidden">
    <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
    <input type="hidden" name="action" value="cancel">
    <input type="hidden" name="return" value="staff_list">
    <input type="hidden" name="return_query" value="<?= $e($returnQuery) ?>">
    <input type="hidden" name="candidature_id">
</form>
<?php endif; ?>

<script>
    lucide.createIcons();

<?php if ($canView && $canDecide): ?>
    const decisionModal = document.getElementById('modal-decision');

    function openDecision(link, decision) {
        const c       = JSON.parse(link.dataset.cand);
        const accept  = decision === 'accepted';
        const title   = document.getElementById('decision-title');
        const comment = document.getElementById('decision-comment');
        const submit  = document.getElementById('decision-submit');

        decisionModal.querySelector('form').reset();
        document.getElementById('decision-id').value = c.id;
        document.getElementById('decision-value').value = decision;
        title.textContent = (accept ? 'Accepter ' : 'Refuser ') + c.number + ' — ' + c.name + ' (' + c.author + ')';
        title.className = 'mb-6 ' + (accept ? 'text-green-400' : 'text-red-400');
        document.getElementById('decision-label').textContent = accept ? 'Commentaire (optionnel)' : 'Motif du refus (obligatoire)';
        comment.required = !accept;
        comment.minLength = accept ? 0 : 3;
        submit.className = 'origin-btn btn--full-graphic flex-1 justify-center ' + (accept ? 'btn--success' : 'btn--danger');
        decisionModal.classList.remove('hidden');
        comment.focus();
    }

    function closeDecision() {
        decisionModal.classList.add('hidden');
    }
    decisionModal.addEventListener('click', function (e) {
        if (e.target === this) closeDecision();
    });

    function cancelDecision(id, number) {
        if (!confirm('Annuler la décision de la candidature ' + number + ' ? Elle repassera en attente.')) return;
        const form = document.getElementById('cancel-form');
        form.elements.candidature_id.value = id;
        form.submit();
    }
<?php endif; ?>
</script>

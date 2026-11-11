<?php
$canManageTickets = $groupManager->userCan($auth->user(), 'manage-tickets');

if ($canManageTickets) {
    $me    = $auth->user();
    $staff = $tickets->staffMembers();

    $filters = [
        'q'        => trim((string) ($_GET['q'] ?? '')),
        'status'   => isset(TicketManager::STAFF_STATUSES[$_GET['status'] ?? '']) ? $_GET['status'] : '',
        'category' => isset(TicketManager::CATEGORIES[$_GET['category'] ?? '']) ? $_GET['category'] : '',
        'assignee' => (string) ($_GET['assignee'] ?? ''),
        'sort'     => isset(TicketManager::SORTS[$_GET['sort'] ?? '']) ? $_GET['sort'] : 'recent',
    ];
    if ($filters['assignee'] !== 'none' && !in_array($filters['assignee'], array_column($staff, 'id'), true)) {
        $filters['assignee'] = '';
    }

    $result = $tickets->listAll($filters, (int) ($_GET['p'] ?? 1));
    $stats  = $tickets->staffStats();
    $csrf   = Auth::generateCsrfToken();
    $flash  = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    $activeFilters = ($filters['status'] !== '') + ($filters['category'] !== '') + ($filters['assignee'] !== '') + ($filters['sort'] !== 'recent');
    $query         = array_filter($filters, fn($v, $k) => $v !== '' && !($k === 'sort' && $v === 'recent'), ARRAY_FILTER_USE_BOTH);
    $returnQuery   = http_build_query($query + ($result['page'] > 1 ? ['p' => $result['page']] : []));
    $pageUrl       = fn(int $p) => './dashboard?' . http_build_query(['page' => 'manage-tickets'] + $query + ($p > 1 ? ['p' => $p] : []));
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
    .modal-box input, .modal-box select { width: 100%; }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 md:pb-40 fade-in-view">
        <h3 class="pb-4 text-cyan-400">Gestion des tickets</h3>
<?php if (!$canManageTickets): ?>
        <p class="text-sm text-red-400 pt-8">Vous n'avez pas la permission de gérer les tickets.</p>
<?php else: ?>
        <p class="text-gray-500 text-xs uppercase tracking-widest pb-12">Vue staff — tous les tickets toutes catégories confondues</p>

        <?php if ($flash): ?>
        <p class="text-sm mb-6 <?= $flash['type'] === 'success' ? 'text-cyan-400' : 'text-red-400' ?>"><?= $e($flash['message']) ?></p>
        <?php endif; ?>

        <!-- Stats rapides -->
        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 2rem;">
            <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-cyan-400 mb-1"><?= $n($stats['unassigned']) ?></div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Non assignés</div>
            </div>
            <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-orange-400 mb-1"><?= $n($stats['in_progress']) ?></div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">En cours</div>
            </div>
            <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-yellow-400 mb-1"><?= $n($stats['waiting']) ?></div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">En attente</div>
            </div>
            <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-green-400 mb-1"><?= $n($stats['resolved']) ?></div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Résolus</div>
            </div>
        </div>

        <form method="GET" action="./dashboard">
            <input type="hidden" name="page" value="manage-tickets">

            <!-- Barre d'outils -->
            <div class="page-toolbar">
                <div class="page-toolbar--search">
                    <input type="text" name="q" id="ticket-search" value="<?= $e($filters['q']) ?>" placeholder="Rechercher un ticket, un auteur...">
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
                        <?php foreach (TicketManager::STAFF_STATUSES as $key => $s): ?>
                        <option value="<?= $key ?>" <?= $filters['status'] === $key ? 'selected' : '' ?>><?= $e($s['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="width: 350px;">
                    <label>Catégorie</label>
                    <select name="category" style="width: 100%;" onchange="this.form.submit()">
                        <option value="">Toutes</option>
                        <?php foreach (TicketManager::CATEGORIES as $key => $c): ?>
                        <option value="<?= $key ?>" <?= $filters['category'] === $key ? 'selected' : '' ?>><?= $e($c['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="width: 350px;">
                    <label>Assigné à</label>
                    <select name="assignee" style="width: 100%;" onchange="this.form.submit()">
                        <option value="">Tous</option>
                        <?php foreach ($staff as $m): ?>
                        <option value="<?= $e($m['id']) ?>" <?= $filters['assignee'] === $m['id'] ? 'selected' : '' ?>><?= $e($m['pseudo']) ?></option>
                        <?php endforeach; ?>
                        <option value="none" <?= $filters['assignee'] === 'none' ? 'selected' : '' ?>>— Non assigné</option>
                    </select>
                </div>
                <div style="width: 350px;">
                    <label>Ordre d'apparition</label>
                    <select name="sort" style="width: 100%;" onchange="this.form.submit()">
                        <?php foreach (TicketManager::SORTS as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $filters['sort'] === $key ? 'selected' : '' ?>><?= $e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>

        <table id="manage-tickets-table">
            <thead>
                <tr>
                    <td width="90px">ID#</td>
                    <td width="140px">Statut</td>
                    <td width="200px">Auteur</td>
                    <td>Titre</td>
                    <td width="150px">Catégorie</td>
                    <td width="160px">Assigné à</td>
                    <td width="80px">Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$result['rows']): ?>
                <tr>
                    <td colspan="7" class="text-center text-gray-500 py-8">Aucun ticket trouvé.</td>
                </tr>
                <?php endif; ?>

                <?php foreach ($result['rows'] as $t):
                    $status   = TicketManager::STAFF_STATUSES[TicketManager::staffStatus($t)];
                    $category = TicketManager::CATEGORIES[$t['category']] ?? ['label' => $t['category'], 'badge' => 'secondary'];
                    $isOpen   = $tickets->isOpen($t);
                    $number   = TicketManager::number($t['id']);
                    $rowData  = json_encode([
                        'id'       => (int) $t['id'],
                        'number'   => $number,
                        'title'    => $t['title'],
                        'category' => $t['category'],
                        'priority' => $t['priority'] ?? '',
                        'assignee' => $t['assigned_to'] ?? '',
                    ]);
                ?>
                <tr>
                    <td class="text-xs text-gray-500"><?= $number ?></td>
                    <td><div class="badge-glass badge-glass--<?= $status['badge'] ?>"><span class="badge-glass--text"><?= $e($status['label']) ?></span></div></td>
                    <td class="text-sm"><?= $e($t['author_name']) ?></td>
                    <td class="text-sm"><a href="./dashboard?page=ticket-staff&id=<?= (int) $t['id'] ?>"><?= $e($t['title']) ?></a></td>
                    <td><div class="badge-glass badge-glass--<?= $e($category['badge']) ?>"><span class="badge-glass--text"><?= $e($category['label']) ?></span></div></td>
                    <td>
                        <?php if ($t['assignee_name']): ?>
                        <span class="text-xs text-cyan-400"><?= $e($t['assignee_name']) ?></span>
                        <?php else: ?>
                        <span class="text-xs text-gray-500">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button type="button" class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="./dashboard?page=ticket-staff&id=<?= (int) $t['id'] ?>">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir le ticket
                                </a>
                                <?php if ($isOpen): ?>
                                    <?php if ($t['assigned_to'] === null): ?>
                                    <a href="#" onclick="ticketAction('assign_self', <?= (int) $t['id'] ?>); return false;">
                                        <i data-lucide="user-check" class="btn--icon"></i>
                                        S'assigner
                                    </a>
                                    <?php else: ?>
                                    <a href="#" onclick="openAssignModal(this); return false;" data-ticket="<?= $e($rowData) ?>">
                                        <i data-lucide="user-check" class="btn--icon"></i>
                                        Réassigner
                                    </a>
                                    <?php endif; ?>
                                <a href="#" onclick="openEditModal(this); return false;" data-ticket="<?= $e($rowData) ?>">
                                    <i data-lucide="pen" class="btn--icon"></i>
                                    Éditer
                                </a>
                                <a href="#" onclick="ticketAction('close', <?= (int) $t['id'] ?>, 'Marquer le ticket <?= $number ?> comme résolu ?'); return false;">
                                    <i data-lucide="ticket-x" class="btn--icon"></i>
                                    Fermer
                                </a>
                                <?php else: ?>
                                <a href="#" onclick="ticketAction('reopen', <?= (int) $t['id'] ?>, 'Rouvrir le ticket <?= $number ?> ?'); return false;">
                                    <i data-lucide="rotate-ccw" class="btn--icon"></i>
                                    Rouvrir
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

<?php if ($canManageTickets): ?>
<!-- Modal édition -->
<div id="modal-edit" class="modal-overlay hidden">
    <form class="modal-box" method="POST" action="assets/handlers/ticket-action">
        <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
        <input type="hidden" name="action" value="staff_update">
        <input type="hidden" name="return" value="manage">
        <input type="hidden" name="return_query" value="<?= $e($returnQuery) ?>">
        <input type="hidden" name="ticket_id" id="edit-ticket-id">

        <h4 class="text-cyan-400 mb-6" id="edit-title">Modifier le ticket</h4>
        <div class="w-full mb-6">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Titre</label>
            <input type="text" name="subject" id="edit-subject" required minlength="5" maxlength="150">
        </div>
        <div class="w-full mb-6">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Catégorie</label>
            <select name="category" id="edit-category">
                <?php foreach (TicketManager::CATEGORIES as $key => $c): ?>
                <option value="<?= $key ?>"><?= $e($c['label']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-full mb-8">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Priorité</label>
            <select name="priority" id="edit-priority">
                <option value="">Aucune</option>
                <?php foreach (TicketManager::PRIORITIES as $key => $pr): ?>
                <option value="<?= $key ?>"><?= $e($pr['label']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex gap-4">
            <button type="button" onclick="closeModals()" class="origin-btn btn--graphic btn--secondary flex-1 justify-center">Annuler</button>
            <button type="submit" class="origin-btn btn--full-graphic btn--success flex-1 justify-center">
                <span>Enregistrer</span>
                <i data-lucide="save" class="btn--icon"></i>
            </button>
        </div>
    </form>
</div>

<!-- Modal réassignation -->
<div id="modal-assign" class="modal-overlay hidden">
    <form class="modal-box" method="POST" action="assets/handlers/ticket-action">
        <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
        <input type="hidden" name="action" value="assign">
        <input type="hidden" name="return" value="manage">
        <input type="hidden" name="return_query" value="<?= $e($returnQuery) ?>">
        <input type="hidden" name="ticket_id" id="assign-ticket-id">

        <h4 class="text-cyan-400 mb-6" id="assign-title">Réassigner le ticket</h4>
        <div class="w-full mb-8">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Assigné à</label>
            <select name="assignee" id="assign-select">
                <option value="">— Non assigné</option>
                <?php foreach ($staff as $m): ?>
                <option value="<?= $e($m['id']) ?>"><?= $e($m['pseudo']) ?><?= $m['id'] === $me['id'] ? ' (moi)' : '' ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex gap-4">
            <button type="button" onclick="closeModals()" class="origin-btn btn--graphic btn--secondary flex-1 justify-center">Annuler</button>
            <button type="submit" class="origin-btn btn--full-graphic btn--success flex-1 justify-center">
                <span>Enregistrer</span>
                <i data-lucide="user-check" class="btn--icon"></i>
            </button>
        </div>
    </form>
</div>

<!-- Actions rapides (s'assigner, fermer, rouvrir) -->
<form id="ticket-action-form" method="POST" action="assets/handlers/ticket-action" class="hidden">
    <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
    <input type="hidden" name="return" value="manage">
    <input type="hidden" name="return_query" value="<?= $e($returnQuery) ?>">
    <input type="hidden" name="action">
    <input type="hidden" name="ticket_id">
</form>
<?php endif; ?>

<script>
    lucide.createIcons();

<?php if ($canManageTickets): ?>
    const modals = [document.getElementById('modal-edit'), document.getElementById('modal-assign')];

    function closeModals() {
        modals.forEach(m => m.classList.add('hidden'));
    }
    modals.forEach(m => m.addEventListener('click', function (e) {
        if (e.target === this) closeModals();
    }));

    function openEditModal(link) {
        const t = JSON.parse(link.dataset.ticket);
        document.getElementById('edit-title').textContent = 'Modifier le ticket ' + t.number;
        document.getElementById('edit-ticket-id').value = t.id;
        document.getElementById('edit-subject').value = t.title;
        document.getElementById('edit-category').value = t.category;
        document.getElementById('edit-priority').value = t.priority;
        document.getElementById('modal-edit').classList.remove('hidden');
    }

    function openAssignModal(link) {
        const t = JSON.parse(link.dataset.ticket);
        document.getElementById('assign-title').textContent = 'Réassigner le ticket ' + t.number;
        document.getElementById('assign-ticket-id').value = t.id;
        document.getElementById('assign-select').value = t.assignee;
        document.getElementById('modal-assign').classList.remove('hidden');
    }

    function ticketAction(action, id, confirmMessage) {
        if (confirmMessage && !confirm(confirmMessage)) return;
        const form = document.getElementById('ticket-action-form');
        form.elements.action.value = action;
        form.elements.ticket_id.value = id;
        form.submit();
    }
<?php endif; ?>
</script>

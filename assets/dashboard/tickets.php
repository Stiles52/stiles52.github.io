<?php
$canViewTickets = $groupManager->userCan($auth->user(), 'view-own-tickets');

if ($canViewTickets) {
    $me      = $auth->user();
    $isStaff = $tickets->isStaff($me, $groupManager);
    $filters = [
        'q'        => trim((string) ($_GET['q'] ?? '')),
        'status'   => isset(TicketManager::STATUSES[$_GET['status'] ?? '']) ? $_GET['status'] : '',
        'priority' => isset(TicketManager::PRIORITIES[$_GET['priority'] ?? '']) ? $_GET['priority'] : '',
        'sort'     => isset(TicketManager::SORTS[$_GET['sort'] ?? '']) ? $_GET['sort'] : 'recent',
    ];
    $result = $tickets->listForUser($me, $filters, (int) ($_GET['p'] ?? 1));
    $csrf   = Auth::generateCsrfToken();
    $flash  = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    $activeFilters = ($filters['status'] !== '') + ($filters['priority'] !== '') + ($filters['sort'] !== 'recent');
    $query         = array_filter($filters, fn($v, $k) => $v !== '' && !($k === 'sort' && $v === 'recent'), ARRAY_FILTER_USE_BOTH);
    $pageUrl       = fn(int $p) => './dashboard?' . http_build_query(['page' => 'tickets'] + $query + ($p > 1 ? ['p' => $p] : []));
    $e             = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
}
?>
<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">

    <div id="view-history" class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">
        <h3 class="pb-12 text-cyan-400">Gérer vos tickets</h3>

<?php if (!$canViewTickets): ?>
        <p class="text-sm text-red-400">Vous n'avez pas la permission de consulter vos tickets.</p>
<?php else: ?>
        <?php if ($flash): ?>
        <p class="text-sm mb-4 <?= $flash['type'] === 'success' ? 'text-cyan-400' : 'text-red-400' ?>"><?= $e($flash['message']) ?></p>
        <?php endif; ?>

        <div class="mt-8">
            <form method="GET" action="./dashboard">
                <input type="hidden" name="page" value="tickets">
                <div class="page-toolbar">
                    <div class="page-toolbar--search">
                        <input type="text" name="q" id="ticket-search" value="<?= $e($filters['q']) ?>" placeholder="Rechercher un ticket...">
                        <button type="submit" class="origin-btn btn--graphic btn--primary" style="flex-shrink:0;">
                            <i data-lucide="search" class="btn--icon"></i>
                        </button>
                    </div>
                    <div class="page-toolbar--actions">
                        <button type="button" class="dropdown origin-btn btn--full-graphic btn--primary">
                            Filtres<?= $activeFilters ? " ($activeFilters)" : '' ?>
                            <i data-lucide="sliders-horizontal" class="btn--icon"></i>
                        </button>
                        <a href="./dashboard?page=ticket-actions" class="origin-btn btn--full-graphic btn--success">
                            Créer un ticket
                            <i data-lucide="circle-plus" class="btn--icon"></i>
                        </a>
                    </div>
                </div>
                <div class="dropdown-container <?= $activeFilters ? '' : 'hidden' ?> w-full flex p-8 gap-4">
                    <div style="width: 350px;">
                        <label>Statut</label>
                        <select name="status" style="width: 100%;" onchange="this.form.submit()">
                            <option value="">Tous</option>
                            <?php foreach (TicketManager::STATUSES as $key => $s): ?>
                            <option value="<?= $key ?>" <?= $filters['status'] === $key ? 'selected' : '' ?>><?= $e(rtrim($s['label'], ' !')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="width: 350px;">
                        <label>Tags</label>
                        <select name="priority" style="width: 100%;" onchange="this.form.submit()">
                            <option value="">Tous</option>
                            <?php foreach (TicketManager::PRIORITIES as $key => $pr): ?>
                            <option value="<?= $key ?>" <?= $filters['priority'] === $key ? 'selected' : '' ?>><?= $e($pr['label']) ?></option>
                            <?php endforeach; ?>
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
            <table>
                <thead>
                    <tr>
                        <td width="100px">ID#</td>
                        <td width="150px">Statut</td>
                        <td width="300px">Titre</td>
                        <td>Dernière réponse</td>
                        <td width="200px">Tags</td>
                        <td width="80px">Actions</td>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$result['rows']): ?>
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-8">
                            <?= $filters['q'] !== '' || $activeFilters ? 'Aucun ticket ne correspond à votre recherche.' : "Vous n'avez encore ouvert aucun ticket." ?>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php $ticketRows = $result['rows']; include __DIR__ . '/../elements/ticket-rows.php'; ?>
                </tbody>
            </table>
        </div>

        <?php
        $current = $result['page'];
        $last    = $result['pages'];
        include __DIR__ . '/../elements/pagination.php';
        ?>

        <form id="ticket-close-form" method="POST" action="assets/handlers/ticket-action" class="hidden">
            <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
            <input type="hidden" name="action" value="close">
            <input type="hidden" name="return" value="list">
            <input type="hidden" name="ticket_id">
        </form>
<?php endif; ?>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<script>
    lucide.createIcons();

    function closeTicket(id, number) {
        if (!confirm('Fermer le ticket ' + number + ' ? Il ne sera plus possible d\'y répondre.')) return;
        const form = document.getElementById('ticket-close-form');
        form.elements.ticket_id.value = id;
        form.submit();
    }
</script>

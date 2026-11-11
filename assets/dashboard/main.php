<?php
$me             = $auth->user();
$canViewTickets = $groupManager->userCan($me, 'view-own-tickets');

if ($canViewTickets) {
    $isStaff = $tickets->isStaff($me, $groupManager);
    $stats   = $tickets->userStats($me['id']);
    // 10 tickets ayant eu l'activité la plus récente (TicketManager::PER_PAGE)
    $recent  = $tickets->listForUser($me, ['q' => '', 'status' => '', 'priority' => '', 'sort' => 'recent'], 1);
    $csrf    = Auth::generateCsrfToken();
    $flash   = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
}

$e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
$n = fn(int $v) => number_format($v, 0, ',', '.');
?>
<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div id="view-history" class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">
        <h3 class="pb-12 text-cyan-400">Dashboard</h3>

<?php if (!$canViewTickets): ?>
        <p class="text-sm text-gray-500">Bienvenue <?= $e($me['pseudo']) ?> !</p>
<?php else: ?>
        <?php if ($flash): ?>
        <p class="text-sm mb-6 <?= $flash['type'] === 'success' ? 'text-cyan-400' : 'text-red-400' ?>"><?= $e($flash['message']) ?></p>
        <?php endif; ?>

        <div style="display: flex; gap: 15px; justify-content: space-between; flex-wrap: wrap;">
            <div class="card-glass-panel" style="min-width: 350px; width: calc(25% - 15px);">
                <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                    <i data-lucide="tickets" class="w-6 h-6 text-cyan-400"></i> <?= $n($stats['total']) ?>
                </h3>
                Nb. totaux de ticket ouvert
            </div>
            <div class="card-glass-panel" style="min-width: 350px; width: calc(25% - 15px);">
                <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                    <i data-lucide="ticket-minus" class="w-6 h-6 text-orange-400"></i> <?= $n($stats['open']) ?>
                </h3>
                Ticket en cours
            </div>
            <div class="card-glass-panel" style="min-width: 350px; width: calc(25% - 15px);">
                <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                    <i data-lucide="ticket-plus" class="w-6 h-6 text-green-400"></i> <?= $n($stats['waiting']) ?>
                </h3>
                Ticket attendant votre réponse
            </div>
            <div class="card-glass-panel" style="min-width: 350px; width: calc(25% - 15px);">
                <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                    <i data-lucide="ticket-slash" class="w-6 h-6 text-red-400"></i> <?= $n($stats['staff']) ?>
                </h3>
                Ticket en attente du staff
            </div>
        </div>

        <div class="mt-8">
            <h4 class="pb-2">Vos tickets ayant eu la plus récente activité :</h4>
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
                    <?php if (!$recent['rows']): ?>
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-8">
                            Vous n'avez encore ouvert aucun ticket. <a href="./dashboard?page=ticket-actions">Créer un ticket</a>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php $ticketRows = $recent['rows']; include __DIR__ . '/../elements/ticket-rows.php'; ?>
                </tbody>
            </table>
            <div class="flex justify-center pt-8 pb-24">
                <a href="./dashboard?page=tickets" class="origin-btn btn--full-graphic btn--primary">
                    <span>Voir plus</span>
                    <i data-lucide="arrow-big-right" class="btn--icon"></i>
                </a>
            </div>
        </div>

        <form id="ticket-close-form" method="POST" action="assets/handlers/ticket-action" class="hidden">
            <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
            <input type="hidden" name="action" value="close">
            <input type="hidden" name="return" value="dashboard">
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

<?php
$me         = $auth->user();
$profile    = $users->profile($me['id']);
$history    = $sanctions->history($me['id']);
$ticketData = $tickets->byAuthor($me['id']);
$candidatureCount = $characters->countCandidaturesByUser($me['id']);
$account    = $db->fetchOne('SELECT discord_id, discord_username, discord_linked_at FROM users WHERE id = ?', [$me['id']]);
$csrf       = Auth::generateCsrfToken();
$flash      = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$e          = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);

$sheetMode  = 'self';
$showEmail  = true;
$canTickets = $groupManager->userCan($me, 'view-own-tickets');
$ticketLink = fn(int $id) => $canTickets ? './dashboard?page=ticket-view&id=' . $id : null;
$profileActions = function () { ?>
    <a href="./dashboard?page=parameters" class="origin-btn btn--graphic btn--primary text-sm">
        <i data-lucide="settings" class="btn--icon"></i>
        Paramètres
    </a>
    <a href="./dashboard?page=ticket-actions" class="origin-btn btn--graphic btn--success text-sm">
        <i data-lucide="ticket-plus" class="btn--icon"></i>
        Créer un ticket
    </a>
<?php };
?>
<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 md:pb-40 fade-in-view">
        <h3 class="pb-4 text-cyan-400">Votre profil</h3>
        <p class="text-gray-500 text-xs uppercase tracking-widest pb-12">Vos informations, sanctions et tickets</p>

        <?php if ($flash): ?>
        <p class="text-sm mb-6 <?= $flash['type'] === 'success' ? 'text-cyan-400' : 'text-red-400' ?>"><?= $e($flash['message']) ?></p>
        <?php endif; ?>

        <!-- Compte Discord (prérequis des candidatures de personnage) -->
        <div class="card-glass-panel mb-6" style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; padding: 1rem 1.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719"></path></svg>
            <div style="flex: 1; min-width: 200px;">
                <div class="text-xs text-gray-500 uppercase tracking-widest">Compte Discord</div>
                <?php if ($account['discord_id']): ?>
                <div class="text-sm text-white">Lié à <strong><?= $e($account['discord_username']) ?></strong> <span class="text-gray-500 text-xs">depuis le <?= date('d/m/Y', strtotime($account['discord_linked_at'])) ?></span></div>
                <?php else: ?>
                <div class="text-sm text-gray-400">Non lié — nécessaire pour déposer une candidature de personnage.</div>
                <?php endif; ?>
            </div>
            <?php if ($account['discord_id']): ?>
            <form method="POST" action="assets/handlers/discord-link" onsubmit="return confirm('Délier votre compte Discord ?')">
                <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
                <input type="hidden" name="from" value="profil">
                <button type="submit" class="origin-btn btn--graphic btn--secondary" style="font-size: 12px;">Délier</button>
            </form>
            <?php elseif (Discord::isConfigured()): ?>
            <a href="assets/handlers/discord-link?from=profil" class="origin-btn btn--glass btn--discord" style="font-size: 12px;"><span>Lier mon Discord</span></a>
            <?php else: ?>
            <span class="text-xs text-yellow-400">Liaison Discord pas encore configurée sur le site.</span>
            <?php endif; ?>
        </div>

        <?php include __DIR__ . '/../elements/profile-sheet.php'; ?>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<script>
    lucide.createIcons();
</script>

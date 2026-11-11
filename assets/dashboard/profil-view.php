<?php
$me              = $auth->user();
$canViewProfiles = $groupManager->userCan($me, 'view-users', 'manage-users');
$profile         = $canViewProfiles ? $users->profile((string) ($_GET['id'] ?? '')) : false;

if ($profile) {
    $sanctions->refresh($profile['id']);              // levée des sanctions expirées
    $profile    = $users->profile($profile['id']);
    $history    = $sanctions->history($profile['id']);
    $ticketData = $tickets->byAuthor($profile['id']);
    $candidatureCount = $characters->countCandidaturesByUser($profile['id']);
    $canManage  = $groupManager->userCan($me, 'manage-users') && $profile['id'] !== $me['id'];
    $canTickets = $groupManager->userCan($me, 'manage-tickets');
    $groups     = $users->groups();
    $csrf       = Auth::generateCsrfToken();
    $flash      = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    $sheetMode  = 'staff';
    $showEmail  = true;
    $ticketLink = fn(int $id) => $canTickets ? './dashboard?page=ticket-staff&id=' . $id : null;
    $profileActions = $canManage ? function () use ($profile) { ?>
        <button type="button" onclick="openEditModal()" class="origin-btn btn--graphic btn--primary text-sm">
            <i data-lucide="pen" class="btn--icon"></i>
            Éditer
        </button>
        <?php if ($profile['status'] !== 'active'): ?>
        <button type="button" onclick="revokeSanction()" class="origin-btn btn--graphic btn--success text-sm">
            <i data-lucide="shield-check" class="btn--icon"></i>
            <?= $profile['status'] === 'banned' ? 'Débannir' : 'Lever la suspension' ?>
        </button>
        <?php endif; ?>
        <?php if ($profile['status'] === 'active'): ?>
        <button type="button" onclick="openSanctionModal('suspension')" class="origin-btn btn--graphic btn--warning text-sm">
            <i data-lucide="user-x" class="btn--icon"></i>
            Suspendre
        </button>
        <?php endif; ?>
        <?php if ($profile['status'] !== 'banned'): ?>
        <button type="button" onclick="openSanctionModal('ban')" class="origin-btn btn--graphic btn--danger text-sm">
            <i data-lucide="ban" class="btn--icon"></i>
            Bannir
        </button>
        <?php endif; ?>
        <button type="button" onclick="openSanctionModal('warning')" class="origin-btn btn--graphic btn--warning text-sm">
            <i data-lucide="triangle-alert" class="btn--icon"></i>
            Avertir
        </button>
    <?php } : null;
}

$e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
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
        max-height: 95vh; overflow-y: auto;
    }
    .modal-box input, .modal-box select { width: 100%; }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 md:pb-40 fade-in-view">
        <a href="./dashboard?page=profil-visual" class="inline-flex items-center gap-2 text-gray-400 hover:text-cyan-400 transition-colors mb-6 text-sm uppercase tracking-widest">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour à la recherche
        </a>
        <h3 class="pb-4 text-cyan-400">Fiche joueur</h3>

<?php if (!$canViewProfiles): ?>
        <p class="text-sm text-red-400">Vous n'avez pas la permission de consulter les profils des joueurs.</p>
<?php elseif (!$profile): ?>
        <p class="text-sm text-gray-500">Ce joueur n'existe pas.</p>
<?php else: ?>
        <p class="text-gray-500 text-xs uppercase tracking-widest pb-12">Informations, sanctions et historique des tickets</p>

        <?php if ($flash): ?>
        <p class="text-sm mb-6 <?= $flash['type'] === 'success' ? 'text-cyan-400' : 'text-red-400' ?>"><?= $e($flash['message']) ?></p>
        <?php endif; ?>

        <?php include __DIR__ . '/../elements/profile-sheet.php'; ?>
<?php endif; ?>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<?php if ($profile && $canManage): ?>
<!-- Modal édition -->
<div id="modal-edit" class="modal-overlay hidden">
    <form class="modal-box" method="POST" action="assets/handlers/users-action">
        <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="return_to" value="profile">
        <input type="hidden" name="user_id" value="<?= $e($profile['id']) ?>">

        <h4 class="text-cyan-400 mb-6">Modifier : <?= $e($profile['pseudo']) ?></h4>
        <div class="w-full mb-6">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Pseudo (site)</label>
            <input type="text" name="pseudo" value="<?= $e($profile['pseudo']) ?>" required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_\-\.]+">
        </div>
        <div class="w-full mb-6">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Pseudo Minecraft</label>
            <input type="text" name="minecraft_pseudo" value="<?= $e($profile['minecraft_pseudo']) ?>" maxlength="16" pattern="[a-zA-Z0-9_]{3,16}" placeholder="Optionnel">
        </div>
        <div class="w-full mb-6">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">E-mail</label>
            <input type="email" name="email" value="<?= $e($profile['email']) ?>" required maxlength="255">
        </div>
        <div class="w-full mb-8">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Groupe</label>
            <select name="group_id">
                <?php foreach ($groups as $g): ?>
                <option value="<?= (int) $g['id'] ?>" <?= (int) $profile['group_id'] === (int) $g['id'] ? 'selected' : '' ?>><?= $e($g['name']) ?></option>
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

<!-- Modal sanction -->
<div id="modal-sanction" class="modal-overlay hidden">
    <form class="modal-box" method="POST" action="assets/handlers/users-action">
        <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
        <input type="hidden" name="action" value="sanction">
        <input type="hidden" name="return_to" value="profile">
        <input type="hidden" name="user_id" value="<?= $e($profile['id']) ?>">

        <h4 class="text-red-400 mb-6">Sanctionner : <?= $e($profile['pseudo']) ?></h4>
        <div class="w-full mb-6">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Type de sanction</label>
            <select name="type" id="sanction-type" onchange="updateSanctionForm()">
                <?php foreach (SanctionManager::TYPES as $key => $t): ?>
                <option value="<?= $key ?>"><?= $e($t['label']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-full mb-6" id="sanction-duration-wrap">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Durée</label>
            <select name="duration" id="sanction-duration">
                <?php foreach (SanctionManager::DURATIONS as $key => $d): ?>
                <option value="<?= $key ?>"><?= $e($d['label']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-full mb-8">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Motif</label>
            <textarea name="reason" id="sanction-reason" rows="3" style="width: 100%;" required minlength="3" maxlength="500" placeholder="Raison de la sanction (affichée au joueur à la connexion)"></textarea>
        </div>
        <div class="flex gap-4">
            <button type="button" onclick="closeModals()" class="origin-btn btn--graphic btn--secondary flex-1 justify-center">Annuler</button>
            <button type="submit" class="origin-btn btn--full-graphic btn--danger flex-1 justify-center">
                <span>Appliquer</span>
                <i data-lucide="gavel" class="btn--icon"></i>
            </button>
        </div>
    </form>
</div>

<!-- Levée de sanction -->
<form id="revoke-form" method="POST" action="assets/handlers/users-action" class="hidden">
    <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
    <input type="hidden" name="action" value="revoke">
    <input type="hidden" name="return_to" value="profile">
    <input type="hidden" name="user_id" value="<?= $e($profile['id']) ?>">
</form>
<?php endif; ?>

<script>
    lucide.createIcons();

<?php if ($profile && $canManage): ?>
    const modals = [document.getElementById('modal-edit'), document.getElementById('modal-sanction')];

    function closeModals() {
        modals.forEach(m => m.classList.add('hidden'));
    }
    modals.forEach(m => m.addEventListener('click', function (e) {
        if (e.target === this) closeModals();
    }));

    function openEditModal() {
        document.getElementById('modal-edit').classList.remove('hidden');
    }

    function openSanctionModal(type) {
        const modal = document.getElementById('modal-sanction');
        modal.querySelector('form').reset();
        document.getElementById('sanction-type').value = type;
        updateSanctionForm();
        modal.classList.remove('hidden');
        document.getElementById('sanction-reason').focus();
    }

    // Avertissement : pas de durée. Suspension : forcément temporaire. Bannissement : définitif par défaut.
    function updateSanctionForm() {
        const type     = document.getElementById('sanction-type').value;
        const duration = document.getElementById('sanction-duration');
        document.getElementById('sanction-duration-wrap').classList.toggle('hidden', type === 'warning');
        duration.disabled = type === 'warning';
        duration.querySelector('option[value="permanent"]').disabled = type === 'suspension';
        duration.value = type === 'ban' ? 'permanent' : '24h';
    }

    function revokeSanction() {
        if (!confirm(<?= json_encode('Lever la sanction de « ' . $profile['pseudo'] . ' » ?', JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) ?>)) return;
        document.getElementById('revoke-form').submit();
    }
<?php endif; ?>
</script>

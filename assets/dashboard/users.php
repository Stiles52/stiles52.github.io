<?php
$canView   = $groupManager->userCan($auth->user(), 'view-users', 'manage-users');
$canManage = $groupManager->userCan($auth->user(), 'manage-users');

if ($canView) {
    $filters = [
        'q'      => trim($_GET['q'] ?? ''),
        'group'  => $_GET['group']  ?? '',
        'status' => $_GET['status'] ?? '',
        'sort'   => isset(UserManager::SORTS[$_GET['sort'] ?? '']) ? $_GET['sort'] : 'recent',
    ];
    $result = $users->search($filters, (int) ($_GET['p'] ?? 1));
    $groups = $users->groups();
    $csrf   = Auth::generateCsrfToken();
    $flash  = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    $activeFilters = array_filter($filters, fn($v, $k) => $v !== '' && $k !== 'q' && !($k === 'sort' && $v === 'recent'), ARRAY_FILTER_USE_BOTH);
    $returnQuery   = http_build_query(array_filter($filters + ['p' => $result['page']], fn($v) => $v !== '' && $v !== 1));

    $pageUrl = fn(int $p) => './dashboard?' . http_build_query(['page' => 'users'] + array_filter($filters + ['p' => $p], fn($v) => $v !== '' && $v !== 1));
    $e       = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
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
        max-height: 95vh; overflow-y: auto;
    }
    .modal-box input, .modal-box select { width: 100%; }
    .flash { padding: 1rem; margin-bottom: 2rem; font-size: 13px; border: 1px solid; }
    .flash--success { color: #22d3ee; border-color: #22d3ee33; }
    .flash--error   { color: #ef4444; border-color: #ef444433; }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">
        <h3 class="pb-12 text-cyan-400">Gestion des utilisateurs</h3>

<?php if (!$canView): ?>
        <p class="flash flash--error">Vous n'avez pas la permission d'accéder à la gestion des utilisateurs.</p>
<?php else: ?>

        <?php if ($flash): ?>
        <p class="flash flash--<?= $flash['type'] === 'success' ? 'success' : 'error' ?>"><?= $e($flash['message']) ?></p>
        <?php endif; ?>

        <form method="GET" action="./dashboard" id="users-filters">
            <input type="hidden" name="page" value="users">

            <div class="page-toolbar">
                <div class="page-toolbar--search">
                    <input type="text" name="q" id="user-search" value="<?= $e($filters['q']) ?>" placeholder="Rechercher un pseudo, pseudo Minecraft ou e-mail...">
                    <button type="submit" class="origin-btn btn--graphic btn--primary" style="flex-shrink:0;">
                        <i data-lucide="search" class="btn--icon"></i>
                    </button>
                </div>
                <div class="page-toolbar--actions">
                    <button type="button" class="dropdown origin-btn btn--full-graphic btn--primary">
                        Filtres<?= $activeFilters ? ' (' . count($activeFilters) . ')' : '' ?>
                        <i data-lucide="sliders-horizontal" class="btn--icon"></i>
                    </button>
                    <?php if ($canManage): ?>
                    <button type="button" onclick="openCreateModal()" class="origin-btn btn--full-graphic btn--success">
                        Créer un utilisateur
                        <i data-lucide="user-plus" class="btn--icon"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="dropdown-container <?= $activeFilters ? '' : 'hidden' ?> w-full flex p-8 gap-4">
                <div style="width: 350px;">
                    <label>Groupe</label>
                    <select name="group" style="width: 100%;" onchange="this.form.submit()">
                        <option value="">Tous</option>
                        <?php foreach ($groups as $g): ?>
                        <option value="<?= $e($g['slug']) ?>" <?= $filters['group'] === $g['slug'] ? 'selected' : '' ?>><?= $e($g['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="width: 350px;">
                    <label>Statut</label>
                    <select name="status" style="width: 100%;" onchange="this.form.submit()">
                        <option value="">Tous</option>
                        <?php foreach (UserManager::STATUSES as $key => $s): ?>
                        <option value="<?= $key ?>" <?= $filters['status'] === $key ? 'selected' : '' ?>><?= $s['label'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="width: 350px;">
                    <label>Ordre d'apparition</label>
                    <select name="sort" style="width: 100%;" onchange="this.form.submit()">
                        <?php foreach (UserManager::SORTS as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $filters['sort'] === $key ? 'selected' : '' ?>><?= $e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>

        <p class="text-xs text-gray-500 uppercase tracking-widest pb-4 pt-4">
            <?= $result['total'] ?> utilisateur<?= $result['total'] > 1 ? 's' : '' ?>
        </p>

        <table id="users-table">
            <thead>
                <tr>
                    <td width="60px">ID#</td>
                    <td width="180px">Pseudo (site)</td>
                    <td width="180px">Pseudo Minecraft</td>
                    <td width="160px">Groupe</td>
                    <td width="130px">Statut</td>
                    <td width="80px">Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$result['rows']): ?>
                <tr>
                    <td colspan="6" class="text-center text-gray-500 py-8">Aucun utilisateur trouvé.</td>
                </tr>
                <?php endif; ?>

                <?php foreach ($result['rows'] as $u):
                    $status    = UserManager::STATUSES[$u['status']];
                    $isSelf    = $u['id'] === $auth->user()['id'];
                    // Résumé de la sanction en cours (infobulle + modale d'édition)
                    $sanctionInfo = $u['status'] !== 'active'
                        ? $status['label'] . ($u['sanction_until'] ? " jusqu'au " . date('d/m/Y à H:i', strtotime($u['sanction_until'])) : ' définitivement')
                          . ($u['sanction_reason'] ? ' — Motif : ' . $u['sanction_reason'] : '')
                        : '';
                    $userData = json_encode([
                        'id'               => $u['id'],
                        'pseudo'           => $u['pseudo'],
                        'minecraft_pseudo' => $u['minecraft_pseudo'] ?? '',
                        'email'            => $u['email'],
                        'group_id'         => $u['group_id'] ?? '',
                        'status'           => $u['status'],
                        'sanction'         => $sanctionInfo,
                        'self'             => $isSelf,
                    ]);
                ?>
                <tr>
                    <td class="text-xs text-gray-500" title="<?= $e($u['id']) ?>">#<?= $e(substr($u['id'], 0, 8)) ?></td>
                    <td class="font-bold" title="<?= $e($u['email']) ?>"><?= $e($u['pseudo']) ?></td>
                    <td class="text-cyan-400 font-mono text-sm"><?= $u['minecraft_pseudo'] ? $e($u['minecraft_pseudo']) : '<span class="text-gray-600">—</span>' ?></td>
                    <td>
                        <?php if ($u['group_name']): ?>
                        <div class="badge-glass badge-glass--<?= $e($u['group_badge']) ?>"><span class="badge-glass--text"><?= $e($u['group_name']) ?></span></div>
                        <?php else: ?>
                        <span class="text-gray-600">Aucun</span>
                        <?php endif; ?>
                    </td>
                    <td><div class="badge-glass badge-glass--<?= $status['badge'] ?>" <?= $sanctionInfo ? 'title="' . $e($sanctionInfo) . '"' : '' ?>><span class="badge-glass--text"><?= $status['label'] ?></span></div></td>
                    <td>
                        <button type="button" class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="./dashboard?page=profil-view&id=<?= $e(urlencode($u['id'])) ?>">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir le profil
                                </a>
                                <?php if ($canManage): ?>
                                <a href="#" onclick="openEditModal(this); return false;" data-user="<?= $e($userData) ?>">
                                    <i data-lucide="pen" class="btn--icon"></i>
                                    Éditer
                                </a>
                                <?php endif; ?>
                                <?php if ($canManage && !$isSelf): ?>
                                <a href="#" onclick="openSanctionModal(this); return false;" data-user="<?= $e($userData) ?>">
                                    <i data-lucide="gavel" class="btn--icon"></i>
                                    Sanctionner
                                </a>
                                    <?php if ($u['status'] !== 'active'): ?>
                                    <a href="#" onclick="revokeSanction(this); return false;" data-user="<?= $e($userData) ?>">
                                        <i data-lucide="shield-check" class="btn--icon"></i>
                                        <?= $u['status'] === 'banned' ? 'Débannir' : 'Lever la suspension' ?>
                                    </a>
                                    <?php endif; ?>
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

<?php if ($canManage): ?>
<!-- Modal création / édition -->
<div id="modal-user" class="modal-overlay hidden">
    <form class="modal-box" method="POST" action="assets/handlers/users-action">
        <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
        <input type="hidden" name="return" value="<?= $e($returnQuery) ?>">
        <input type="hidden" name="action" id="modal-action" value="create">
        <input type="hidden" name="user_id" id="modal-user-id" value="">

        <h4 class="text-cyan-400 mb-6" id="modal-title">Créer un utilisateur</h4>
        <div class="w-full mb-6">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Pseudo (site)</label>
            <input type="text" name="pseudo" id="modal-pseudo" required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_\-\.]+">
        </div>
        <div class="w-full mb-6">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Pseudo Minecraft</label>
            <input type="text" name="minecraft_pseudo" id="modal-minecraft" maxlength="16" pattern="[a-zA-Z0-9_]{3,16}" placeholder="Optionnel">
        </div>
        <div class="w-full mb-6">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">E-mail</label>
            <input type="email" name="email" id="modal-email" required maxlength="255">
        </div>
        <div class="w-full mb-6">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Groupe</label>
            <select name="group_id" id="modal-group">
                <?php foreach ($groups as $g): ?>
                <option value="<?= (int) $g['id'] ?>" <?= $g['is_default'] ? 'data-default' : '' ?>><?= $e($g['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- Création uniquement : le mot de passe d'un compte existant n'est pas modifiable par le staff -->
        <div class="w-full mb-8" id="modal-password-wrap">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Mot de passe</label>
            <input type="password" name="password" id="modal-password" minlength="8" autocomplete="new-password" placeholder="8 caractères minimum">
        </div>

        <!-- Édition uniquement : sanctions -->
        <div class="w-full mb-8 hidden" id="modal-sanctions">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Sanctions</label>
            <p class="text-xs mb-4" id="modal-sanction-state"></p>
            <div class="flex gap-2 flex-wrap">
                <button type="button" onclick="openSanctionModal(null, 'warning')" class="origin-btn btn--graphic btn--warning">
                    <span>Avertir</span>
                    <i data-lucide="triangle-alert" class="btn--icon"></i>
                </button>
                <button type="button" onclick="openSanctionModal(null, 'suspension')" class="origin-btn btn--graphic btn--warning">
                    <span>Suspendre</span>
                    <i data-lucide="user-x" class="btn--icon"></i>
                </button>
                <button type="button" onclick="openSanctionModal(null, 'ban')" class="origin-btn btn--graphic btn--danger">
                    <span>Bannir</span>
                    <i data-lucide="ban" class="btn--icon"></i>
                </button>
                <button type="button" onclick="revokeSanction(null)" id="modal-revoke" class="origin-btn btn--graphic btn--success">
                    <span>Lever la sanction</span>
                    <i data-lucide="shield-check" class="btn--icon"></i>
                </button>
            </div>
        </div>
        <div class="flex gap-4">
            <button type="button" onclick="closeModal()" class="origin-btn btn--graphic btn--secondary flex-1 justify-center">Annuler</button>
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
        <input type="hidden" name="return" value="<?= $e($returnQuery) ?>">
        <input type="hidden" name="action" value="sanction">
        <input type="hidden" name="user_id" id="sanction-user-id" value="">

        <h4 class="text-red-400 mb-6" id="sanction-title">Sanctionner</h4>
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
            <button type="button" onclick="closeSanctionModal()" class="origin-btn btn--graphic btn--secondary flex-1 justify-center">Annuler</button>
            <button type="submit" class="origin-btn btn--full-graphic btn--danger flex-1 justify-center">
                <span>Appliquer</span>
                <i data-lucide="gavel" class="btn--icon"></i>
            </button>
        </div>
    </form>
</div>

<!-- Levée de sanction -->
<form id="user-revoke-form" method="POST" action="assets/handlers/users-action" class="hidden">
    <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
    <input type="hidden" name="return" value="<?= $e($returnQuery) ?>">
    <input type="hidden" name="action" value="revoke">
    <input type="hidden" name="user_id">
</form>
<?php endif; ?>

<script>
    lucide.createIcons();

<?php if ($canManage): ?>
    const modal         = document.getElementById('modal-user');
    const sanctionModal = document.getElementById('modal-sanction');
    let editedUser      = null; // utilisateur ouvert dans la modale d'édition

    function setPasswordField(visible) {
        const pwd = document.getElementById('modal-password');
        document.getElementById('modal-password-wrap').classList.toggle('hidden', !visible);
        pwd.disabled = !visible; // champ désactivé = non envoyé
        pwd.required = visible;
        pwd.value = '';
    }

    function openCreateModal() {
        editedUser = null;
        modal.querySelector('form').reset();
        document.getElementById('modal-title').textContent = 'Créer un utilisateur';
        document.getElementById('modal-action').value = 'create';
        document.getElementById('modal-user-id').value = '';
        const def = modal.querySelector('#modal-group option[data-default]');
        if (def) def.selected = true;
        setPasswordField(true);
        document.getElementById('modal-sanctions').classList.add('hidden');
        modal.classList.remove('hidden');
    }

    function openEditModal(link) {
        const user = editedUser = JSON.parse(link.dataset.user);
        document.getElementById('modal-title').textContent = 'Modifier : ' + user.pseudo;
        document.getElementById('modal-action').value = 'update';
        document.getElementById('modal-user-id').value = user.id;
        document.getElementById('modal-pseudo').value = user.pseudo;
        document.getElementById('modal-minecraft').value = user.minecraft_pseudo;
        document.getElementById('modal-email').value = user.email;
        document.getElementById('modal-group').value = user.group_id;
        setPasswordField(false);

        // Pas de sanction possible sur son propre compte
        document.getElementById('modal-sanctions').classList.toggle('hidden', user.self);
        const state = document.getElementById('modal-sanction-state');
        state.textContent = user.sanction || 'Aucune sanction en cours.';
        state.className = 'text-xs mb-4 ' + (user.sanction ? 'text-red-400' : 'text-gray-500');
        document.getElementById('modal-revoke').classList.toggle('hidden', user.status === 'active');

        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    // link = lien du menu de la ligne, ou null depuis la modale d'édition
    function openSanctionModal(link, type = 'warning') {
        const user = link ? JSON.parse(link.dataset.user) : editedUser;
        sanctionModal.querySelector('form').reset();
        document.getElementById('sanction-title').textContent = 'Sanctionner : ' + user.pseudo;
        document.getElementById('sanction-user-id').value = user.id;
        document.getElementById('sanction-type').value = type;
        updateSanctionForm();
        closeModal();
        sanctionModal.classList.remove('hidden');
        document.getElementById('sanction-reason').focus();
    }

    function closeSanctionModal() {
        sanctionModal.classList.add('hidden');
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

    function revokeSanction(link) {
        const user = link ? JSON.parse(link.dataset.user) : editedUser;
        if (!confirm('Lever la sanction de « ' + user.pseudo + ' » ?')) return;
        const form = document.getElementById('user-revoke-form');
        form.elements.user_id.value = user.id;
        form.submit();
    }

    [modal, sanctionModal].forEach(m => m.addEventListener('click', function (e) {
        if (e.target === this) this.classList.add('hidden');
    }));
<?php endif; ?>
</script>

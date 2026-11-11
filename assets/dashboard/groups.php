<?php
$canManageGroups = $groupManager->userCan($auth->user(), 'manage-groups');

if ($canManageGroups) {
    $groupList = $groupManager->all();
    $csrf      = Auth::generateCsrfToken();
    $flash     = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    $e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
}
?>
<style>
    .group-card {
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(34, 211, 238, 0.2);
        border-top-width: 4px;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }
    .group-card:hover {
        border-color: rgba(34, 211, 238, 0.5);
        box-shadow: 0 0 20px rgba(34, 211, 238, 0.05);
    }
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
    .flash { padding: 1rem; margin-bottom: 2rem; font-size: 13px; border: 1px solid; }
    .flash--success { color: #22d3ee; border-color: #22d3ee33; }
    .flash--error   { color: #ef4444; border-color: #ef444433; }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">
<?php if (!$canManageGroups): ?>
        <h3 class="pb-12 text-cyan-400">Gestion des groupes</h3>
        <p class="flash flash--error">Vous n'avez pas la permission d'accéder à la gestion des groupes.</p>
<?php else: ?>
        <div class="flex items-center justify-between pb-12">
            <h3 class="text-cyan-400">Gestion des groupes</h3>
            <button onclick="openModal()" class="origin-btn btn--full-graphic btn--success">
                Créer un groupe
                <i data-lucide="plus" class="btn--icon"></i>
            </button>
        </div>

        <?php if ($flash): ?>
        <p class="flash flash--<?= $flash['type'] === 'success' ? 'success' : 'error' ?>"><?= $e($flash['message']) ?></p>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">

            <?php foreach ($groupList as $g):
                $editData = json_encode(['id' => (int) $g['id'], 'name' => $g['name'], 'color' => $g['color'], 'permissions' => $g['permissions']]);
            ?>
            <!-- <?= $e($g['name']) ?> -->
            <div class="group-card" style="border-top-color: <?= $e($g['color']) ?>;">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <i data-lucide="<?= $e($g['icon']) ?>" class="w-5 h-5 <?= $g['palette']['card'] ?>"></i>
                        <h5 class="text-white"><?= $e($g['name']) ?></h5>
                    </div>
                    <button class="dropdown">
                        <i data-lucide="ellipsis-vertical" class="w-5 h-5 text-gray-400"></i>
                        <div class="dropdown-container dropdown-container--popup hidden">
                            <a href="#" onclick="openEditModal(this); return false;" data-group="<?= $e($editData) ?>">
                                <i data-lucide="pen" class="btn--icon"></i>
                                Modifier
                            </a>
                            <?php if (!$g['is_default']): ?>
                            <a href="#" class="text-red-400" onclick="deleteGroup(<?= (int) $g['id'] ?>, '<?= $e(addslashes($g['name'])) ?>', <?= (int) $g['members'] ?>); return false;">
                                <i data-lucide="trash-2" class="btn--icon"></i>
                                Supprimer
                            </a>
                            <?php endif; ?>
                        </div>
                    </button>
                </div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="badge-glass badge-glass--<?= $e($g['badge']) ?>"><span class="badge-glass--text"><?= $e($g['name']) ?></span></div>
                    <span class="text-xs text-gray-500"><?= (int) $g['members'] ?> membre<?= $g['members'] > 1 ? 's' : '' ?></span>
                </div>
                <div class="text-xs text-gray-500 uppercase tracking-widest mb-2">Permissions</div>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($g['permissions'] as $perm): ?>
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300"><?= $e($perm) ?></span>
                    <?php endforeach; ?>
                    <?php if (!$g['permissions']): ?>
                    <span class="text-xs text-gray-600">Aucune</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
<?php endif; ?>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<?php if ($canManageGroups): ?>
<!-- Modal création de groupe -->
<div id="modal-create" class="modal-overlay hidden">
    <form class="modal-box" method="POST" action="assets/handlers/groups-action">
        <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
        <input type="hidden" name="action" id="group-action" value="create">
        <input type="hidden" name="group_id" id="group-id" value="">
        <input type="hidden" name="color" id="group-color" value="">

        <h4 class="text-cyan-400 mb-6" id="modal-title">Créer un groupe</h4>
        <div class="w-full mb-6">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Nom du groupe</label>
            <input type="text" name="name" id="group-name" style="width: 100%;" placeholder="Ex : Recruteur" required minlength="2" maxlength="50">
        </div>
        <div class="w-full mb-6">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Couleur du badge</label>
            <div class="flex gap-3 flex-wrap mt-2">
                <?php foreach (array_keys(GroupManager::PALETTE) as $hex): ?>
                <button type="button" onclick="selectColor('<?= $hex ?>')" data-color="<?= $hex ?>" class="w-8 h-8 rounded" style="background:<?= $hex ?>;"></button>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="w-full mb-8">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Permissions</label>
            <div class="flex flex-wrap gap-3 mt-2">
                <?php
                foreach(GroupManager::allPermissions() as $perm) {
                    echo '<label class="flex items-center gap-2 text-xs text-gray-400 cursor-pointer">';
                    echo '<input type="checkbox" name="permissions[]" value="'.$perm.'" class="accent-cyan-400">';
                    echo $perm;
                    echo '</label>';
                }
                ?>
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

<!-- Formulaire de suppression -->
<form id="group-delete-form" method="POST" action="assets/handlers/groups-action" class="hidden">
    <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="group_id">
</form>
<?php endif; ?>

<script>
    lucide.createIcons();

<?php if ($canManageGroups): ?>
    const groupForm = document.querySelector('#modal-create form');

    function openModal() {
        groupForm.reset();
        document.getElementById('modal-title').textContent = 'Créer un groupe';
        document.getElementById('group-action').value = 'create';
        document.getElementById('group-id').value = '';
        selectColor('#4b5563');
        document.getElementById('modal-create').classList.remove('hidden');
    }
    function openEditModal(link) {
        const group = JSON.parse(link.dataset.group);
        groupForm.reset();
        document.getElementById('modal-title').textContent = 'Modifier : ' + group.name;
        document.getElementById('group-action').value = 'update';
        document.getElementById('group-id').value = group.id;
        document.getElementById('group-name').value = group.name;
        selectColor(group.color);
        groupForm.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = group.permissions.includes(cb.value));
        document.getElementById('modal-create').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('modal-create').classList.add('hidden');
    }
    function selectColor(hex) {
        document.getElementById('group-color').value = hex;
        document.querySelectorAll('.modal-box button[data-color]').forEach(b => {
            b.style.outline = b.dataset.color === hex ? '3px solid #22d3ee' : 'none';
        });
    }
    function deleteGroup(id, name, members) {
        const msg = 'Supprimer le groupe « ' + name + ' » ?' + (members > 0 ? '\n' + members + ' membre(s) rejoindront le groupe par défaut.' : '');
        if (!confirm(msg)) return;
        const form = document.getElementById('group-delete-form');
        form.elements.group_id.value = id;
        form.submit();
    }
    document.getElementById('modal-create').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
<?php endif; ?>
</script>

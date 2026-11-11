<?php
$canManagePerms = $groupManager->userCan($auth->user(), 'manage-permissions');

if ($canManagePerms) {
    $groupList = $groupManager->all();
    $csrf      = Auth::generateCsrfToken();
    $e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
}
?>
<style>
    .perm-table th, .perm-table td {
        padding: 0.75rem 1rem;
        border: 1px solid rgba(34, 211, 238, 0.1);
        text-align: center;
        vertical-align: middle;
    }
    .perm-table th { background: rgba(0, 0, 0, 0.6); color: #9ca3af; font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; }
    .perm-table th.perm-group { color: #22d3ee; font-size: 12px; }
    .perm-table td.perm-name { text-align: left; color: #9ca3af; font-size: 12px; font-family: 'JetBrains Mono', monospace; }
    .perm-table tr:hover td { background: rgba(34, 211, 238, 0.03); }
    .perm-toggle {
        width: 20px; height: 20px; border-radius: 4px;
        border: 1px solid rgba(34, 211, 238, 0.3);
        background: transparent; cursor: pointer;
        transition: all 0.2s ease;
        margin: 0 auto; display: flex; align-items: center; justify-content: center;
    }
    .perm-toggle.active { background: rgba(34, 211, 238, 0.2); border-color: #22d3ee; }
    .perm-toggle.active::after { content: '✓'; color: #22d3ee; font-size: 12px; }
    .perm-section { background: rgba(34, 211, 238, 0.05); }
    .perm-section td { color: #22d3ee; font-size: 10px; text-transform: uppercase; letter-spacing: 0.15em; padding: 0.5rem 1rem; }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">
<?php if (!$canManagePerms): ?>
        <h3 class="pb-12 text-cyan-400">Gestion des permissions</h3>
        <p class="text-sm text-red-400">Vous n'avez pas la permission d'accéder à la gestion des permissions.</p>
<?php else: ?>
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; padding-bottom:2rem;">
            <h3 class="text-cyan-400" style="margin:0;">Gestion des permissions</h3>
            <button class="origin-btn btn--full-graphic btn--success" onclick="savePerms()">
                Sauvegarder
                <i data-lucide="save" class="btn--icon"></i>
            </button>
        </div>

        <cite>
            Les modifications apportées à la matrice de permissions s'appliquent à tous les membres du groupe concerné.
            Cliquez sur une case pour activer / désactiver une permission.
        </cite>

        <div style="overflow-x: auto; margin-top: 2rem;">
            <table class="perm-table w-full">
                <thead>
                    <tr>
                        <th style="min-width: 240px; text-align: left;">Permission</th>
                        <?php foreach ($groupList as $g): ?>
                        <th class="perm-group" style="min-width: 120px;">
                            <i data-lucide="<?= $e($g['icon']) ?>" class="w-4 h-4 <?= $g['palette']['head'] ?> inline mb-1"></i><br><?= $e($g['name']) ?>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (GroupManager::PERMISSIONS as $section => $perms): ?>
                    <!-- <?= $e($section) ?> -->
                    <tr class="perm-section">
                        <td colspan="<?= count($groupList) + 1 ?>"><?= $e($section) ?></td>
                    </tr>
                    <?php foreach ($perms as $perm): ?>
                    <tr>
                        <td class="perm-name"><?= $perm ?></td>
                        <?php foreach ($groupList as $g): ?>
                        <td><div class="perm-toggle <?= in_array($perm, $g['permissions'], true) ? 'active' : '' ?>" data-group="<?= (int) $g['id'] ?>" data-perm="<?= $perm ?>" onclick="togglePerm(this)"></div></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="save-feedback" class="hidden text-center text-sm text-green-400 mt-6 uppercase tracking-widest"></div>
<?php endif; ?>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<script>
    lucide.createIcons();

    function togglePerm(el) {
        el.classList.toggle('active');
    }

<?php if ($canManagePerms): ?>
    let saving = false;

    async function savePerms() {
        if (saving) return;
        saving = true;

        // Matrice complète : chaque groupe avec la liste de ses permissions actives
        const matrix = {};
        document.querySelectorAll('.perm-toggle[data-group]').forEach(el => {
            matrix[el.dataset.group] ??= [];
            if (el.classList.contains('active')) matrix[el.dataset.group].push(el.dataset.perm);
        });

        const body = new FormData();
        body.append('csrf_token', <?= json_encode($csrf) ?>);
        body.append('matrix', JSON.stringify(matrix));

        let result;
        try {
            const res = await fetch('assets/api/permissions-save', { method: 'POST', body });
            result = await res.json();
        } catch (err) {
            result = { success: false, message: 'Erreur réseau, réessayez.' };
        }
        saving = false;

        const fb = document.getElementById('save-feedback');
        fb.textContent = (result.success ? '✓ ' : '✗ ') + result.message;
        fb.classList.toggle('text-green-400', result.success);
        fb.classList.toggle('text-red-400', !result.success);
        fb.classList.remove('hidden');
        setTimeout(() => fb.classList.add('hidden'), 3000);
    }
<?php endif; ?>
</script>

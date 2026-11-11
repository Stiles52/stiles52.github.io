<?php
$canViewProfiles = $groupManager->userCan($auth->user(), 'view-users', 'manage-users');

if ($canViewProfiles) {
    $query  = trim((string) ($_GET['user'] ?? ''));
    $result = $query !== '' ? $users->search(['q' => $query], (int) ($_GET['p'] ?? 1)) : null;

    // Un seul résultat dont le pseudo (site ou Minecraft) correspond exactement : ouverture directe de la fiche
    $exact = null;
    if ($result && $result['total'] === 1) {
        $only  = $result['rows'][0];
        $exact = strcasecmp($only['pseudo'], $query) === 0 || strcasecmp((string) $only['minecraft_pseudo'], $query) === 0 ? $only : null;
    }

    $pageUrl = fn(int $p) => './dashboard?' . http_build_query(['page' => 'profil-visual', 'user' => $query] + ($p > 1 ? ['p' => $p] : []));
    $e       = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
}
?>
<style>
    .recent-searches {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .recent-searches a {
        text-decoration: none;
    }
    .table-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 md:pb-40 fade-in-view">
        <h3 class="pb-4 text-cyan-400">Visualisateur des profils</h3>
        <p class="text-gray-500 text-xs uppercase tracking-widest pb-12">Fiche joueur — informations, sanctions et historique des tickets</p>

<?php if (!$canViewProfiles): ?>
        <p class="text-sm text-red-400">Vous n'avez pas la permission de consulter les profils des joueurs.</p>
<?php else: ?>
        <!-- Outils -->
        <form method="GET" action="./dashboard" class="page-toolbar mb-8">
            <input type="hidden" name="page" value="profil-visual">
            <div class="page-toolbar--search">
                <input type="text" name="user" id="profile-search" value="<?= $e($query) ?>" placeholder="Pseudo Minecraft ou pseudo du site...">
                <button type="submit" class="origin-btn btn--graphic btn--primary" style="flex-shrink:0;">
                    <i data-lucide="search" class="btn--icon"></i>
                </button>
            </div>
            <div class="page-toolbar--actions">
                <a href="./dashboard?page=users" class="origin-btn btn--full-graphic btn--primary">
                    Liste des utilisateurs
                    <i data-lucide="users" class="btn--icon"></i>
                </a>
            </div>
        </form>

        <?php if ($result !== null): ?>
        <!-- Résultats -->
        <p class="text-xs text-gray-500 uppercase tracking-widest pb-4">
            <?= $result['total'] ?> résultat<?= $result['total'] > 1 ? 's' : '' ?> pour « <?= $e($query) ?> »
        </p>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <td width="180px">Pseudo (site)</td>
                        <td width="180px">Pseudo Minecraft</td>
                        <td width="160px">Groupe</td>
                        <td width="130px">Statut</td>
                        <td>Inscrit le</td>
                        <td width="80px">Actions</td>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$result['rows']): ?>
                    <tr>
                        <td colspan="6" class="text-center text-xs text-gray-600 py-6">Aucun joueur ne correspond à cette recherche.</td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($result['rows'] as $u):
                        $status  = UserManager::STATUSES[$u['status']];
                        $viewUrl = './dashboard?page=profil-view&id=' . urlencode($u['id']);
                    ?>
                    <tr>
                        <td class="font-bold"><a href="<?= $e($viewUrl) ?>"><?= $e($u['pseudo']) ?></a></td>
                        <td class="text-cyan-400 font-mono text-sm"><?= $u['minecraft_pseudo'] ? $e($u['minecraft_pseudo']) : '<span class="text-gray-600">—</span>' ?></td>
                        <td>
                            <?php if ($u['group_name']): ?>
                            <div class="badge-glass badge-glass--<?= $e($u['group_badge']) ?>"><span class="badge-glass--text"><?= $e($u['group_name']) ?></span></div>
                            <?php else: ?>
                            <span class="text-gray-600">Aucun</span>
                            <?php endif; ?>
                        </td>
                        <td><div class="badge-glass badge-glass--<?= $status['badge'] ?>"><span class="badge-glass--text"><?= $status['label'] ?></span></div></td>
                        <td class="text-xs text-gray-400"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                        <td>
                            <a href="<?= $e($viewUrl) ?>" class="origin-btn btn--graphic btn--primary" title="Voir la fiche">
                                <i data-lucide="eye" class="btn--icon"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        $current = $result['page'];
        $last    = $result['pages'];
        include __DIR__ . '/../elements/pagination.php';
        ?>
        <?php else: ?>

        <!-- État vide (avant recherche) -->
        <div id="empty-zone" class="card-glass-panel text-center" style="padding: 4rem 2rem;">
            <i data-lucide="user-search" class="w-16 h-16 text-cyan-900 mx-auto mb-6"></i>
            <p class="text-gray-400 text-sm uppercase tracking-widest text-center m-0 pb-2">Aucun profil sélectionné</p>
            <p class="text-gray-600 text-xs text-center m-0">Recherchez un joueur par son pseudo Minecraft ou son pseudo du site pour afficher sa fiche.</p>

            <div id="recent-zone" class="hidden" style="margin-top: 2.5rem;">
                <p class="text-gray-500 text-xs uppercase tracking-widest text-center pb-4">Recherches récentes</p>
                <div class="recent-searches" id="recent-searches"></div>
            </div>
        </div>
        <?php endif; ?>
<?php endif; ?>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<script>
    lucide.createIcons();

<?php if ($canViewProfiles): ?>
    const RECENT_KEY = 'origin-profile-recent';
    const RECENT_MAX = 6;

    function getRecent() {
        try {
            return JSON.parse(localStorage.getItem(RECENT_KEY)) || [];
        } catch (e) {
            return [];
        }
    }

    function saveRecent(query) {
        const list = getRecent().filter(q => q.toLowerCase() !== query.toLowerCase());
        list.unshift(query);
        try {
            localStorage.setItem(RECENT_KEY, JSON.stringify(list.slice(0, RECENT_MAX)));
        } catch (e) {}
    }

    function renderRecent() {
        const list = getRecent();
        if (!list.length) return;

        const container = document.getElementById('recent-searches');
        list.forEach(query => {
            const url = new URL(window.location.href);
            url.searchParams.set('user', query);
            url.searchParams.delete('p');

            const link = document.createElement('a');
            link.href = url.toString();
            link.className = 'badge-glass badge-glass--primary';

            const text = document.createElement('span');
            text.className = 'badge-glass--text';
            text.textContent = query;

            link.appendChild(text);
            container.appendChild(link);
        });
        document.getElementById('recent-zone').classList.remove('hidden');
    }

    <?php if ($query !== ''): ?>
    // Mémorise la recherche seulement si elle a donné des résultats
    <?php if ($result['total'] > 0): ?>
    saveRecent(<?= json_encode($query, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);
    <?php endif; ?>
    <?php if ($exact): ?>
    window.location.replace(<?= json_encode('./dashboard?page=profil-view&id=' . urlencode($exact['id'])) ?>);
    <?php endif; ?>
    <?php else: ?>
    renderRecent();
    <?php endif; ?>
<?php endif; ?>
</script>

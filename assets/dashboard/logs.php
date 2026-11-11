<?php
$canViewLogs = $groupManager->userCan($auth->user(), 'view-logs');

if ($canViewLogs) {
    $filters = Logger::filtersFrom($_GET);
    $result  = Logger::search($filters, (int) ($_GET['p'] ?? 1));
    $stats   = Logger::todayStats();

    $activeFilters = ($filters['type'] !== '') + ($filters['period'] !== 'all') + ($filters['sort'] !== 'recent');
    $query         = array_filter($filters, fn($v, $k) => $v !== '' && !($k === 'period' && $v === 'all') && !($k === 'sort' && $v === 'recent'), ARRAY_FILTER_USE_BOTH);

    $pageUrl   = fn(int $p) => './dashboard?' . http_build_query(['page' => 'logs'] + $query + ($p > 1 ? ['p' => $p] : []));
    $exportUrl = './assets/api/logs-export' . ($query ? '?' . http_build_query($query) : '');
    $e         = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
}
?>
<style>
    .log-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding: 0 1rem;
        margin-bottom: 0;
    }
    .log-toolbar--search {
        display: flex;
        align-items: center;
        flex: 1 1 240px;
        max-width: 480px;
    }
    .log-toolbar--search input {
        flex: 1;
        width: auto !important;
        margin: 0 !important;
        border-right: none !important;
    }
    .log-toolbar--actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    @media (max-width: 767px) {
        .log-toolbar { flex-direction: column; align-items: stretch; padding: 0.5rem; }
        .log-toolbar--search { max-width: none; }
        .log-toolbar--actions { flex-direction: column; }
        .log-toolbar--actions .origin-btn { width: 100%; justify-content: center; }
    }
    .badge-glass { white-space: nowrap; }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">
        <h3 class="pb-8 text-cyan-400">Gestion des logs</h3>

<?php if (!$canViewLogs): ?>
        <p class="text-sm text-red-400">Vous n'avez pas la permission d'accéder aux logs.</p>
<?php else: ?>
        <!-- Stats rapides (aujourd'hui) -->
        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 2rem;">
            <div class="card-glass-panel" style="min-width: 140px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-cyan-400 mb-1"><?= $stats['total'] ?></div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Logs aujourd'hui</div>
            </div>
            <div class="card-glass-panel" style="min-width: 140px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-green-400 mb-1"><?= $stats['logins'] ?></div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Connexions</div>
            </div>
            <div class="card-glass-panel" style="min-width: 140px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-orange-400 mb-1"><?= $stats['tickets'] ?></div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Tickets créés</div>
            </div>
            <div class="card-glass-panel" style="min-width: 140px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-red-400 mb-1"><?= $stats['admin'] ?></div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Actions admin</div>
            </div>
        </div>

        <form method="GET" action="./dashboard">
            <input type="hidden" name="page" value="logs">

            <!-- Barre d'outils -->
            <div class="log-toolbar">
                <div class="log-toolbar--search">
                    <input type="text" name="q" id="log-search" value="<?= $e($filters['q']) ?>" placeholder="Rechercher un utilisateur, une action...">
                    <button type="submit" class="origin-btn btn--graphic btn--primary" style="flex-shrink: 0;">
                        <i data-lucide="search" class="btn--icon"></i>
                    </button>
                </div>
                <div class="log-toolbar--actions">
                    <button type="button" class="dropdown origin-btn btn--full-graphic btn--primary">
                        Filtres<?= $activeFilters ? " ($activeFilters)" : '' ?>
                        <i data-lucide="sliders-horizontal" class="btn--icon"></i>
                    </button>
                    <a href="<?= $e($exportUrl) ?>" class="origin-btn btn--full-graphic btn--secondary">
                        Exporter
                        <i data-lucide="download" class="btn--icon"></i>
                    </a>
                </div>
            </div>

            <!-- Filtres inline -->
            <div class="dropdown-container <?= $activeFilters ? '' : 'hidden' ?> w-full flex p-8 gap-4">
                <div style="width: 350px;">
                    <label>Type d'action</label>
                    <select name="type" style="width: 100%;" onchange="this.form.submit()">
                        <option value="">Tous</option>
                        <?php foreach (Logger::TYPES as $key => $t): ?>
                        <option value="<?= $key ?>" <?= $filters['type'] === $key ? 'selected' : '' ?>><?= $t['label'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="width: 350px;">
                    <label>Période</label>
                    <select name="period" style="width: 100%;" onchange="this.form.submit()">
                        <?php foreach (Logger::PERIODS as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $filters['period'] === $key ? 'selected' : '' ?>><?= $e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="width: 350px;">
                    <label>Ordre d'apparition</label>
                    <select name="sort" style="width: 100%;" onchange="this.form.submit()">
                        <?php foreach (Logger::SORTS as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $filters['sort'] === $key ? 'selected' : '' ?>><?= $e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>

        <!-- Table -->
        <table id="logs-table" style="margin-top: 0.5rem;">
            <thead>
                <tr>
                    <td width="175px">Horodatage</td>
                    <td width="150px">Utilisateur</td>
                    <td width="145px">Type</td>
                    <td>Détails</td>
                    <td width="120px">IP</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$result['rows']): ?>
                <tr>
                    <td colspan="5" class="text-center text-gray-500 py-8">Aucun log trouvé.</td>
                </tr>
                <?php endif; ?>

                <?php foreach ($result['rows'] as $log):
                    $type = Logger::TYPES[$log['type']] ?? ['label' => $log['type'], 'badge' => 'secondary'];
                ?>
                <tr>
                    <td class="text-xs text-gray-400 font-mono"><?= date('d/m/Y — H:i:s', strtotime($log['created_at'])) ?></td>
                    <td class="font-bold text-sm"><?= $log['username'] !== null ? $e($log['username']) : '<span class="text-gray-600">Système</span>' ?></td>
                    <td><div class="badge-glass badge-glass--<?= $type['badge'] ?>"><span class="badge-glass--text"><?= $e($type['label']) ?></span></div></td>
                    <td class="text-xs text-gray-400"><?= $e($log['details']) ?><?php if ($log['highlight'] !== null): ?><strong class="text-<?= $e($log['highlight_tone'] ?? 'cyan') ?>-400"><?= $e($log['highlight']) ?></strong><?php endif; ?></td>
                    <td class="text-xs text-gray-600 font-mono"><?= $e(Logger::maskIp($log['ip_address'])) ?></td>
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

<script>
    lucide.createIcons();
</script>

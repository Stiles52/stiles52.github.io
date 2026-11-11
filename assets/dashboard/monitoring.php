<?php
$canMonitor = $groupManager->userCan($auth->user(), 'view-monitoring');

if ($canMonitor) {
    $monitor->runCheck();
    $monitoring = $monitor->status();
}

// Carte de statut (contenu rempli par renderMonitoring())
$statusCard = function (string $key, string $icon, string $iconClass, string $title) {
    ?>
            <div class="card-glass-panel" style="min-width: 200px; flex: 1;">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-2 h-2 rounded-full bg-gray-500 animate-pulse" id="<?= $key ?>-dot"></div>
                    <span class="text-xs text-gray-500 uppercase tracking-widest" id="<?= $key ?>-state">—</span>
                </div>
                <h4 class="text-white mb-1 flex items-center gap-3">
                    <i data-lucide="<?= $icon ?>" class="w-5 h-5 <?= $iconClass ?>"></i> <?= $title ?>
                </h4>
                <p class="text-xs text-gray-500 m-0" id="<?= $key ?>-text">—</p>
            </div>
    <?php
};
?>
<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">
        <h3 class="pb-4 text-cyan-400">Monitoring du site</h3>
<?php if (!$canMonitor): ?>
        <p class="text-sm text-red-400 pt-8">Vous n'avez pas la permission d'accéder au monitoring.</p>
<?php else: ?>
        <p class="text-gray-500 text-xs uppercase tracking-widest pb-12">Dernière mise à jour : <span id="last-update">—</span></p>

        <!-- Statuts -->
        <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 2rem;">
            <?php
            $statusCard('site', 'globe', 'text-cyan-400', 'Site web');
            $statusCard('response', 'zap', 'text-yellow-400', 'Temps de réponse');
            $statusCard('online', 'users', 'text-purple-400', 'Membres connectés');
            $statusCard('server', 'server', 'text-orange-400', 'Serveur');
            ?>
        </div>

        <!-- Stats Tickets -->
        <h4 class="pb-4">Statistiques des tickets</h4>
        <div style="display: flex; gap: 15px; justify-content: space-between; flex-wrap: wrap; margin-bottom: 2rem;">
            <div class="card-glass-panel" style="min-width: 180px; width: calc(25% - 15px);">
                <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                    <i data-lucide="tickets" class="w-6 h-6 text-cyan-400"></i> <span id="tickets-total">—</span>
                </h3>
                Tickets totaux
            </div>
            <div class="card-glass-panel" style="min-width: 180px; width: calc(25% - 15px);">
                <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                    <i data-lucide="ticket-minus" class="w-6 h-6 text-orange-400"></i> <span id="tickets-in_progress">—</span>
                </h3>
                En cours de traitement
            </div>
            <div class="card-glass-panel" style="min-width: 180px; width: calc(25% - 15px);">
                <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                    <i data-lucide="ticket-plus" class="w-6 h-6 text-yellow-400"></i> <span id="tickets-waiting">—</span>
                </h3>
                En attente de réponse
            </div>
            <div class="card-glass-panel" style="min-width: 180px; width: calc(25% - 15px);">
                <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                    <i data-lucide="ticket-check" class="w-6 h-6 text-green-400"></i> <span id="tickets-resolved">—</span>
                </h3>
                Tickets résolus
            </div>
        </div>

        <!-- Activité récente -->
        <h4 class="pb-4">Activité récente des membres</h4>
        <div class="mt-2">
            <table>
                <thead>
                    <tr>
                        <td width="180px">Horodatage</td>
                        <td width="180px">Utilisateur</td>
                        <td width="160px">Action</td>
                        <td>Détails</td>
                    </tr>
                </thead>
                <tbody id="activity-body"></tbody>
            </table>
            <?php if ($groupManager->userCan($auth->user(), 'view-logs')): ?>
            <div class="flex justify-end mt-6 mb-8">
                <a href="./dashboard?page=logs" class="origin-btn btn--full-graphic btn--primary">
                    <span>Voir tous les logs</span>
                    <i data-lucide="arrow-big-right" class="btn--icon"></i>
                </a>
            </div>
            <?php endif; ?>
        </div>
<?php endif; ?>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<script>
    lucide.createIcons();

<?php if ($canMonitor): ?>
    const REFRESH_MS = 30000;
    const TONES = {
        green:  { dot: 'bg-green-500',  text: 'text-green-500' },
        orange: { dot: 'bg-orange-500', text: 'text-orange-400' },
        red:    { dot: 'bg-red-500',    text: 'text-red-500' },
        gray:   { dot: 'bg-gray-500',   text: 'text-gray-500' },
    };

    function renderCard(key, card) {
        const tone = TONES[card.tone] || TONES.gray;
        document.getElementById(key + '-dot').className   = 'w-2 h-2 rounded-full animate-pulse ' + tone.dot;
        document.getElementById(key + '-state').className = 'text-xs uppercase tracking-widest ' + tone.text;
        document.getElementById(key + '-state').textContent = card.state;
        document.getElementById(key + '-text').textContent  = card.text;
    }

    function cell(text, className) {
        const td = document.createElement('td');
        if (className) td.className = className;
        td.textContent = text;
        return td;
    }

    function renderMonitoring(data) {
        document.getElementById('last-update').textContent = data.updated_at;
        ['site', 'response', 'online', 'server'].forEach(key => renderCard(key, data[key]));

        ['total', 'in_progress', 'waiting', 'resolved'].forEach(key => {
            const v = data.tickets ? data.tickets[key] : null;
            document.getElementById('tickets-' + key).textContent = v === null || v === undefined ? '—' : v.toLocaleString('fr-FR');
        });

        const body = document.getElementById('activity-body');
        body.replaceChildren();
        if (!data.activity.length) {
            const tr = document.createElement('tr');
            const td = cell('Aucune activité enregistrée.', 'text-center text-xs text-gray-600 py-6');
            td.colSpan = 4;
            tr.append(td);
            body.append(tr);
        }
        data.activity.forEach(a => {
            const tr    = document.createElement('tr');
            const badge = document.createElement('td');
            badge.innerHTML = '<div class="badge-glass"><span class="badge-glass--text"></span></div>';
            badge.firstChild.classList.add('badge-glass--' + a.badge);
            badge.querySelector('span').textContent = a.label;
            tr.append(cell(a.date, 'text-xs text-gray-400'), cell(a.user), badge, cell(a.details, 'text-xs text-gray-400'));
            body.append(tr);
        });
    }

    async function refreshMonitoring() {
        try {
            const res  = await fetch('assets/api/monitoring-status', { cache: 'no-store' });
            const data = await res.json();
            if (data.success) renderMonitoring(data);
        } catch (e) {
            renderCard('site', { tone: 'red', state: 'Injoignable', text: 'Impossible de contacter le serveur' });
        }
    }

    renderMonitoring(<?= json_encode($monitoring, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>);
    setInterval(refreshMonitoring, REFRESH_MS);
<?php endif; ?>
</script>

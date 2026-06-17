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

        <!-- Stats rapides -->
        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 2rem;">
            <div class="card-glass-panel" style="min-width: 140px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-cyan-400 mb-1">48</div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Logs aujourd'hui</div>
            </div>
            <div class="card-glass-panel" style="min-width: 140px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-green-400 mb-1">12</div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Connexions</div>
            </div>
            <div class="card-glass-panel" style="min-width: 140px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-orange-400 mb-1">7</div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Tickets créés</div>
            </div>
            <div class="card-glass-panel" style="min-width: 140px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-red-400 mb-1">3</div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Actions admin</div>
            </div>
        </div>

        <!-- Barre d'outils -->
        <div class="log-toolbar">
            <div class="log-toolbar--search">
                <input type="text" id="log-search" placeholder="Rechercher un utilisateur, une action...">
                <button onclick="searchLogs()" class="origin-btn btn--graphic btn--primary" style="flex-shrink: 0;">
                    <i data-lucide="search" class="btn--icon"></i>
                </button>
            </div>
            <div class="log-toolbar--actions">
                <button class="dropdown origin-btn btn--full-graphic btn--primary">
                    Filtres
                    <i data-lucide="sliders-horizontal" class="btn--icon"></i>
                </button>
                <button class="origin-btn btn--full-graphic btn--secondary">
                    Exporter
                    <i data-lucide="download" class="btn--icon"></i>
                </button>
            </div>
        </div>

        <!-- Filtres inline -->
        <div class="dropdown-container hidden w-full flex p-8 gap-4">
            <div style="width: 350px;">
                <label>Type d'action</label>
                <select style="width: 100%;">
                    <option>Tous</option>
                    <option>Connexion</option>
                    <option>Déconnexion</option>
                    <option>Ticket créé</option>
                    <option>Ticket résolu</option>
                    <option>Action admin</option>
                    <option>Modification</option>
                </select>
            </div>
            <div style="width: 350px;">
                <label>Période</label>
                <select style="width: 100%;">
                    <option>Aujourd'hui</option>
                    <option>7 derniers jours</option>
                    <option>30 derniers jours</option>
                    <option>Tout</option>
                </select>
            </div>
            <div style="width: 350px;">
                <label>Ordre d'apparition</label>
                <select style="width: 100%;">
                    <option>Plus récent</option>
                    <option>Plus ancien</option>
                </select>
            </div>
        </div>

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
                <tr>
                    <td class="text-xs text-gray-400 font-mono">17/06/2026 — 14:32:08</td>
                    <td class="font-bold text-sm">Mathis150</td>
                    <td><div class="badge-glass badge-glass--primary"><span class="badge-glass--text">Connexion</span></div></td>
                    <td class="text-xs text-gray-400">Connexion réussie — Dashboard</td>
                    <td class="text-xs text-gray-600 font-mono">192.168.1.x</td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-400 font-mono">17/06/2026 — 14:18:44</td>
                    <td class="font-bold text-sm">NovaThorn</td>
                    <td><div class="badge-glass badge-glass--info"><span class="badge-glass--text">Ticket créé</span></div></td>
                    <td class="text-xs text-gray-400">Ticket #0001249 ouvert — "Signalement de bug : chute hors map"</td>
                    <td class="text-xs text-gray-600 font-mono">10.0.0.x</td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-400 font-mono">17/06/2026 — 13:55:11</td>
                    <td class="font-bold text-sm">LunaVex</td>
                    <td><div class="badge-glass badge-glass--success"><span class="badge-glass--text">Ticket résolu</span></div></td>
                    <td class="text-xs text-gray-400">Ticket #0001241 marqué résolu — "Débanissement"</td>
                    <td class="text-xs text-gray-600 font-mono">172.16.0.x</td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-400 font-mono">17/06/2026 — 13:40:59</td>
                    <td class="font-bold text-sm">KaelDrift</td>
                    <td><div class="badge-glass badge-glass--primary"><span class="badge-glass--text">Connexion</span></div></td>
                    <td class="text-xs text-gray-400">Connexion réussie — Dashboard</td>
                    <td class="text-xs text-gray-600 font-mono">10.0.1.x</td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-400 font-mono">17/06/2026 — 13:22:03</td>
                    <td class="font-bold text-sm">Mathis150</td>
                    <td><div class="badge-glass badge-glass--danger"><span class="badge-glass--text">Action admin</span></div></td>
                    <td class="text-xs text-gray-400">Utilisateur "Zork_92" — statut modifié : <strong class="text-red-400">Suspendu</strong></td>
                    <td class="text-xs text-gray-600 font-mono">192.168.1.x</td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-400 font-mono">17/06/2026 — 12:58:17</td>
                    <td class="font-bold text-sm">Mathis150</td>
                    <td><div class="badge-glass badge-glass--warning"><span class="badge-glass--text">Modification</span></div></td>
                    <td class="text-xs text-gray-400">Groupe "Scénariste" — permission "manage-tickets" ajoutée</td>
                    <td class="text-xs text-gray-600 font-mono">192.168.1.x</td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-400 font-mono">17/06/2026 — 12:48:00</td>
                    <td class="font-bold text-sm">Zark_r</td>
                    <td><div class="badge-glass badge-glass--primary"><span class="badge-glass--text">Connexion</span></div></td>
                    <td class="text-xs text-gray-400">Connexion réussie — Dashboard</td>
                    <td class="text-xs text-gray-600 font-mono">172.16.2.x</td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-400 font-mono">17/06/2026 — 12:31:45</td>
                    <td class="font-bold text-sm">LunaVex</td>
                    <td><div class="badge-glass badge-glass--info"><span class="badge-glass--text">Ticket créé</span></div></td>
                    <td class="text-xs text-gray-400">Ticket #0001248 ouvert — "Question & aide : lore métier"</td>
                    <td class="text-xs text-gray-600 font-mono">172.16.0.x</td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-400 font-mono">17/06/2026 — 11:59:22</td>
                    <td class="font-bold text-sm">NovaThorn</td>
                    <td><div class="badge-glass badge-glass--secondary"><span class="badge-glass--text">Déconnexion</span></div></td>
                    <td class="text-xs text-gray-400">Session terminée — durée : 1h 22min</td>
                    <td class="text-xs text-gray-600 font-mono">10.0.0.x</td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-400 font-mono">17/06/2026 — 11:10:05</td>
                    <td class="font-bold text-sm">Mathis150</td>
                    <td><div class="badge-glass badge-glass--danger"><span class="badge-glass--text">Action admin</span></div></td>
                    <td class="text-xs text-gray-400">Candidature #0000412 — statut modifié : <strong class="text-green-400">Acceptée</strong></td>
                    <td class="text-xs text-gray-600 font-mono">192.168.1.x</td>
                </tr>
            </tbody>
        </table>

        <div class="flex justify-end mt-8 mb-24 gap-4">
            <a href="#" class="origin-btn btn--graphic btn--primary">
                <i data-lucide="arrow-big-left" class="btn--icon"></i>
            </a>
            <a href="#" class="origin-btn btn--full-graphic btn--primary"><span>1</span></a>
            <a href="#" class="origin-btn btn--full-graphic btn--primary"><span>2</span></a>
            <a href="#" class="origin-btn btn--full-graphic btn--primary"><span>3</span></a>
            <a href="#" class="origin-btn btn--full-graphic btn--secondary"><span>...</span></a>
            <a href="#" class="origin-btn btn--graphic btn--primary">
                <i data-lucide="arrow-big-right" class="btn--icon"></i>
            </a>
        </div>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<script>
    lucide.createIcons();

    function searchLogs() {
        const query = document.getElementById('log-search').value.toLowerCase();
        const rows = document.querySelectorAll('#logs-table tbody tr');
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
        });
    }

    document.getElementById('log-search').addEventListener('keydown', e => {
        if (e.key === 'Enter') searchLogs();
    });
</script>

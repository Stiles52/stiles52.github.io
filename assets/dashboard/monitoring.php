<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">
        <h3 class="pb-4 text-cyan-400">Monitoring du site</h3>
        <p class="text-gray-500 text-xs uppercase tracking-widest pb-12">Dernière mise à jour : <span id="last-update">—</span></p>

        <!-- Statuts -->
        <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 2rem;">
            <div class="card-glass-panel" style="min-width: 200px; flex: 1;">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="text-xs text-green-500 uppercase tracking-widest">Opérationnel</span>
                </div>
                <h4 class="text-white mb-1 flex items-center gap-3">
                    <i data-lucide="globe" class="w-5 h-5 text-cyan-400"></i> Site web
                </h4>
                <p class="text-xs text-gray-500 m-0">Uptime : 99,8%</p>
            </div>
            <div class="card-glass-panel" style="min-width: 200px; flex: 1;">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="text-xs text-green-500 uppercase tracking-widest">Nominal</span>
                </div>
                <h4 class="text-white mb-1 flex items-center gap-3">
                    <i data-lucide="zap" class="w-5 h-5 text-yellow-400"></i> Temps de réponse
                </h4>
                <p class="text-xs text-gray-500 m-0">Moy. : 142 ms</p>
            </div>
            <div class="card-glass-panel" style="min-width: 200px; flex: 1;">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="text-xs text-green-500 uppercase tracking-widest">Actif</span>
                </div>
                <h4 class="text-white mb-1 flex items-center gap-3">
                    <i data-lucide="users" class="w-5 h-5 text-purple-400"></i> Membres connectés
                </h4>
                <p class="text-xs text-gray-500 m-0">12 membres en ligne</p>
            </div>
            <div class="card-glass-panel" style="min-width: 200px; flex: 1;">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></div>
                    <span class="text-xs text-orange-400 uppercase tracking-widest">Chargé</span>
                </div>
                <h4 class="text-white mb-1 flex items-center gap-3">
                    <i data-lucide="server" class="w-5 h-5 text-orange-400"></i> Serveur
                </h4>
                <p class="text-xs text-gray-500 m-0">CPU : 64% — RAM : 3.2 / 8 Go</p>
            </div>
        </div>

        <!-- Stats Tickets -->
        <h4 class="pb-4">Statistiques des tickets</h4>
        <div style="display: flex; gap: 15px; justify-content: space-between; flex-wrap: wrap; margin-bottom: 2rem;">
            <div class="card-glass-panel" style="min-width: 180px; width: calc(25% - 15px);">
                <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                    <i data-lucide="tickets" class="w-6 h-6 text-cyan-400"></i> 1 248
                </h3>
                Tickets totaux
            </div>
            <div class="card-glass-panel" style="min-width: 180px; width: calc(25% - 15px);">
                <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                    <i data-lucide="ticket-minus" class="w-6 h-6 text-orange-400"></i> 34
                </h3>
                En cours de traitement
            </div>
            <div class="card-glass-panel" style="min-width: 180px; width: calc(25% - 15px);">
                <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                    <i data-lucide="ticket-plus" class="w-6 h-6 text-yellow-400"></i> 12
                </h3>
                En attente de réponse
            </div>
            <div class="card-glass-panel" style="min-width: 180px; width: calc(25% - 15px);">
                <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                    <i data-lucide="ticket-check" class="w-6 h-6 text-green-400"></i> 1 202
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
                <tbody>
                    <tr>
                        <td class="text-xs text-gray-400">17/06/2026 — 14:32</td>
                        <td>Mathis150</td>
                        <td><div class="badge-glass badge-glass--primary"><span class="badge-glass--text">Connexion</span></div></td>
                        <td class="text-xs text-gray-400">IP : 192.168.x.x — Dashboard</td>
                    </tr>
                    <tr>
                        <td class="text-xs text-gray-400">17/06/2026 — 14:18</td>
                        <td>NovaThorn</td>
                        <td><div class="badge-glass badge-glass--info"><span class="badge-glass--text">Ticket créé</span></div></td>
                        <td class="text-xs text-gray-400">Ticket #0001249 — Signalement de bug</td>
                    </tr>
                    <tr>
                        <td class="text-xs text-gray-400">17/06/2026 — 13:55</td>
                        <td>LunaVex</td>
                        <td><div class="badge-glass badge-glass--success"><span class="badge-glass--text">Ticket résolu</span></div></td>
                        <td class="text-xs text-gray-400">Ticket #0001241 — Débanissement</td>
                    </tr>
                    <tr>
                        <td class="text-xs text-gray-400">17/06/2026 — 13:40</td>
                        <td>KaelDrift</td>
                        <td><div class="badge-glass badge-glass--warning"><span class="badge-glass--text">Connexion</span></div></td>
                        <td class="text-xs text-gray-400">IP : 10.0.x.x — Dashboard</td>
                    </tr>
                    <tr>
                        <td class="text-xs text-gray-400">17/06/2026 — 13:22</td>
                        <td>Mathis150</td>
                        <td><div class="badge-glass badge-glass--danger"><span class="badge-glass--text">Action admin</span></div></td>
                        <td class="text-xs text-gray-400">Utilisateur "Zork_92" suspendu</td>
                    </tr>
                    <tr>
                        <td class="text-xs text-gray-400">17/06/2026 — 12:48</td>
                        <td>Zark_r</td>
                        <td><div class="badge-glass badge-glass--primary"><span class="badge-glass--text">Connexion</span></div></td>
                        <td class="text-xs text-gray-400">IP : 172.16.x.x — Dashboard</td>
                    </tr>
                    <tr>
                        <td class="text-xs text-gray-400">17/06/2026 — 12:10</td>
                        <td>NovaThorn</td>
                        <td><div class="badge-glass badge-glass--info"><span class="badge-glass--text">Ticket créé</span></div></td>
                        <td class="text-xs text-gray-400">Ticket #0001248 — Question & aide</td>
                    </tr>
                </tbody>
            </table>
            <div class="flex justify-end mt-6 mb-8">
                <a href="./dashboard?page=logs" class="origin-btn btn--full-graphic btn--primary">
                    <span>Voir tous les logs</span>
                    <i data-lucide="arrow-big-right" class="btn--icon"></i>
                </a>
            </div>
        </div>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<script>
    lucide.createIcons();
    document.getElementById('last-update').textContent = new Date().toLocaleString('fr-FR');
</script>

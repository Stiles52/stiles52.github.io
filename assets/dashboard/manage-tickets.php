<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">
        <h3 class="pb-4 text-cyan-400">Gestion des tickets</h3>
        <p class="text-gray-500 text-xs uppercase tracking-widest pb-12">Vue staff — tous les tickets toutes catégories confondues</p>

        <!-- Stats rapides -->
        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 2rem;">
            <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-cyan-400 mb-1">12</div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Non assignés</div>
            </div>
            <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-orange-400 mb-1">34</div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">En cours</div>
            </div>
            <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-yellow-400 mb-1">8</div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">En attente</div>
            </div>
            <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-green-400 mb-1">1 202</div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Résolus</div>
            </div>
        </div>

        <!-- Barre d'outils -->
        <div class="page-toolbar">
            <input type="text" id="ticket-search" placeholder="Recherche">
            <button onclick="searchTickets()" class="origin-btn btn--graphic btn--primary">
                <i data-lucide="search" class="btn--icon"></i>
            </button>
            <button class="dropdown origin-btn btn--full-graphic btn--primary">
                Filtres
                <i data-lucide="sliders-horizontal" class="btn--icon"></i>
            </button>
        </div>
        <div class="dropdown-container hidden w-full flex p-8 gap-4">
            <div style="width: 350px;">
                <label>Statut</label>
                <select style="width: 100%;">
                    <option>Tous</option>
                    <option>Non assigné</option>
                    <option>En cours</option>
                    <option>En attente</option>
                    <option>Résolu</option>
                </select>
            </div>
            <div style="width: 350px;">
                <label>Catégorie</label>
                <select style="width: 100%;">
                    <option>Toutes</option>
                    <option>Bug</option>
                    <option>Modération</option>
                    <option>Haut-Staff</option>
                    <option>Recrutement</option>
                    <option>Débanissement</option>
                    <option>Scénarisation</option>
                    <option>Question & Aide</option>
                    <option>Développement</option>
                    <option>Spécialisations</option>
                    <option>Construction</option>
                    <option>Animation</option>
                </select>
            </div>
            <div style="width: 350px;">
                <label>Assigné à</label>
                <select style="width: 100%;">
                    <option>Tous</option>
                    <option>Mathis150</option>
                    <option>NovaThorn</option>
                    <option>LunaVex</option>
                    <option>— Non assigné</option>
                </select>
            </div>
            <div style="width: 350px;">
                <label>Ordre d'apparition</label>
                <select style="width: 100%;">
                    <option>Réponse la plus récente</option>
                    <option>Réponse la moins récente</option>
                    <option>Ordre alphabétique</option>
                    <option>Ordre de création</option>
                </select>
            </div>
        </div>

        <table id="manage-tickets-table">
            <thead>
                <tr>
                    <td width="90px">ID#</td>
                    <td width="140px">Statut</td>
                    <td width="200px">Auteur</td>
                    <td>Titre</td>
                    <td width="150px">Catégorie</td>
                    <td width="160px">Assigné à</td>
                    <td width="80px">Actions</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-xs text-gray-500">#0001249</td>
                    <td><div class="badge-glass badge-glass--danger"><span class="badge-glass--text">Non assigné</span></div></td>
                    <td class="text-sm">KaelDrift</td>
                    <td class="text-sm">Chute hors de la map en zone minière</td>
                    <td><div class="badge-glass badge-glass--warning"><span class="badge-glass--text">Bug</span></div></td>
                    <td><span class="text-xs text-gray-500">—</span></td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="#">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir le ticket
                                </a>
                                <a href="#">
                                    <i data-lucide="user-check" class="btn--icon"></i>
                                    S'assigner
                                </a>
                                <a href="#">
                                    <i data-lucide="pen" class="btn--icon"></i>
                                    Éditer
                                </a>
                                <a href="#">
                                    <i data-lucide="ticket-x" class="btn--icon"></i>
                                    Fermer
                                </a>
                            </div>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-500">#0001248</td>
                    <td><div class="badge-glass badge-glass--warning"><span class="badge-glass--text">En cours</span></div></td>
                    <td class="text-sm">NovaThorn</td>
                    <td class="text-sm">Question sur le lore du métier de Médecin</td>
                    <td><div class="badge-glass badge-glass--primary"><span class="badge-glass--text">Q&A</span></div></td>
                    <td class="text-xs text-cyan-400">LunaVex</td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="#">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir le ticket
                                </a>
                                <a href="#">
                                    <i data-lucide="user-check" class="btn--icon"></i>
                                    Réassigner
                                </a>
                                <a href="#">
                                    <i data-lucide="pen" class="btn--icon"></i>
                                    Éditer
                                </a>
                                <a href="#">
                                    <i data-lucide="ticket-x" class="btn--icon"></i>
                                    Fermer
                                </a>
                            </div>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-500">#0001247</td>
                    <td><div class="badge-glass badge-glass--danger"><span class="badge-glass--text">Non assigné</span></div></td>
                    <td class="text-sm">Zark_r</td>
                    <td class="text-sm">Demande d'accès Haut-Staff — suivi dossier</td>
                    <td><div class="badge-glass badge-glass--danger"><span class="badge-glass--text">Haut-Staff</span></div></td>
                    <td><span class="text-xs text-gray-500">—</span></td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="#">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir le ticket
                                </a>
                                <a href="#">
                                    <i data-lucide="user-check" class="btn--icon"></i>
                                    S'assigner
                                </a>
                                <a href="#">
                                    <i data-lucide="pen" class="btn--icon"></i>
                                    Éditer
                                </a>
                                <a href="#">
                                    <i data-lucide="ticket-x" class="btn--icon"></i>
                                    Fermer
                                </a>
                            </div>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-500">#0001246</td>
                    <td><div class="badge-glass badge-glass--secondary"><span class="badge-glass--text">En attente</span></div></td>
                    <td class="text-sm">SkyFen</td>
                    <td class="text-sm">Contestation de sanction — ban du 12/06</td>
                    <td><div class="badge-glass badge-glass--danger"><span class="badge-glass--text">Déban.</span></div></td>
                    <td class="text-xs text-cyan-400">NovaThorn</td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="#">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir le ticket
                                </a>
                                <a href="#">
                                    <i data-lucide="user-check" class="btn--icon"></i>
                                    Réassigner
                                </a>
                                <a href="#">
                                    <i data-lucide="pen" class="btn--icon"></i>
                                    Éditer
                                </a>
                                <a href="#">
                                    <i data-lucide="ticket-x" class="btn--icon"></i>
                                    Fermer
                                </a>
                            </div>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-500">#0001241</td>
                    <td><div class="badge-glass badge-glass--success"><span class="badge-glass--text">Résolu</span></div></td>
                    <td class="text-sm">LunaVex</td>
                    <td class="text-sm">Problème de connexion répété depuis 3 jours</td>
                    <td><div class="badge-glass badge-glass--primary"><span class="badge-glass--text">Q&A</span></div></td>
                    <td class="text-xs text-cyan-400">Mathis150</td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="#">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir le ticket
                                </a>
                                <a href="#">
                                    <i data-lucide="rotate-ccw" class="btn--icon"></i>
                                    Rouvrir
                                </a>
                            </div>
                        </button>
                    </td>
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

    function searchTickets() {
        const query = document.getElementById('ticket-search').value.toLowerCase();
        const rows = document.querySelectorAll('#manage-tickets-table tbody tr');
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
        });
    }

    document.getElementById('ticket-search').addEventListener('keydown', e => {
        if (e.key === 'Enter') searchTickets();
    });
</script>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">
        <h3 class="pb-4 text-cyan-400">Gestion des candidatures</h3>
        <p class="text-gray-500 text-xs uppercase tracking-widest pb-12">Candidatures de whitelist — accès au serveur OriginRP</p>

        <!-- Stats -->
        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 2rem;">
            <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-yellow-400 mb-1">7</div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">En attente</div>
            </div>
            <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-green-400 mb-1">412</div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Acceptées</div>
            </div>
            <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-red-400 mb-1">38</div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Refusées</div>
            </div>
            <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
                <div class="text-2xl font-bold text-cyan-400 mb-1">457</div>
                <div class="text-xs text-gray-500 uppercase tracking-widest">Total</div>
            </div>
        </div>

        <!-- Outils -->
        <div class="page-toolbar">
            <div class="page-toolbar--search">
                <input type="text" id="cand-search" placeholder="Rechercher un pseudo...">
                <button onclick="searchCandidatures()" class="origin-btn btn--graphic btn--primary" style="flex-shrink:0;">
                    <i data-lucide="search" class="btn--icon"></i>
                </button>
            </div>
            <div class="page-toolbar--actions">
                <button class="dropdown origin-btn btn--full-graphic btn--primary">
                    Filtres
                    <i data-lucide="sliders-horizontal" class="btn--icon"></i>
                </button>
            </div>
        </div>
        <div class="dropdown-container hidden w-full flex p-8 gap-4">
            <div style="width: 350px;">
                <label>Statut</label>
                <select style="width: 100%;">
                    <option>Tous</option>
                    <option>En attente</option>
                    <option>Acceptée</option>
                    <option>Refusée</option>
                </select>
            </div>
            <div style="width: 350px;">
                <label>Ordre d'apparition</label>
                <select style="width: 100%;">
                    <option>Date (récent)</option>
                    <option>Date (ancien)</option>
                    <option>Alphabétique</option>
                </select>
            </div>
        </div>

        <table id="cand-table">
            <thead>
                <tr>
                    <td width="80px">ID#</td>
                    <td width="180px">Pseudo Minecraft</td>
                    <td width="180px">Pseudo (site)</td>
                    <td width="160px">Date de dépôt</td>
                    <td width="150px">Statut</td>
                    <td>Commentaire</td>
                    <td width="80px">Actions</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-xs text-gray-500">#0457</td>
                    <td class="text-cyan-400 font-mono text-sm">StarRolen</td>
                    <td>StarRolen</td>
                    <td class="text-xs text-gray-400">17/06/2026</td>
                    <td><div class="badge-glass badge-glass--warning"><span class="badge-glass--text">En attente</span></div></td>
                    <td class="text-xs text-gray-500">—</td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="#" onclick="openDetail('StarRolen', '0457')">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir la candidature
                                </a>
                                <a href="#" class="text-green-400">
                                    <i data-lucide="check" class="btn--icon"></i>
                                    Accepter
                                </a>
                                <a href="#" class="text-red-400">
                                    <i data-lucide="x" class="btn--icon"></i>
                                    Refuser
                                </a>
                            </div>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-500">#0456</td>
                    <td class="text-cyan-400 font-mono text-sm">IronLore_R</td>
                    <td>IronLore</td>
                    <td class="text-xs text-gray-400">16/06/2026</td>
                    <td><div class="badge-glass badge-glass--warning"><span class="badge-glass--text">En attente</span></div></td>
                    <td class="text-xs text-gray-500">—</td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="#" onclick="openDetail('IronLore_R', '0456')">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir la candidature
                                </a>
                                <a href="#" class="text-green-400">
                                    <i data-lucide="check" class="btn--icon"></i>
                                    Accepter
                                </a>
                                <a href="#" class="text-red-400">
                                    <i data-lucide="x" class="btn--icon"></i>
                                    Refuser
                                </a>
                            </div>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-500">#0455</td>
                    <td class="text-cyan-400 font-mono text-sm">Kael_Drift</td>
                    <td>KaelDrift</td>
                    <td class="text-xs text-gray-400">14/06/2026</td>
                    <td><div class="badge-glass badge-glass--success"><span class="badge-glass--text">Acceptée</span></div></td>
                    <td class="text-xs text-gray-400">Bonne candidature, RP convaincant.</td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="#" onclick="openDetail('Kael_Drift', '0455')">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir la candidature
                                </a>
                                <a href="#" class="text-red-400">
                                    <i data-lucide="rotate-ccw" class="btn--icon"></i>
                                    Annuler la décision
                                </a>
                            </div>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-500">#0454</td>
                    <td class="text-cyan-400 font-mono text-sm">Zork92</td>
                    <td>Zork_92</td>
                    <td class="text-xs text-gray-400">13/06/2026</td>
                    <td><div class="badge-glass badge-glass--danger"><span class="badge-glass--text">Refusée</span></div></td>
                    <td class="text-xs text-gray-400">Candidature trop courte, manque d'investissement RP.</td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="#" onclick="openDetail('Zork92', '0454')">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir la candidature
                                </a>
                                <a href="#" class="text-green-400">
                                    <i data-lucide="rotate-ccw" class="btn--icon"></i>
                                    Annuler la décision
                                </a>
                            </div>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-500">#0412</td>
                    <td class="text-cyan-400 font-mono text-sm">Zark_r</td>
                    <td>Zark_r</td>
                    <td class="text-xs text-gray-400">02/05/2026</td>
                    <td><div class="badge-glass badge-glass--success"><span class="badge-glass--text">Acceptée</span></div></td>
                    <td class="text-xs text-gray-400">Candidature exemplaire.</td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="#" onclick="openDetail('Zark_r', '0412')">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir la candidature
                                </a>
                                <a href="#" class="text-red-400">
                                    <i data-lucide="rotate-ccw" class="btn--icon"></i>
                                    Annuler la décision
                                </a>
                            </div>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="pagination">
            <a href="#" class="origin-btn btn--graphic btn--secondary">
                <i data-lucide="arrow-big-left" class="btn--icon"></i>
            </a>
            <a href="#" class="pagination--page pagination--current origin-btn btn--full-graphic btn--primary"><span>1</span></a>
            <a href="#" class="pagination--page origin-btn btn--full-graphic btn--secondary"><span>2</span></a>
            <a href="#" class="pagination--ellipsis origin-btn btn--full-graphic btn--secondary"><span>...</span></a>
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

    function searchCandidatures() {
        const query = document.getElementById('cand-search').value.toLowerCase();
        const rows = document.querySelectorAll('#cand-table tbody tr');
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
        });
    }

    document.getElementById('cand-search').addEventListener('keydown', e => {
        if (e.key === 'Enter') searchCandidatures();
    });

    function openDetail(pseudo, id) {
        // Placeholder — à connecter à une vue détail
        alert('Ouverture de la candidature #' + id + ' de ' + pseudo);
    }
</script>

<style>
    .profile-avatar {
        width: 80px; height: 80px;
        background: rgba(34, 211, 238, 0.1);
        border: 2px solid rgba(34, 211, 238, 0.3);
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; font-weight: bold; color: #22d3ee;
        flex-shrink: 0;
    }
    .profile-stat {
        background: rgba(0,0,0,0.6);
        border: 1px solid rgba(34, 211, 238, 0.15);
        padding: 1rem 1.5rem;
        flex: 1; min-width: 130px;
    }
    .profile-stat .value { font-size: 1.5rem; font-weight: bold; color: white; }
    .profile-stat .label { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.1em; margin-top: 4px; }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">
        <h3 class="pb-12 text-cyan-400">Visualisateur des profils</h3>

        <!-- Barre de recherche -->
        <div class="flex gap-4 mb-8 flex-wrap">
            <input type="text" id="profile-search" style="flex: 1; min-width: 250px;" placeholder="Pseudo Minecraft ou pseudo du site...">
            <button onclick="loadProfile()" class="origin-btn btn--full-graphic btn--primary">
                Rechercher
                <i data-lucide="search" class="btn--icon"></i>
            </button>
        </div>

        <!-- Zone profil (cachée par défaut, affichée après recherche) -->
        <div id="profile-zone" class="<?php echo isset($_GET['user']) ? '' : 'hidden'; ?>">

            <!-- En-tête profil -->
            <div class="card-glass-panel mb-6" style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
                <div class="profile-avatar" id="profile-avatar">M</div>
                <div style="flex: 1; min-width: 200px;">
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <h4 class="text-white" id="profile-name">Mathis150</h4>
                        <div class="badge-glass badge-glass--danger" id="profile-badge"><span class="badge-glass--text">Administrateur</span></div>
                        <div class="badge-glass badge-glass--success" id="profile-status"><span class="badge-glass--text">Actif</span></div>
                    </div>
                    <div class="flex items-center gap-2 mb-3">
                        <i data-lucide="monitor" class="w-4 h-4 text-gray-500"></i>
                        <span class="text-xs text-cyan-400 font-mono" id="profile-mc">mathis150</span>
                        <span class="text-xs text-gray-600">— Pseudo Minecraft</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
                        <span class="text-xs text-gray-400" id="profile-date">Inscrit le 01/01/2025 — Dernière connexion : 17/06/2026 à 14:32</span>
                    </div>
                </div>
                <div class="flex gap-3 flex-wrap">
                    <a href="./dashboard?page=users" class="origin-btn btn--graphic btn--primary text-sm">
                        <i data-lucide="pen" class="btn--icon"></i>
                        Éditer
                    </a>
                    <button class="origin-btn btn--graphic btn--warning text-sm">
                        <i data-lucide="user-x" class="btn--icon"></i>
                        Suspendre
                    </button>
                    <button class="origin-btn btn--graphic btn--danger text-sm">
                        <i data-lucide="ban" class="btn--icon"></i>
                        Bannir
                    </button>
                </div>
            </div>

            <!-- Stats rapides -->
            <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 1.5rem;">
                <div class="profile-stat">
                    <div class="value text-cyan-400" id="stat-tickets">14</div>
                    <div class="label">Tickets créés</div>
                </div>
                <div class="profile-stat">
                    <div class="value text-green-400" id="stat-resolved">11</div>
                    <div class="label">Tickets résolus</div>
                </div>
                <div class="profile-stat">
                    <div class="value text-red-400" id="stat-sanctions">0</div>
                    <div class="label">Sanctions</div>
                </div>
                <div class="profile-stat">
                    <div class="value text-purple-400" id="stat-cand">1</div>
                    <div class="label">Candidatures</div>
                </div>
            </div>

            <!-- Historique de sanctions -->
            <h4 class="pb-4">Historique des sanctions</h4>
            <div class="mb-8" id="sanctions-zone">
                <table>
                    <thead>
                        <tr>
                            <td width="160px">Date</td>
                            <td width="140px">Type</td>
                            <td>Raison</td>
                            <td width="160px">Infligé par</td>
                            <td width="140px">Statut</td>
                        </tr>
                    </thead>
                    <tbody id="sanctions-body">
                        <tr>
                            <td colspan="5" class="text-center text-xs text-gray-600 py-6">Aucune sanction enregistrée.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Historique des tickets -->
            <h4 class="pb-4">Historique des tickets</h4>
            <div id="tickets-zone">
                <table>
                    <thead>
                        <tr>
                            <td width="90px">ID#</td>
                            <td width="140px">Statut</td>
                            <td>Titre</td>
                            <td width="150px">Catégorie</td>
                            <td width="160px">Date</td>
                            <td width="80px">Actions</td>
                        </tr>
                    </thead>
                    <tbody id="tickets-body">
                        <tr>
                            <td class="text-xs text-gray-500">#0001241</td>
                            <td><div class="badge-glass badge-glass--success"><span class="badge-glass--text">Résolu</span></div></td>
                            <td class="text-sm">Problème de connexion répété</td>
                            <td><div class="badge-glass badge-glass--primary"><span class="badge-glass--text">Q&A</span></div></td>
                            <td class="text-xs text-gray-400">17/06/2026</td>
                            <td>
                                <button class="dropdown">
                                    <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                                    <div class="dropdown-container dropdown-container--popup hidden">
                                        <a href="#">
                                            <i data-lucide="eye" class="btn--icon"></i>
                                            Voir le ticket
                                        </a>
                                    </div>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-xs text-gray-500">#0001100</td>
                            <td><div class="badge-glass badge-glass--success"><span class="badge-glass--text">Résolu</span></div></td>
                            <td class="text-sm">Demande de spécialisation Médecin</td>
                            <td><div class="badge-glass badge-glass--warning"><span class="badge-glass--text">Spécialisations</span></div></td>
                            <td class="text-xs text-gray-400">12/03/2026</td>
                            <td>
                                <button class="dropdown">
                                    <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                                    <div class="dropdown-container dropdown-container--popup hidden">
                                        <a href="#">
                                            <i data-lucide="eye" class="btn--icon"></i>
                                            Voir le ticket
                                        </a>
                                    </div>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- État vide (avant recherche) -->
        <div id="empty-zone" class="<?php echo isset($_GET['user']) ? 'hidden' : ''; ?> text-center py-24">
            <i data-lucide="user-search" class="w-16 h-16 text-gray-700 mx-auto mb-6"></i>
            <p class="text-gray-600 text-sm uppercase tracking-widest m-0">Recherchez un joueur pour afficher son profil</p>
        </div>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<script>
    lucide.createIcons();

    <?php if(isset($_GET['user'])): ?>
    document.getElementById('profile-search').value = "<?php echo htmlspecialchars($_GET['user']); ?>";
    <?php endif; ?>

    function loadProfile() {
        const query = document.getElementById('profile-search').value.trim();
        if (!query) return;
        const url = new URL(window.location.href);
        url.searchParams.set('user', query);
        window.location.href = url.toString();
    }

    document.getElementById('profile-search').addEventListener('keydown', e => {
        if (e.key === 'Enter') loadProfile();
    });
</script>

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
        <div class="flex items-center justify-between pb-12">
            <h3 class="text-cyan-400">Gestion des permissions</h3>
            <button class="origin-btn btn--full-graphic btn--success" onclick="savePerms()">
                Sauvegarder les modifications
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
                        <th class="perm-group" style="min-width: 120px;">
                            <i data-lucide="crown" class="w-4 h-4 text-red-400 inline mb-1"></i><br>Admin
                        </th>
                        <th class="perm-group" style="min-width: 120px;">
                            <i data-lucide="shield" class="w-4 h-4 text-orange-400 inline mb-1"></i><br>Haut-Staff
                        </th>
                        <th class="perm-group" style="min-width: 120px;">
                            <i data-lucide="scale" class="w-4 h-4 text-purple-400 inline mb-1"></i><br>Modérateur
                        </th>
                        <th class="perm-group" style="min-width: 120px;">
                            <i data-lucide="scroll-text" class="w-4 h-4 text-pink-400 inline mb-1"></i><br>Scénariste
                        </th>
                        <th class="perm-group" style="min-width: 120px;">
                            <i data-lucide="code" class="w-4 h-4 text-green-400 inline mb-1"></i><br>Développeur
                        </th>
                        <th class="perm-group" style="min-width: 120px;">
                            <i data-lucide="user" class="w-4 h-4 text-gray-400 inline mb-1"></i><br>Joueur
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Tickets -->
                    <tr class="perm-section">
                        <td colspan="7">Tickets</td>
                    </tr>
                    <tr>
                        <td class="perm-name">create-ticket</td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                    </tr>
                    <tr>
                        <td class="perm-name">view-own-tickets</td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                    </tr>
                    <tr>
                        <td class="perm-name">manage-tickets</td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                    </tr>
                    <!-- Candidatures -->
                    <tr class="perm-section">
                        <td colspan="7">Candidatures</td>
                    </tr>
                    <tr>
                        <td class="perm-name">view-candidatures</td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                    </tr>
                    <tr>
                        <td class="perm-name">manage-candidatures</td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                    </tr>
                    <!-- Utilisateurs -->
                    <tr class="perm-section">
                        <td colspan="7">Utilisateurs</td>
                    </tr>
                    <tr>
                        <td class="perm-name">view-users</td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                    </tr>
                    <tr>
                        <td class="perm-name">manage-users</td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                    </tr>
                    <!-- Administration -->
                    <tr class="perm-section">
                        <td colspan="7">Administration</td>
                    </tr>
                    <tr>
                        <td class="perm-name">manage-groups</td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                    </tr>
                    <tr>
                        <td class="perm-name">manage-permissions</td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                    </tr>
                    <tr>
                        <td class="perm-name">view-logs</td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle active" onclick="togglePerm(this)"></div></td>
                        <td><div class="perm-toggle" onclick="togglePerm(this)"></div></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="save-feedback" class="hidden text-center text-sm text-green-400 mt-6 uppercase tracking-widest"></div>
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

    function savePerms() {
        const fb = document.getElementById('save-feedback');
        fb.textContent = '✓ Permissions sauvegardées avec succès';
        fb.classList.remove('hidden');
        setTimeout(() => fb.classList.add('hidden'), 3000);
    }
</script>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">
        <h3 class="pb-12 text-cyan-400">Gestion des utilisateurs</h3>

        <div class="page-toolbar">
            <input type="text" id="user-search" placeholder="Rechercher un pseudo...">
            <button onclick="searchUsers()" class="origin-btn btn--graphic btn--primary">
                <i data-lucide="search" class="btn--icon"></i>
            </button>
            <button class="dropdown origin-btn btn--full-graphic btn--primary">
                Filtres
                <i data-lucide="sliders-horizontal" class="btn--icon"></i>
            </button>
            <button class="origin-btn btn--full-graphic btn--success">
                Créer un utilisateur
                <i data-lucide="user-plus" class="btn--icon"></i>
            </button>
        </div>
        <div class="dropdown-container hidden w-full flex p-8 gap-4">
            <div style="width: 350px;">
                <label>Groupe</label>
                <select style="width: 100%;">
                    <option>Tous</option>
                    <option>Administrateur</option>
                    <option>Modérateur</option>
                    <option>Haut-Staff</option>
                    <option>Scénariste</option>
                    <option>Joueur</option>
                </select>
            </div>
            <div style="width: 350px;">
                <label>Statut</label>
                <select style="width: 100%;">
                    <option>Tous</option>
                    <option>Actif</option>
                    <option>Suspendu</option>
                    <option>Banni</option>
                </select>
            </div>
            <div style="width: 350px;">
                <label>Ordre d'apparition</label>
                <select style="width: 100%;">
                    <option>Date d'inscription (récent)</option>
                    <option>Alphabétique</option>
                    <option>Dernière connexion</option>
                </select>
            </div>
        </div>

        <table id="users-table">
            <thead>
                <tr>
                    <td width="60px">ID#</td>
                    <td width="180px">Pseudo (site)</td>
                    <td width="180px">Pseudo Minecraft</td>
                    <td width="160px">Groupe</td>
                    <td width="130px">Statut</td>
                    <td width="80px">Actions</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-xs text-gray-500">#001</td>
                    <td class="font-bold">Mathis150</td>
                    <td class="text-cyan-400 font-mono text-sm">mathis150</td>
                    <td><div class="badge-glass badge-glass--danger"><span class="badge-glass--text">Administrateur</span></div></td>
                    <td><div class="badge-glass badge-glass--success"><span class="badge-glass--text">Actif</span></div></td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="./dashboard?page=profil-visual&user=mathis150">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir le profil
                                </a>
                                <a href="#">
                                    <i data-lucide="pen" class="btn--icon"></i>
                                    Éditer
                                </a>
                                <a href="#">
                                    <i data-lucide="user-x" class="btn--icon"></i>
                                    Suspendre
                                </a>
                            </div>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-500">#002</td>
                    <td class="font-bold">NovaThorn</td>
                    <td class="text-cyan-400 font-mono text-sm">Nova_Thorn</td>
                    <td><div class="badge-glass badge-glass--warning"><span class="badge-glass--text">Modérateur</span></div></td>
                    <td><div class="badge-glass badge-glass--success"><span class="badge-glass--text">Actif</span></div></td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="./dashboard?page=profil-visual&user=Nova_Thorn">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir le profil
                                </a>
                                <a href="#">
                                    <i data-lucide="pen" class="btn--icon"></i>
                                    Éditer
                                </a>
                                <a href="#">
                                    <i data-lucide="user-x" class="btn--icon"></i>
                                    Suspendre
                                </a>
                            </div>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-500">#003</td>
                    <td class="font-bold">LunaVex</td>
                    <td class="text-cyan-400 font-mono text-sm">LunaVex_RP</td>
                    <td><div class="badge-glass badge-glass--primary"><span class="badge-glass--text">Scénariste</span></div></td>
                    <td><div class="badge-glass badge-glass--success"><span class="badge-glass--text">Actif</span></div></td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="./dashboard?page=profil-visual&user=LunaVex_RP">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir le profil
                                </a>
                                <a href="#">
                                    <i data-lucide="pen" class="btn--icon"></i>
                                    Éditer
                                </a>
                                <a href="#">
                                    <i data-lucide="user-x" class="btn--icon"></i>
                                    Suspendre
                                </a>
                            </div>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-500">#004</td>
                    <td class="font-bold">KaelDrift</td>
                    <td class="text-cyan-400 font-mono text-sm">Kael_Drift</td>
                    <td><div class="badge-glass badge-glass--secondary"><span class="badge-glass--text">Joueur</span></div></td>
                    <td><div class="badge-glass badge-glass--success"><span class="badge-glass--text">Actif</span></div></td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="./dashboard?page=profil-visual&user=Kael_Drift">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir le profil
                                </a>
                                <a href="#">
                                    <i data-lucide="pen" class="btn--icon"></i>
                                    Éditer
                                </a>
                                <a href="#">
                                    <i data-lucide="user-x" class="btn--icon"></i>
                                    Suspendre
                                </a>
                            </div>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-500">#005</td>
                    <td class="font-bold">Zork_92</td>
                    <td class="text-cyan-400 font-mono text-sm">Zork92</td>
                    <td><div class="badge-glass badge-glass--secondary"><span class="badge-glass--text">Joueur</span></div></td>
                    <td><div class="badge-glass badge-glass--warning"><span class="badge-glass--text">Suspendu</span></div></td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="./dashboard?page=profil-visual&user=Zork92">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir le profil
                                </a>
                                <a href="#">
                                    <i data-lucide="pen" class="btn--icon"></i>
                                    Éditer
                                </a>
                                <a href="#">
                                    <i data-lucide="shield-check" class="btn--icon"></i>
                                    Réactiver
                                </a>
                                <a href="#">
                                    <i data-lucide="ban" class="btn--icon"></i>
                                    Bannir
                                </a>
                            </div>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-500">#006</td>
                    <td class="font-bold">Zark_r</td>
                    <td class="text-cyan-400 font-mono text-sm">Zark_r</td>
                    <td><div class="badge-glass badge-glass--secondary"><span class="badge-glass--text">Joueur</span></div></td>
                    <td><div class="badge-glass badge-glass--success"><span class="badge-glass--text">Actif</span></div></td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="./dashboard?page=profil-visual&user=Zark_r">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir le profil
                                </a>
                                <a href="#">
                                    <i data-lucide="pen" class="btn--icon"></i>
                                    Éditer
                                </a>
                                <a href="#">
                                    <i data-lucide="user-x" class="btn--icon"></i>
                                    Suspendre
                                </a>
                            </div>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="text-xs text-gray-500">#007</td>
                    <td class="font-bold">SkyFen</td>
                    <td class="text-cyan-400 font-mono text-sm">SkyFen_MC</td>
                    <td><div class="badge-glass badge-glass--secondary"><span class="badge-glass--text">Joueur</span></div></td>
                    <td><div class="badge-glass badge-glass--danger"><span class="badge-glass--text">Banni</span></div></td>
                    <td>
                        <button class="dropdown">
                            <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                            <div class="dropdown-container dropdown-container--popup hidden">
                                <a href="./dashboard?page=profil-visual&user=SkyFen_MC">
                                    <i data-lucide="eye" class="btn--icon"></i>
                                    Voir le profil
                                </a>
                                <a href="#">
                                    <i data-lucide="shield-check" class="btn--icon"></i>
                                    Débannir
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

    function searchUsers() {
        const query = document.getElementById('user-search').value.toLowerCase();
        const rows = document.querySelectorAll('#users-table tbody tr');
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
        });
    }

    document.getElementById('user-search').addEventListener('keydown', e => {
        if (e.key === 'Enter') searchUsers();
    });
</script>

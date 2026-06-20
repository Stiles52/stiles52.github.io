<style>
    .group-card {
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(34, 211, 238, 0.2);
        border-top-width: 4px;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }
    .group-card:hover {
        border-color: rgba(34, 211, 238, 0.5);
        box-shadow: 0 0 20px rgba(34, 211, 238, 0.05);
    }
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.85);
        z-index: 100; display: flex; align-items: center; justify-content: center;
    }
    .modal-box {
        background: rgba(0,0,0,0.95);
        border: 1px solid rgba(34, 211, 238, 0.3);
        border-top: 4px solid #22d3ee;
        padding: 2rem; width: 520px; max-width: 95vw;
    }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">
        <div class="flex items-center justify-between pb-12">
            <h3 class="text-cyan-400">Gestion des groupes</h3>
            <button onclick="openModal()" class="origin-btn btn--full-graphic btn--success">
                Créer un groupe
                <i data-lucide="plus" class="btn--icon"></i>
            </button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">

            <!-- Administrateur -->
            <div class="group-card" style="border-top-color: #ef4444;">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <i data-lucide="crown" class="w-5 h-5 text-red-500"></i>
                        <h5 class="text-white">Administrateur</h5>
                    </div>
                    <button class="dropdown">
                        <i data-lucide="ellipsis-vertical" class="w-5 h-5 text-gray-400"></i>
                        <div class="dropdown-container dropdown-container--popup hidden">
                            <a href="#" onclick="openEditModal('Administrateur')">
                                <i data-lucide="pen" class="btn--icon"></i>
                                Modifier
                            </a>
                            <a href="#" class="text-red-400">
                                <i data-lucide="trash-2" class="btn--icon"></i>
                                Supprimer
                            </a>
                        </div>
                    </button>
                </div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="badge-glass badge-glass--danger"><span class="badge-glass--text">Administrateur</span></div>
                    <span class="text-xs text-gray-500">2 membres</span>
                </div>
                <div class="text-xs text-gray-500 uppercase tracking-widest mb-2">Permissions</div>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">manage-users</span>
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">manage-groups</span>
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">manage-tickets</span>
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">manage-permissions</span>
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">view-logs</span>
                </div>
            </div>

            <!-- Modérateur -->
            <div class="group-card" style="border-top-color: #a855f7;">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <i data-lucide="scale" class="w-5 h-5 text-purple-500"></i>
                        <h5 class="text-white">Modérateur</h5>
                    </div>
                    <button class="dropdown">
                        <i data-lucide="ellipsis-vertical" class="w-5 h-5 text-gray-400"></i>
                        <div class="dropdown-container dropdown-container--popup hidden">
                            <a href="#" onclick="openEditModal('Modérateur')">
                                <i data-lucide="pen" class="btn--icon"></i>
                                Modifier
                            </a>
                            <a href="#" class="text-red-400">
                                <i data-lucide="trash-2" class="btn--icon"></i>
                                Supprimer
                            </a>
                        </div>
                    </button>
                </div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="badge-glass badge-glass--warning"><span class="badge-glass--text">Modérateur</span></div>
                    <span class="text-xs text-gray-500">5 membres</span>
                </div>
                <div class="text-xs text-gray-500 uppercase tracking-widest mb-2">Permissions</div>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">manage-tickets</span>
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">view-users</span>
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">manage-candidatures</span>
                </div>
            </div>

            <!-- Haut-Staff -->
            <div class="group-card" style="border-top-color: #f97316;">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <i data-lucide="shield" class="w-5 h-5 text-orange-500"></i>
                        <h5 class="text-white">Haut-Staff</h5>
                    </div>
                    <button class="dropdown">
                        <i data-lucide="ellipsis-vertical" class="w-5 h-5 text-gray-400"></i>
                        <div class="dropdown-container dropdown-container--popup hidden">
                            <a href="#" onclick="openEditModal('Haut-Staff')">
                                <i data-lucide="pen" class="btn--icon"></i>
                                Modifier
                            </a>
                            <a href="#" class="text-red-400">
                                <i data-lucide="trash-2" class="btn--icon"></i>
                                Supprimer
                            </a>
                        </div>
                    </button>
                </div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="badge-glass badge-glass--warning"><span class="badge-glass--text">Haut-Staff</span></div>
                    <span class="text-xs text-gray-500">3 membres</span>
                </div>
                <div class="text-xs text-gray-500 uppercase tracking-widest mb-2">Permissions</div>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">manage-tickets</span>
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">manage-candidatures</span>
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">view-users</span>
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">view-logs</span>
                </div>
            </div>

            <!-- Scénariste -->
            <div class="group-card" style="border-top-color: #ec4899;">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <i data-lucide="scroll-text" class="w-5 h-5 text-pink-500"></i>
                        <h5 class="text-white">Scénariste</h5>
                    </div>
                    <button class="dropdown">
                        <i data-lucide="ellipsis-vertical" class="w-5 h-5 text-gray-400"></i>
                        <div class="dropdown-container dropdown-container--popup hidden">
                            <a href="#" onclick="openEditModal('Scénariste')">
                                <i data-lucide="pen" class="btn--icon"></i>
                                Modifier
                            </a>
                            <a href="#" class="text-red-400">
                                <i data-lucide="trash-2" class="btn--icon"></i>
                                Supprimer
                            </a>
                        </div>
                    </button>
                </div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="badge-glass badge-glass--primary"><span class="badge-glass--text">Scénariste</span></div>
                    <span class="text-xs text-gray-500">4 membres</span>
                </div>
                <div class="text-xs text-gray-500 uppercase tracking-widest mb-2">Permissions</div>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">manage-tickets</span>
                </div>
            </div>

            <!-- Développeur -->
            <div class="group-card" style="border-top-color: #22c55e;">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <i data-lucide="code" class="w-5 h-5 text-green-500"></i>
                        <h5 class="text-white">Développeur</h5>
                    </div>
                    <button class="dropdown">
                        <i data-lucide="ellipsis-vertical" class="w-5 h-5 text-gray-400"></i>
                        <div class="dropdown-container dropdown-container--popup hidden">
                            <a href="#" onclick="openEditModal('Développeur')">
                                <i data-lucide="pen" class="btn--icon"></i>
                                Modifier
                            </a>
                            <a href="#" class="text-red-400">
                                <i data-lucide="trash-2" class="btn--icon"></i>
                                Supprimer
                            </a>
                        </div>
                    </button>
                </div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="badge-glass badge-glass--success"><span class="badge-glass--text">Développeur</span></div>
                    <span class="text-xs text-gray-500">2 membres</span>
                </div>
                <div class="text-xs text-gray-500 uppercase tracking-widest mb-2">Permissions</div>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">view-logs</span>
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">manage-tickets</span>
                </div>
            </div>

            <!-- Joueur -->
            <div class="group-card" style="border-top-color: #4b5563;">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
                        <h5 class="text-white">Joueur</h5>
                    </div>
                    <button class="dropdown">
                        <i data-lucide="ellipsis-vertical" class="w-5 h-5 text-gray-400"></i>
                        <div class="dropdown-container dropdown-container--popup hidden">
                            <a href="#" onclick="openEditModal('Joueur')">
                                <i data-lucide="pen" class="btn--icon"></i>
                                Modifier
                            </a>
                            <a href="#" class="text-red-400">
                                <i data-lucide="trash-2" class="btn--icon"></i>
                                Supprimer
                            </a>
                        </div>
                    </button>
                </div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="badge-glass badge-glass--secondary"><span class="badge-glass--text">Joueur</span></div>
                    <span class="text-xs text-gray-500">142 membres</span>
                </div>
                <div class="text-xs text-gray-500 uppercase tracking-widest mb-2">Permissions</div>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">create-ticket</span>
                    <span class="text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300">view-own-tickets</span>
                </div>
            </div>

        </div>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<!-- Modal création de groupe -->
<div id="modal-create" class="modal-overlay hidden">
    <div class="modal-box">
        <h4 class="text-cyan-400 mb-6" id="modal-title">Créer un groupe</h4>
        <div class="w-full mb-6">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Nom du groupe</label>
            <input type="text" id="group-name" style="width: 100%;" placeholder="Ex : Recruteur">
        </div>
        <div class="w-full mb-6">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Couleur du badge</label>
            <div class="flex gap-3 flex-wrap mt-2">
                <button onclick="selectColor('#ef4444')" class="w-8 h-8 rounded" style="background:#ef4444;"></button>
                <button onclick="selectColor('#a855f7')" class="w-8 h-8 rounded" style="background:#a855f7;"></button>
                <button onclick="selectColor('#f97316')" class="w-8 h-8 rounded" style="background:#f97316;"></button>
                <button onclick="selectColor('#22d3ee')" class="w-8 h-8 rounded" style="background:#22d3ee;"></button>
                <button onclick="selectColor('#22c55e')" class="w-8 h-8 rounded" style="background:#22c55e;"></button>
                <button onclick="selectColor('#ec4899')" class="w-8 h-8 rounded" style="background:#ec4899;"></button>
                <button onclick="selectColor('#eab308')" class="w-8 h-8 rounded" style="background:#eab308;"></button>
                <button onclick="selectColor('#4b5563')" class="w-8 h-8 rounded" style="background:#4b5563;"></button>
            </div>
        </div>
        <div class="w-full mb-8">
            <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Permissions</label>
            <div class="flex flex-wrap gap-3 mt-2">
                <?php
                $all_permissions = ['create-ticket','view-own-tickets','manage-tickets','manage-candidatures','view-users','manage-users','manage-groups','manage-permissions','view-logs'];
                foreach($all_permissions as $perm) {
                    echo '<label class="flex items-center gap-2 text-xs text-gray-400 cursor-pointer">';
                    echo '<input type="checkbox" value="'.$perm.'" class="accent-cyan-400">';
                    echo $perm;
                    echo '</label>';
                }
                ?>
            </div>
        </div>
        <div class="flex gap-4">
            <button onclick="closeModal()" class="origin-btn btn--graphic btn--secondary flex-1 justify-center">Annuler</button>
            <button class="origin-btn btn--full-graphic btn--success flex-1 justify-center">
                <span>Enregistrer</span>
                <i data-lucide="save" class="btn--icon"></i>
            </button>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    function openModal() {
        document.getElementById('modal-title').textContent = 'Créer un groupe';
        document.getElementById('modal-create').classList.remove('hidden');
    }
    function openEditModal(name) {
        document.getElementById('modal-title').textContent = 'Modifier : ' + name;
        document.getElementById('group-name').value = name;
        document.getElementById('modal-create').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('modal-create').classList.add('hidden');
    }
    function selectColor(hex) {
        document.querySelectorAll('.modal-box button[onclick^="selectColor"]').forEach(b => b.style.outline = 'none');
        event.target.style.outline = '3px solid #22d3ee';
    }
    document.getElementById('modal-create').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>

<style>
    .character-card {
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(34, 211, 238, 0.15);
        border-left: 4px solid rgba(34, 211, 238, 0.4);
        padding: 1.25rem 1.5rem;
        display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        transition: border-color 0.2s, background 0.2s;
    }
    .character-card:hover { background: rgba(34, 211, 238, 0.03); border-left-color: #22d3ee; }
    .character-card--info { display: flex; align-items: center; gap: 1.25rem; flex: 1; flex-wrap: wrap; }
    .character-initial {
        width: 48px; height: 48px; flex-shrink: 0;
        background: rgba(34, 211, 238, 0.08);
        border: 1px solid rgba(34, 211, 238, 0.25);
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; color: #22d3ee; font-size: 1.2rem;
        font-family: 'JetBrains Mono', monospace;
    }
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.88);
        z-index: 200; display: flex; align-items: center; justify-content: center;
    }
    .modal-box {
        background: rgba(5,5,5,0.98);
        border: 1px solid rgba(34, 211, 238, 0.25);
        border-top: 3px solid #22d3ee;
        padding: 2rem; width: 560px; max-width: 95vw;
    }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">

        <div class="flex items-center justify-between pb-12">
            <h3 class="text-cyan-400">Gestion des personnages</h3>
            <button onclick="openModal()" class="origin-btn btn--full-graphic btn--success">
                Ajouter un personnage
                <i data-lucide="plus" class="btn--icon"></i>
            </button>
        </div>

        <!-- Barre de recherche -->
        <div class="page-toolbar" style="margin-bottom: 1.5rem;">
            <div class="page-toolbar--search">
                <input type="text" id="char-search" placeholder="Rechercher un personnage...">
                <button onclick="searchChars()" class="origin-btn btn--graphic btn--primary" style="flex-shrink:0;">
                    <i data-lucide="search" class="btn--icon"></i>
                </button>
            </div>
        </div>

        <!-- Liste -->
        <div id="characters-list" style="display: flex; flex-direction: column; gap: 0.75rem;">

            <div class="character-card" style="border-left-color: #f97316;">
                <div class="character-card--info">
                    <div class="character-initial">A</div>
                    <div>
                        <div class="text-base font-bold text-white mb-2">Alaric Vorn</div>
                        <div class="flex flex-wrap gap-2">
                            <div class="badge-glass badge-glass--warning"><span class="badge-glass--text">Médecin</span></div>
                            <div class="badge-glass badge-glass--primary"><span class="badge-glass--text">Confirmé</span></div>
                            <div class="badge-glass badge-glass--success"><span class="badge-glass--text">Actif</span></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs text-gray-600">Créé le 12/01/2025</span>
                    <button class="dropdown">
                        <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                        <div class="dropdown-container dropdown-container--popup hidden">
                            <a href="#"><i data-lucide="eye" class="btn--icon"></i>Voir la fiche</a>
                            <a href="#" onclick="openModal('Alaric Vorn', 'Médecin', 'Confirmé', 'Actif')"><i data-lucide="pen" class="btn--icon"></i>Modifier</a>
                            <a href="#" onclick="deleteChar(this)" class="text-red-400"><i data-lucide="trash-2" class="btn--icon"></i>Supprimer</a>
                        </div>
                    </button>
                </div>
            </div>

            <div class="character-card" style="border-left-color: #22d3ee;">
                <div class="character-card--info">
                    <div class="character-initial">S</div>
                    <div>
                        <div class="text-base font-bold text-white mb-2">Sera Lind</div>
                        <div class="flex flex-wrap gap-2">
                            <div class="badge-glass badge-glass--primary"><span class="badge-glass--text">Artisan</span></div>
                            <div class="badge-glass badge-glass--info"><span class="badge-glass--text">Spécialisé</span></div>
                            <div class="badge-glass badge-glass--success"><span class="badge-glass--text">Actif</span></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs text-gray-600">Créé le 03/03/2025</span>
                    <button class="dropdown">
                        <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                        <div class="dropdown-container dropdown-container--popup hidden">
                            <a href="#"><i data-lucide="eye" class="btn--icon"></i>Voir la fiche</a>
                            <a href="#" onclick="openModal('Sera Lind', 'Artisan', 'Spécialisé', 'Actif')"><i data-lucide="pen" class="btn--icon"></i>Modifier</a>
                            <a href="#" onclick="deleteChar(this)" class="text-red-400"><i data-lucide="trash-2" class="btn--icon"></i>Supprimer</a>
                        </div>
                    </button>
                </div>
            </div>

            <div class="character-card" style="border-left-color: #4b5563;">
                <div class="character-card--info">
                    <div class="character-initial" style="color: #6b7280; border-color: rgba(107,114,128,0.3);">V</div>
                    <div>
                        <div class="text-base font-bold text-white mb-2">Vael Noir</div>
                        <div class="flex flex-wrap gap-2">
                            <div class="badge-glass badge-glass--secondary"><span class="badge-glass--text">Agriculteur</span></div>
                            <div class="badge-glass badge-glass--secondary"><span class="badge-glass--text">Débutant</span></div>
                            <div class="badge-glass badge-glass--danger"><span class="badge-glass--text">Inactif</span></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs text-gray-600">Créé le 22/09/2025</span>
                    <button class="dropdown">
                        <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                        <div class="dropdown-container dropdown-container--popup hidden">
                            <a href="#"><i data-lucide="eye" class="btn--icon"></i>Voir la fiche</a>
                            <a href="#" onclick="openModal('Vael Noir', 'Agriculteur', 'Débutant', 'Inactif')"><i data-lucide="pen" class="btn--icon"></i>Modifier</a>
                            <a href="#" onclick="deleteChar(this)" class="text-red-400"><i data-lucide="trash-2" class="btn--icon"></i>Supprimer</a>
                        </div>
                    </button>
                </div>
            </div>

        </div>

        <div id="empty-state" class="hidden text-center py-24">
            <i data-lucide="theater" class="w-14 h-14 text-gray-700 mx-auto mb-5"></i>
            <p class="text-gray-600 text-xs uppercase tracking-widest m-0">Aucun personnage enregistré</p>
        </div>

    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<!-- Modal ajout / édition -->
<div id="modal-char" class="modal-overlay hidden">
    <div class="modal-box">
        <h4 class="text-cyan-400 mb-6" id="modal-title">Ajouter un personnage</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div style="grid-column: 1 / -1;">
                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Prénom & Nom du personnage</label>
                <input type="text" id="char-name" style="width: 100%;" placeholder="Ex : Alaric Vorn">
            </div>
            <div>
                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Métier</label>
                <select id="char-job" style="width: 100%;">
                    <option value="">— Sélectionnez —</option>
                    <option>Médecin</option>
                    <option>Artisan</option>
                    <option>Agriculteur</option>
                    <option>Marchand</option>
                    <option>Soldat</option>
                    <option>Autre</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Niveau de spécialisation</label>
                <select id="char-level" style="width: 100%;">
                    <option>Débutant</option>
                    <option>Confirmé</option>
                    <option>Spécialisé</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Statut</label>
                <select id="char-status" style="width: 100%;">
                    <option>Actif</option>
                    <option>Inactif</option>
                </select>
            </div>
            <div style="grid-column: 1 / -1;">
                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Description (optionnel)</label>
                <textarea id="char-desc" rows="3" style="width: 100%;" placeholder="Quelques mots sur ce personnage..."></textarea>
            </div>
        </div>
        <p id="modal-error" class="hidden text-xs text-red-400 mb-4 uppercase tracking-widest"></p>
        <div class="flex gap-3">
            <button onclick="closeModal()" class="origin-btn btn--graphic btn--secondary" style="flex: 1; justify-content: center;">Annuler</button>
            <button onclick="saveChar()" class="origin-btn btn--full-graphic btn--success" style="flex: 1; justify-content: center;">
                Enregistrer
                <i data-lucide="save" class="btn--icon"></i>
            </button>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    const jobColors = {
        'Médecin':     { border: '#f97316', badge: 'badge-glass--warning', initial: '#f97316' },
        'Artisan':     { border: '#22d3ee', badge: 'badge-glass--primary',  initial: '#22d3ee' },
        'Agriculteur': { border: '#22c55e', badge: 'badge-glass--success',  initial: '#22c55e' },
        'Marchand':    { border: '#eab308', badge: 'badge-glass--warning',  initial: '#eab308' },
        'Soldat':      { border: '#ef4444', badge: 'badge-glass--danger',   initial: '#ef4444' },
        'Autre':       { border: '#6b7280', badge: 'badge-glass--secondary',initial: '#6b7280' },
    };
    const levelBadge = { 'Débutant': 'badge-glass--secondary', 'Confirmé': 'badge-glass--primary', 'Spécialisé': 'badge-glass--info' };

    function openModal(name, job, level, status) {
        document.getElementById('modal-title').textContent = name ? 'Modifier : ' + name : 'Ajouter un personnage';
        document.getElementById('char-name').value   = name   || '';
        document.getElementById('char-desc').value   = '';
        document.getElementById('modal-error').classList.add('hidden');
        if (job)    document.getElementById('char-job').value    = job;
        if (level)  document.getElementById('char-level').value  = level;
        if (status) document.getElementById('char-status').value = status;
        document.getElementById('modal-char').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('modal-char').classList.add('hidden');
    }

    function saveChar() {
        const name   = document.getElementById('char-name').value.trim();
        const job    = document.getElementById('char-job').value;
        const level  = document.getElementById('char-level').value;
        const status = document.getElementById('char-status').value;
        const err    = document.getElementById('modal-error');

        if (!name || !job) {
            err.textContent = '✕ Le nom et le métier sont obligatoires.';
            err.classList.remove('hidden');
            return;
        }

        const colors     = jobColors[job] || jobColors['Autre'];
        const lvlBadge   = levelBadge[level] || 'badge-glass--secondary';
        const statBadge  = status === 'Actif' ? 'badge-glass--success' : 'badge-glass--danger';
        const initial    = name.charAt(0).toUpperCase();
        const today      = new Date().toLocaleDateString('fr-FR');

        const card = document.createElement('div');
        card.className = 'character-card';
        card.style.borderLeftColor = colors.border;
        card.innerHTML = `
            <div class="character-card--info">
                <div class="character-initial" style="color:${colors.initial}; border-color:${colors.initial}44;">${initial}</div>
                <div>
                    <div class="text-base font-bold text-white mb-2">${name}</div>
                    <div class="flex flex-wrap gap-2">
                        <div class="badge-glass ${colors.badge}"><span class="badge-glass--text">${job}</span></div>
                        <div class="badge-glass ${lvlBadge}"><span class="badge-glass--text">${level}</span></div>
                        <div class="badge-glass ${statBadge}"><span class="badge-glass--text">${status}</span></div>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-600">Créé le ${today}</span>
                <button class="dropdown">
                    <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                    <div class="dropdown-container dropdown-container--popup hidden">
                        <a href="#"><i data-lucide="eye" class="btn--icon"></i>Voir la fiche</a>
                        <a href="#" onclick="openModal('${name}', '${job}', '${level}', '${status}')"><i data-lucide="pen" class="btn--icon"></i>Modifier</a>
                        <a href="#" onclick="deleteChar(this)" class="text-red-400"><i data-lucide="trash-2" class="btn--icon"></i>Supprimer</a>
                    </div>
                </button>
            </div>`;
        document.getElementById('characters-list').appendChild(card);
        lucide.createIcons();
        closeModal();
        checkEmpty();
    }

    function deleteChar(el) {
        el.closest('.character-card').remove();
        checkEmpty();
    }

    function checkEmpty() {
        const empty = document.getElementById('characters-list').children.length === 0;
        document.getElementById('empty-state').classList.toggle('hidden', !empty);
    }

    function searchChars() {
        const query = document.getElementById('char-search').value.toLowerCase();
        document.querySelectorAll('#characters-list .character-card').forEach(card => {
            card.style.display = card.textContent.toLowerCase().includes(query) ? '' : 'none';
        });
    }

    document.getElementById('char-search').addEventListener('keydown', e => {
        if (e.key === 'Enter') searchChars();
    });
    document.getElementById('modal-char').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>

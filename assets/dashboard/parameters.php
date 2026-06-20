<style>
    .param-field {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }
    .param-field--label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #6b7280;
    }
    .param-field--value {
        font-size: 0.9rem;
        color: #e5e7eb;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(34, 211, 238, 0.08);
    }
    .param-avatar {
        width: 72px; height: 72px;
        background: rgba(34, 211, 238, 0.1);
        border: 2px solid rgba(34, 211, 238, 0.3);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.75rem; font-weight: bold; color: #22d3ee;
        flex-shrink: 0;
        font-family: 'JetBrains Mono', monospace;
    }
    .section-panel {
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(34, 211, 238, 0.15);
        padding: 1.75rem;
        margin-bottom: 1.25rem;
    }
    .section-panel--title {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: #22d3ee;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(34, 211, 238, 0.12);
        display: flex; align-items: center; gap: 0.5rem;
    }
    .link-card {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1.1rem 1.25rem;
        border: 1px solid rgba(255,255,255,0.07);
        background: rgba(255,255,255,0.02);
        transition: background 0.2s;
        gap: 1rem;
    }
    .link-card:not(:last-child) { margin-bottom: 0.75rem; }
    .link-card--info { display: flex; align-items: center; gap: 1rem; }
    .link-card--icon {
        width: 40px; height: 40px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .link-card--texts { display: flex; flex-direction: column; gap: 0.2rem; }
    .link-card--name { font-size: 0.85rem; color: #e5e7eb; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; }
    .link-card--status { font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; }
    .link-card--status.linked { color: #22c55e; }
    .link-card--status.unlinked { color: #6b7280; }

    .character-card {
        border: 1px solid rgba(34, 211, 238, 0.12);
        background: rgba(255,255,255,0.02);
        padding: 1.1rem 1.25rem;
        display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        transition: background 0.2s;
    }
    .character-card:not(:last-child) { margin-bottom: 0.75rem; }
    .character-card:hover { background: rgba(34, 211, 238, 0.03); }
    .character-card--info { display: flex; align-items: center; gap: 1rem; flex: 1; flex-wrap: wrap; }
    .character-initial {
        width: 40px; height: 40px; flex-shrink: 0;
        background: rgba(34, 211, 238, 0.08);
        border: 1px solid rgba(34, 211, 238, 0.2);
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; color: #22d3ee; font-size: 1rem;
    }

    .params-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }
    @media (max-width: 1100px) {
        .params-grid { grid-template-columns: 1fr; }
    }

    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.88);
        z-index: 200; display: flex; align-items: center; justify-content: center;
    }
    .modal-box {
        background: rgba(5,5,5,0.98);
        border: 1px solid rgba(34, 211, 238, 0.25);
        border-top: 3px solid #22d3ee;
        padding: 2rem; width: 520px; max-width: 95vw;
    }

    .params-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }
    .modal-fields-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }

    @media (max-width: 768px) {
        .params-grid-2 { grid-template-columns: 1fr; }
        .modal-fields-grid { grid-template-columns: 1fr; }
        .section-panel { padding: 1.25rem; }
        .param-avatar { width: 56px; height: 56px; font-size: 1.4rem; }
        .link-card { flex-wrap: wrap; }
        .link-card--info { min-width: 0; flex: 1; }
        .modal-box { padding: 1.25rem; }
    }
    @media (max-width: 480px) {
        .section-panel { padding: 1rem; }
        .link-card button { width: 100%; justify-content: center; }
    }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">

        <!-- En-tête -->
        <div class="flex items-center justify-between pb-10" style="flex-wrap: wrap; gap: 0.75rem;">
            <h3 class="text-cyan-400">Paramètres</h3>
            <div id="btn-edit-group" class="flex gap-3">
                <button onclick="enterEdit()" class="origin-btn btn--full-graphic btn--primary">
                    Modifier
                    <i data-lucide="pen" class="btn--icon"></i>
                </button>
            </div>
            <div id="btn-save-group" class="flex gap-3 hidden">
                <button onclick="exitEdit()" class="origin-btn btn--graphic btn--secondary">Annuler</button>
                <button onclick="saveChanges()" class="origin-btn btn--full-graphic btn--success">
                    Enregistrer
                    <i data-lucide="save" class="btn--icon"></i>
                </button>
            </div>
        </div>

        <!-- ══════════════════════════════ VUE LECTURE -->
        <div id="view-display">

            <!-- Bandeau identité -->
            <div class="section-panel" style="margin-bottom: 1.25rem;">
                <div class="section-panel--title"><i data-lucide="user" class="w-3 h-3"></i> Identité</div>
                <div class="flex items-center gap-6 mb-6">
                    <div class="param-avatar" id="disp-avatar">M</div>
                    <div>
                        <div class="flex items-center gap-3 mb-2 flex-wrap">
                            <h4 class="text-white" id="disp-pseudo">Mathis150</h4>
                            <div class="badge-glass badge-glass--danger"><span class="badge-glass--text">Administrateur</span></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                            <span class="text-xs text-green-500 uppercase tracking-widest">Connecté</span>
                        </div>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.25rem;">
                    <div class="param-field">
                        <span class="param-field--label">Pseudo (site)</span>
                        <span class="param-field--value" id="disp-username">Mathis150</span>
                    </div>
                    <div class="param-field">
                        <span class="param-field--label">Pseudo Minecraft</span>
                        <span class="param-field--value text-cyan-400 font-mono" id="disp-mc">mathis150</span>
                    </div>
                    <div class="param-field">
                        <span class="param-field--label">Membre depuis</span>
                        <span class="param-field--value text-gray-400">01 janvier 2025</span>
                    </div>
                    <div class="param-field">
                        <span class="param-field--label">Adresse e-mail</span>
                        <span class="param-field--value" id="disp-email">mathis.150.online@gmail.com</span>
                    </div>
                </div>
            </div>

            <!-- Grille 2 colonnes -->
            <div class="params-grid">

                <!-- Colonne gauche -->
                <div>
                    <!-- Liaisons de compte -->
                    <div class="section-panel">
                        <div class="section-panel--title"><i data-lucide="link" class="w-3 h-3"></i> Liaisons de compte</div>

                        <!-- Discord -->
                        <div class="link-card">
                            <div class="link-card--info">
                                <div class="link-card--icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03z" fill="#5865F2"/>
                                    </svg>
                                </div>
                                <div class="link-card--texts">
                                    <span class="link-card--name">Discord</span>
                                    <span class="link-card--status linked" id="discord-status">● Lié — Mathis150#0001</span>
                                </div>
                            </div>
                            <button onclick="unlinkAccount('discord')" class="origin-btn btn--graphic btn--danger text-xs" style="padding: 0.5rem 1rem;">
                                Délier
                                <i data-lucide="unlink" class="btn--icon"></i>
                            </button>
                        </div>

                        <!-- Microsoft -->
                        <div class="link-card">
                            <div class="link-card--info">
                                <div class="link-card--icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="1" y="1" width="10.5" height="10.5" fill="#F25022"/>
                                        <rect x="12.5" y="1" width="10.5" height="10.5" fill="#7FBA00"/>
                                        <rect x="1" y="12.5" width="10.5" height="10.5" fill="#00A4EF"/>
                                        <rect x="12.5" y="12.5" width="10.5" height="10.5" fill="#FFB900"/>
                                    </svg>
                                </div>
                                <div class="link-card--texts">
                                    <span class="link-card--name">Microsoft</span>
                                    <span class="link-card--status unlinked" id="microsoft-status">○ Non lié</span>
                                </div>
                            </div>
                            <button onclick="linkAccount('microsoft')" class="origin-btn btn--graphic btn--primary text-xs" style="padding: 0.5rem 1rem;">
                                Lier
                                <i data-lucide="link" class="btn--icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Sécurité -->
                    <div class="section-panel">
                        <div class="section-panel--title"><i data-lucide="lock" class="w-3 h-3"></i> Sécurité</div>
                        <div class="params-grid-2">
                            <div class="param-field">
                                <span class="param-field--label">Mot de passe</span>
                                <span class="param-field--value text-gray-500">••••••••••••</span>
                            </div>
                            <div class="param-field">
                                <span class="param-field--label">Dernière connexion</span>
                                <span class="param-field--value text-gray-400">17/06/2026 à 14:32</span>
                            </div>
                        </div>
                    </div>

                    <!-- Préférences -->
                    <div class="section-panel">
                        <div class="section-panel--title"><i data-lucide="sliders-horizontal" class="w-3 h-3"></i> Préférences</div>
                        <div class="params-grid-2">
                            <div class="param-field">
                                <span class="param-field--label">Notifications par e-mail</span>
                                <span class="param-field--value" id="disp-notif">Activées</span>
                            </div>
                            <div class="param-field">
                                <span class="param-field--label">Langue</span>
                                <span class="param-field--value" id="disp-lang">Français</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne droite — Personnages -->
                <div>
                    <div class="section-panel" style="height: calc(100% - 1.25rem);">
                        <div class="section-panel--title" style="justify-content: space-between;">
                            <span class="flex items-center gap-2">
                                <i data-lucide="users" class="w-3 h-3"></i> Personnages RP
                            </span>
                            <button onclick="openCharModal()" class="origin-btn btn--graphic btn--primary" style="padding: 0.3rem 0.75rem; font-size: 10px; gap: 0.4rem; border-width: 1px;">
                                <i data-lucide="plus" class="w-3 h-3"></i> Ajouter
                            </button>
                        </div>

                        <div id="characters-list">
                            <div class="character-card">
                                <div class="character-card--info">
                                    <div class="character-initial">A</div>
                                    <div>
                                        <div class="text-sm font-bold text-white mb-1">Alaric Vorn</div>
                                        <div class="flex flex-wrap gap-2">
                                            <div class="badge-glass badge-glass--warning"><span class="badge-glass--text">Médecin — Confirmé</span></div>
                                            <div class="badge-glass badge-glass--success"><span class="badge-glass--text">Actif</span></div>
                                        </div>
                                    </div>
                                </div>
                                <button class="dropdown">
                                    <i data-lucide="ellipsis-vertical" class="w-5 h-5 text-gray-400"></i>
                                    <div class="dropdown-container dropdown-container--popup hidden">
                                        <a href="#"><i data-lucide="eye" class="btn--icon"></i>Voir la fiche</a>
                                        <a href="#" onclick="openCharModal('Alaric Vorn')"><i data-lucide="pen" class="btn--icon"></i>Modifier</a>
                                        <a href="#" class="text-red-400"><i data-lucide="trash-2" class="btn--icon"></i>Supprimer</a>
                                    </div>
                                </button>
                            </div>

                            <div class="character-card">
                                <div class="character-card--info">
                                    <div class="character-initial">S</div>
                                    <div>
                                        <div class="text-sm font-bold text-white mb-1">Sera Lind</div>
                                        <div class="flex flex-wrap gap-2">
                                            <div class="badge-glass badge-glass--primary"><span class="badge-glass--text">Artisan — Spécialisé</span></div>
                                            <div class="badge-glass badge-glass--success"><span class="badge-glass--text">Actif</span></div>
                                        </div>
                                    </div>
                                </div>
                                <button class="dropdown">
                                    <i data-lucide="ellipsis-vertical" class="w-5 h-5 text-gray-400"></i>
                                    <div class="dropdown-container dropdown-container--popup hidden">
                                        <a href="#"><i data-lucide="eye" class="btn--icon"></i>Voir la fiche</a>
                                        <a href="#" onclick="openCharModal('Sera Lind')"><i data-lucide="pen" class="btn--icon"></i>Modifier</a>
                                        <a href="#" class="text-red-400"><i data-lucide="trash-2" class="btn--icon"></i>Supprimer</a>
                                    </div>
                                </button>
                            </div>

                            <div class="character-card">
                                <div class="character-card--info">
                                    <div class="character-initial">V</div>
                                    <div>
                                        <div class="text-sm font-bold text-white mb-1">Vael Noir</div>
                                        <div class="flex flex-wrap gap-2">
                                            <div class="badge-glass badge-glass--secondary"><span class="badge-glass--text">Agriculteur</span></div>
                                            <div class="badge-glass badge-glass--danger"><span class="badge-glass--text">Inactif</span></div>
                                        </div>
                                    </div>
                                </div>
                                <button class="dropdown">
                                    <i data-lucide="ellipsis-vertical" class="w-5 h-5 text-gray-400"></i>
                                    <div class="dropdown-container dropdown-container--popup hidden">
                                        <a href="#"><i data-lucide="eye" class="btn--icon"></i>Voir la fiche</a>
                                        <a href="#" onclick="openCharModal('Vael Noir')"><i data-lucide="pen" class="btn--icon"></i>Modifier</a>
                                        <a href="#" class="text-red-400"><i data-lucide="trash-2" class="btn--icon"></i>Supprimer</a>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <div id="no-characters" class="hidden text-center py-12">
                            <i data-lucide="user-x" class="w-10 h-10 text-gray-700 mx-auto mb-3"></i>
                            <p class="text-gray-600 text-xs uppercase tracking-widest m-0">Aucun personnage enregistré</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ══════════════════════════════ VUE ÉDITION -->
        <div id="view-edit" class="hidden">

            <!-- Identité -->
            <div class="section-panel">
                <div class="section-panel--title"><i data-lucide="user" class="w-3 h-3"></i> Identité</div>
                <div class="flex items-center gap-6 mb-6">
                    <div class="param-avatar" id="edit-avatar">M</div>
                    <p class="text-xs text-gray-500 m-0">L'avatar est généré automatiquement depuis l'initiale du pseudo.</p>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.25rem;">
                    <div>
                        <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Pseudo (site)</label>
                        <input type="text" id="edit-username" style="width: 100%;" value="Mathis150">
                    </div>
                    <div>
                        <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Pseudo Minecraft</label>
                        <input type="text" id="edit-mc" style="width: 100%;" value="mathis150">
                        <cite>Doit correspondre exactement à votre nom en jeu.</cite>
                    </div>
                    <div>
                        <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Adresse e-mail</label>
                        <input type="email" id="edit-email" style="width: 100%;" value="mathis.150.online@gmail.com">
                    </div>
                </div>
            </div>

            <!-- Sécurité -->
            <div class="section-panel">
                <div class="section-panel--title"><i data-lucide="lock" class="w-3 h-3"></i> Changer le mot de passe</div>
                <cite>Laissez ces champs vides si vous ne souhaitez pas modifier votre mot de passe.</cite>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.25rem; margin-top: 1.25rem;">
                    <div>
                        <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Mot de passe actuel</label>
                        <input type="password" id="edit-pwd-current" style="width: 100%;" placeholder="••••••••••••">
                    </div>
                    <div>
                        <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Nouveau mot de passe</label>
                        <input type="password" id="edit-pwd-new" style="width: 100%;" placeholder="••••••••••••">
                    </div>
                    <div>
                        <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Confirmer le nouveau mot de passe</label>
                        <input type="password" id="edit-pwd-confirm" style="width: 100%;" placeholder="••••••••••••">
                    </div>
                </div>
            </div>

            <!-- Préférences -->
            <div class="section-panel">
                <div class="section-panel--title"><i data-lucide="sliders-horizontal" class="w-3 h-3"></i> Préférences</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.25rem;">
                    <div>
                        <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Notifications par e-mail</label>
                        <select id="edit-notif" style="width: 100%;">
                            <option value="1" selected>Activées</option>
                            <option value="0">Désactivées</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Langue</label>
                        <select id="edit-lang" style="width: 100%;">
                            <option value="fr" selected>Français</option>
                            <option value="en">English</option>
                        </select>
                    </div>
                </div>
            </div>

            <p id="save-status" class="hidden text-center text-xs mt-4 uppercase tracking-widest"></p>
        </div>

    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<!-- Modal personnage -->
<div id="modal-char" class="modal-overlay hidden">
    <div class="modal-box">
        <h4 class="text-cyan-400 mb-6" id="modal-char-title">Ajouter un personnage</h4>
        <div class="modal-fields-grid">
            <div style="grid-column: 1 / -1;">
                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Prénom & Nom du personnage</label>
                <input type="text" id="char-name" style="width: 100%;" placeholder="Ex : Alaric Vorn">
            </div>
            <div>
                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Métier</label>
                <select id="char-job" style="width: 100%;">
                    <option>— Sélectionnez —</option>
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
        </div>
        <p id="modal-char-error" class="hidden text-xs text-red-400 mb-4 uppercase tracking-widest"></p>
        <div class="flex gap-3">
            <button onclick="closeCharModal()" class="origin-btn btn--graphic btn--secondary" style="flex: 1; justify-content: center;">Annuler</button>
            <button onclick="saveChar()" class="origin-btn btn--full-graphic btn--success" style="flex: 1; justify-content: center;">
                <span>Enregistrer</span>
                <i data-lucide="save" class="btn--icon"></i>
            </button>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    /* ──── Bascule lecture ↔ édition ──── */
    function enterEdit() {
        document.getElementById('view-display').classList.add('hidden');
        document.getElementById('view-edit').classList.remove('hidden');
        document.getElementById('btn-edit-group').classList.add('hidden');
        document.getElementById('btn-save-group').classList.remove('hidden');
    }
    function exitEdit() {
        document.getElementById('view-edit').classList.add('hidden');
        document.getElementById('view-display').classList.remove('hidden');
        document.getElementById('btn-save-group').classList.add('hidden');
        document.getElementById('btn-edit-group').classList.remove('hidden');
        document.getElementById('save-status').classList.add('hidden');
    }
    function saveChanges() {
        const pwdNew     = document.getElementById('edit-pwd-new').value;
        const pwdConfirm = document.getElementById('edit-pwd-confirm').value;
        const status     = document.getElementById('save-status');

        if (pwdNew && pwdNew !== pwdConfirm) {
            status.textContent = '✕ Les mots de passe ne correspondent pas.';
            status.className = 'text-center text-xs mt-4 uppercase tracking-widest text-red-400';
            status.classList.remove('hidden');
            return;
        }
        const username = document.getElementById('edit-username').value.trim();
        const mc       = document.getElementById('edit-mc').value.trim();
        const email    = document.getElementById('edit-email').value.trim();
        const notifEl  = document.getElementById('edit-notif');
        const langEl   = document.getElementById('edit-lang');

        document.getElementById('disp-pseudo').textContent   = username;
        document.getElementById('disp-username').textContent = username;
        document.getElementById('disp-mc').textContent       = mc;
        document.getElementById('disp-email').textContent    = email;
        document.getElementById('disp-notif').textContent    = notifEl.options[notifEl.selectedIndex].text;
        document.getElementById('disp-lang').textContent     = langEl.options[langEl.selectedIndex].text;

        const initial = username.charAt(0).toUpperCase();
        document.getElementById('disp-avatar').textContent = initial;
        document.getElementById('edit-avatar').textContent = initial;

        status.textContent = '✓ Modifications enregistrées.';
        status.className = 'text-center text-xs mt-4 uppercase tracking-widest text-green-400';
        status.classList.remove('hidden');
        setTimeout(exitEdit, 1200);
    }

    /* ──── Liaisons de compte ──── */
    function linkAccount(provider) {
        const btn = event.target.closest('button');
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-pulse">Connexion...</span>';
        setTimeout(() => {
            document.getElementById(provider + '-status').textContent = '● Lié — compte@example.com';
            document.getElementById(provider + '-status').className = 'link-card--status linked';
            btn.textContent = 'Délier';
            btn.className = btn.className.replace('btn--primary', 'btn--danger');
            btn.setAttribute('onclick', "unlinkAccount('" + provider + "')");
            btn.disabled = false;
            lucide.createIcons();
        }, 1200);
    }
    function unlinkAccount(provider) {
        if (!confirm('Délier ce compte ' + provider + ' ?')) return;
        document.getElementById(provider + '-status').textContent = '○ Non lié';
        document.getElementById(provider + '-status').className = 'link-card--status unlinked';
        const btn = event.target.closest('button');
        btn.textContent = 'Lier';
        btn.className = btn.className.replace('btn--danger', 'btn--primary');
        btn.setAttribute('onclick', "linkAccount('" + provider + "')");
        lucide.createIcons();
    }

    /* ──── Personnages ──── */
    function openCharModal(name) {
        document.getElementById('modal-char-title').textContent = name ? 'Modifier : ' + name : 'Ajouter un personnage';
        document.getElementById('char-name').value = name || '';
        document.getElementById('modal-char-error').classList.add('hidden');
        document.getElementById('modal-char').classList.remove('hidden');
    }
    function closeCharModal() {
        document.getElementById('modal-char').classList.add('hidden');
    }
    function saveChar() {
        const name   = document.getElementById('char-name').value.trim();
        const job    = document.getElementById('char-job').value;
        const level  = document.getElementById('char-level').value;
        const status = document.getElementById('char-status').value;
        const err    = document.getElementById('modal-char-error');

        if (!name || job === '— Sélectionnez —') {
            err.textContent = '✕ Le nom et le métier sont obligatoires.';
            err.classList.remove('hidden');
            return;
        }

        const initial = name.charAt(0).toUpperCase();
        const statusClass = status === 'Actif' ? 'badge-glass--success' : 'badge-glass--danger';
        const card = document.createElement('div');
        card.className = 'character-card';
        card.innerHTML = `
            <div class="character-card--info">
                <div class="character-initial">${initial}</div>
                <div>
                    <div class="text-sm font-bold text-white mb-1">${name}</div>
                    <div class="flex flex-wrap gap-2">
                        <div class="badge-glass badge-glass--primary"><span class="badge-glass--text">${job} — ${level}</span></div>
                        <div class="badge-glass ${statusClass}"><span class="badge-glass--text">${status}</span></div>
                    </div>
                </div>
            </div>
            <button class="dropdown">
                <i data-lucide="ellipsis-vertical" class="w-5 h-5 text-gray-400"></i>
                <div class="dropdown-container dropdown-container--popup hidden">
                    <a href="#"><i data-lucide="eye" class="btn--icon"></i>Voir la fiche</a>
                    <a href="#" onclick="openCharModal('${name}')"><i data-lucide="pen" class="btn--icon"></i>Modifier</a>
                    <a href="#" onclick="this.closest('.character-card').remove()" class="text-red-400"><i data-lucide="trash-2" class="btn--icon"></i>Supprimer</a>
                </div>
            </button>`;
        document.getElementById('characters-list').appendChild(card);
        lucide.createIcons();
        closeCharModal();
    }
    document.getElementById('modal-char').addEventListener('click', function(e) {
        if (e.target === this) closeCharModal();
    });
</script>

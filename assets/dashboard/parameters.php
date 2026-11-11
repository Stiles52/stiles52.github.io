<?php
$me            = $auth->user();
$profile       = $users->profile($me['id']);
$account       = $db->fetchOne('SELECT discord_id, discord_username, email_notifications FROM users WHERE id = ?', [$me['id']]);
$characterList = $characters->listForUser($me['id']);
$pre           = $characters->prerequisites($me['id']);
$canAddCharacter = $pre['count'] < $pre['max'];
$csrf          = Auth::generateCsrfToken();
$flash         = $_SESSION['flash'] ?? null;
$old           = $_SESSION['settings_form'] ?? null;   // saisie conservée après une erreur
unset($_SESSION['flash'], $_SESSION['settings_form']);

$editing = $old !== null;
$val = fn(string $k) => $old !== null
    ? (string) ($old[$k] ?? '')
    : match ($k) {
        'email_notifications' => $account['email_notifications'] ? '1' : '0',
        default               => (string) ($profile[$k] ?? ''),
    };

$e       = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
$initial = mb_strtoupper(mb_substr($profile['pseudo'], 0, 1));
$months  = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
$created = strtotime($profile['created_at']);
$memberSince = date('d', $created) . ' ' . $months[(int) date('n', $created) - 1] . ' ' . date('Y', $created);
?>
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
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 md:pb-40 fade-in-view">

        <!-- En-tête -->
        <div class="flex items-center justify-between pb-10" style="flex-wrap: wrap; gap: 0.75rem;">
            <h3 class="text-cyan-400">Paramètres</h3>
            <div id="btn-edit-group" class="flex gap-3 <?= $editing ? 'hidden' : '' ?>">
                <button type="button" onclick="enterEdit()" class="origin-btn btn--full-graphic btn--primary">
                    Modifier
                    <i data-lucide="pen" class="btn--icon"></i>
                </button>
            </div>
            <div id="btn-save-group" class="flex gap-3 <?= $editing ? '' : 'hidden' ?>">
                <button type="button" onclick="exitEdit()" class="origin-btn btn--graphic btn--secondary">Annuler</button>
                <button type="submit" form="settings-form" class="origin-btn btn--full-graphic btn--success">
                    Enregistrer
                    <i data-lucide="save" class="btn--icon"></i>
                </button>
            </div>
        </div>

        <?php if ($flash): ?>
        <p class="text-center text-xs mb-6 uppercase tracking-widest <?= $flash['type'] === 'success' ? 'text-green-400' : 'text-red-400' ?>"><?= $flash['type'] === 'success' ? '✓' : '✕' ?> <?= $e($flash['message']) ?></p>
        <?php endif; ?>

        <!-- ══════════════════════════════ VUE LECTURE -->
        <div id="view-display" class="<?= $editing ? 'hidden' : '' ?>">

            <!-- Bandeau identité -->
            <div class="section-panel" style="margin-bottom: 1.25rem;">
                <div class="section-panel--title"><i data-lucide="user" class="w-3 h-3"></i> Identité</div>
                <div class="flex items-center gap-6 mb-6">
                    <div class="param-avatar" id="disp-avatar"><?= $e($initial) ?></div>
                    <div>
                        <div class="flex items-center gap-3 mb-2 flex-wrap">
                            <h4 class="text-white" id="disp-pseudo"><?= $e($profile['pseudo']) ?></h4>
                            <?php if ($profile['group_name']): ?>
                            <div class="badge-glass badge-glass--<?= $e($profile['group_badge']) ?>"><span class="badge-glass--text"><?= $e($profile['group_name']) ?></span></div>
                            <?php endif; ?>
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
                        <span class="param-field--value" id="disp-username"><?= $e($profile['pseudo']) ?></span>
                    </div>
                    <div class="param-field">
                        <span class="param-field--label">Pseudo Minecraft</span>
                        <span class="param-field--value text-cyan-400 font-mono" id="disp-mc"><?= $profile['minecraft_pseudo'] ? $e($profile['minecraft_pseudo']) : '<span class="text-gray-600">—</span>' ?></span>
                    </div>
                    <div class="param-field">
                        <span class="param-field--label">Membre depuis</span>
                        <span class="param-field--value text-gray-400"><?= $e($memberSince) ?></span>
                    </div>
                    <div class="param-field">
                        <span class="param-field--label">Adresse e-mail</span>
                        <span class="param-field--value" id="disp-email"><?= $e($profile['email']) ?></span>
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
                                    <?php if ($account['discord_id']): ?>
                                    <span class="link-card--status linked" id="discord-status">● Lié — <?= $e($account['discord_username']) ?></span>
                                    <?php else: ?>
                                    <span class="link-card--status unlinked" id="discord-status">○ Non lié<?= Discord::isConfigured() ? '' : ' — liaison pas encore configurée sur le site' ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($account['discord_id']): ?>
                            <form method="POST" action="assets/handlers/discord-link" onsubmit="return confirm('Délier votre compte Discord ? Il est nécessaire pour déposer une candidature de personnage.')">
                                <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
                                <input type="hidden" name="from" value="parameters">
                                <button type="submit" class="origin-btn btn--graphic btn--danger text-xs" style="padding: 0.5rem 1rem;">
                                    Délier
                                    <i data-lucide="unlink" class="btn--icon"></i>
                                </button>
                            </form>
                            <?php elseif (Discord::isConfigured()): ?>
                            <a href="assets/handlers/discord-link?from=parameters" class="origin-btn btn--graphic btn--primary text-xs" style="padding: 0.5rem 1rem;">
                                Lier
                                <i data-lucide="link" class="btn--icon"></i>
                            </a>
                            <?php else: ?>
                            <button type="button" disabled class="origin-btn btn--graphic btn--secondary text-xs" style="padding: 0.5rem 1rem; cursor: not-allowed; opacity: .5;">
                                Lier
                                <i data-lucide="link" class="btn--icon"></i>
                            </button>
                            <?php endif; ?>
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
                                    <span class="link-card--status unlinked" id="microsoft-status">○ Bientôt disponible</span>
                                </div>
                            </div>
                            <button type="button" disabled class="origin-btn btn--graphic btn--secondary text-xs" style="padding: 0.5rem 1rem; cursor: not-allowed; opacity: .5;">
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
                                <span class="param-field--value text-gray-400"><?= $profile['last_login_at'] ? date('d/m/Y à H:i', strtotime($profile['last_login_at'])) : '—' ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Préférences -->
                    <div class="section-panel">
                        <div class="section-panel--title"><i data-lucide="sliders-horizontal" class="w-3 h-3"></i> Préférences</div>
                        <div class="params-grid-2">
                            <div class="param-field">
                                <span class="param-field--label">Notifications par e-mail</span>
                                <span class="param-field--value" id="disp-notif"><?= $account['email_notifications'] ? 'Activées' : 'Désactivées' ?></span>
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
                            <?php if ($canAddCharacter): ?>
                            <a href="./dashboard?page=personnage-form" class="origin-btn btn--graphic btn--primary" style="padding: 0.3rem 0.75rem; font-size: 10px; gap: 0.4rem; border-width: 1px;">
                                <i data-lucide="plus" class="w-3 h-3"></i> Ajouter
                            </a>
                            <?php endif; ?>
                        </div>

                        <div id="characters-list">
                            <?php foreach ($characterList as $c):
                                $job       = CharacterManager::JOBS[$c['job']] ?? CharacterManager::JOBS['Autre'];
                                $validated = $c['sheet'] !== null;
                                [$stBadge, $stLabel] = !$validated
                                    ? ($c['cand_status'] === 'refused' ? ['danger', 'Refusée'] : ['warning', 'En validation'])
                                    : ($c['active'] ? ['success', 'Actif'] : ['danger', 'Inactif']);
                            ?>
                            <div class="character-card">
                                <div class="character-card--info">
                                    <div class="character-initial" style="color: <?= $e($job['color']) ?>; border-color: <?= $e($job['color']) ?>44;"><?= $e(mb_strtoupper(mb_substr($c['name'], 0, 1))) ?></div>
                                    <div>
                                        <a href="./dashboard?page=personnage&id=<?= (int) $c['id'] ?>" class="text-sm font-bold text-white mb-1 block"><?= $e($c['name']) ?></a>
                                        <div class="flex flex-wrap gap-2">
                                            <div class="badge-glass badge-glass--<?= $job['badge'] ?>"><span class="badge-glass--text"><?= $e($c['job']) ?> — <?= CharacterManager::LEVELS[$c['level']]['label'] ?></span></div>
                                            <div class="badge-glass badge-glass--<?= $stBadge ?>"><span class="badge-glass--text"><?= $stLabel ?></span></div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="dropdown">
                                    <i data-lucide="ellipsis-vertical" class="w-5 h-5 text-gray-400"></i>
                                    <div class="dropdown-container dropdown-container--popup hidden">
                                        <a href="./dashboard?page=personnage&id=<?= (int) $c['id'] ?>"><i data-lucide="eye" class="btn--icon"></i>Voir la fiche</a>
                                        <a href="./dashboard?page=personnage-form&id=<?= (int) $c['id'] ?>"><i data-lucide="pen" class="btn--icon"></i>Modifier</a>
                                        <a href="#" onclick="deleteChar(<?= (int) $c['id'] ?>, <?= $e(json_encode($c['name'])) ?>); return false;" class="text-red-400"><i data-lucide="trash-2" class="btn--icon"></i>Supprimer</a>
                                    </div>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div id="no-characters" class="<?= $characterList ? 'hidden' : '' ?> text-center py-12">
                            <i data-lucide="user-x" class="w-10 h-10 text-gray-700 mx-auto mb-3"></i>
                            <p class="text-gray-600 text-xs uppercase tracking-widest m-0">Aucun personnage enregistré</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ══════════════════════════════ VUE ÉDITION -->
        <form id="settings-form" method="POST" action="assets/handlers/settings-action" class="<?= $editing ? '' : 'hidden' ?>">
            <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">

            <!-- Identité -->
            <div class="section-panel">
                <div class="section-panel--title"><i data-lucide="user" class="w-3 h-3"></i> Identité</div>
                <div class="flex items-center gap-6 mb-6">
                    <div class="param-avatar" id="edit-avatar"><?= $e($initial) ?></div>
                    <p class="text-xs text-gray-500 m-0">L'avatar est généré automatiquement depuis l'initiale du pseudo.</p>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.25rem;">
                    <div>
                        <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Pseudo (site)</label>
                        <input type="text" name="pseudo" id="edit-username" style="width: 100%;" value="<?= $e($val('pseudo')) ?>" required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_\-\.]+">
                    </div>
                    <div>
                        <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Pseudo Minecraft</label>
                        <input type="text" name="minecraft_pseudo" id="edit-mc" style="width: 100%;" value="<?= $e($val('minecraft_pseudo')) ?>" maxlength="16" pattern="[a-zA-Z0-9_]{3,16}">
                        <cite>Doit correspondre exactement à votre nom en jeu.</cite>
                    </div>
                    <div>
                        <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Adresse e-mail</label>
                        <input type="email" name="email" id="edit-email" style="width: 100%;" value="<?= $e($val('email')) ?>" required maxlength="255">
                        <cite>Changer d'adresse demande votre mot de passe actuel (section ci-dessous).</cite>
                    </div>
                </div>
            </div>

            <!-- Sécurité -->
            <div class="section-panel">
                <div class="section-panel--title"><i data-lucide="lock" class="w-3 h-3"></i> Changer le mot de passe</div>
                <cite>Laissez les nouveaux mots de passe vides si vous ne souhaitez pas modifier votre mot de passe. Votre mot de passe actuel est demandé pour changer de mot de passe ou d'adresse e-mail.</cite>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.25rem; margin-top: 1.25rem;">
                    <div>
                        <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Mot de passe actuel</label>
                        <input type="password" name="current_password" id="edit-pwd-current" style="width: 100%;" placeholder="••••••••••••" autocomplete="current-password">
                    </div>
                    <div>
                        <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Nouveau mot de passe</label>
                        <input type="password" name="new_password" id="edit-pwd-new" style="width: 100%;" placeholder="••••••••••••" minlength="8" autocomplete="new-password">
                    </div>
                    <div>
                        <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Confirmer le nouveau mot de passe</label>
                        <input type="password" name="new_password_confirm" id="edit-pwd-confirm" style="width: 100%;" placeholder="••••••••••••" minlength="8" autocomplete="new-password">
                    </div>
                </div>
            </div>

            <!-- Préférences -->
            <div class="section-panel">
                <div class="section-panel--title"><i data-lucide="sliders-horizontal" class="w-3 h-3"></i> Préférences</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.25rem;">
                    <div>
                        <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Notifications par e-mail</label>
                        <select name="email_notifications" id="edit-notif" style="width: 100%;">
                            <option value="1" <?= $val('email_notifications') === '1' ? 'selected' : '' ?>>Activées</option>
                            <option value="0" <?= $val('email_notifications') === '0' ? 'selected' : '' ?>>Désactivées</option>
                        </select>
                        <cite>Réponses du staff à vos tickets, décisions et messages sur vos candidatures.</cite>
                    </div>
                    <div>
                        <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Langue</label>
                        <select id="edit-lang" style="width: 100%;">
                            <option value="fr" selected>Français</option>
                            <option value="en" disabled>English (bientôt disponible)</option>
                        </select>
                    </div>
                </div>
            </div>

            <p id="save-status" class="hidden text-center text-xs mt-4 uppercase tracking-widest"></p>
        </form>

        <form id="char-delete-form" method="POST" action="assets/handlers/character-action" class="hidden">
            <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="return" value="parameters">
            <input type="hidden" name="character_id">
        </form>

    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<script>
    lucide.createIcons();

    /* ──── Bascule lecture ↔ édition ──── */
    function enterEdit() {
        document.getElementById('view-display').classList.add('hidden');
        document.getElementById('settings-form').classList.remove('hidden');
        document.getElementById('btn-edit-group').classList.add('hidden');
        document.getElementById('btn-save-group').classList.remove('hidden');
    }
    function exitEdit() {
        const form = document.getElementById('settings-form');
        form.reset();
        form.classList.add('hidden');
        document.getElementById('view-display').classList.remove('hidden');
        document.getElementById('btn-save-group').classList.add('hidden');
        document.getElementById('btn-edit-group').classList.remove('hidden');
        updateAvatar();
    }

    /* ──── Vérifications avant envoi ──── */
    document.getElementById('settings-form').addEventListener('submit', e => {
        const pwdNew     = document.getElementById('edit-pwd-new').value;
        const pwdConfirm = document.getElementById('edit-pwd-confirm').value;
        const status     = document.getElementById('save-status');
        if (pwdNew !== pwdConfirm) {
            e.preventDefault();
            status.textContent = '✕ Les mots de passe ne correspondent pas.';
            status.className = 'text-center text-xs mt-4 uppercase tracking-widest text-red-400';
        }
    });

    /* ──── Avatar : initiale du pseudo ──── */
    function updateAvatar() {
        const initial = (document.getElementById('edit-username').value.trim().charAt(0) || '?').toUpperCase();
        document.getElementById('edit-avatar').textContent = initial;
    }
    document.getElementById('edit-username').addEventListener('input', updateAvatar);

    /* ──── Personnages ──── */
    function deleteChar(id, name) {
        if (!confirm('Supprimer le personnage « ' + name + ' » ? Une candidature en attente sera retirée.')) return;
        const form = document.getElementById('char-delete-form');
        form.elements.character_id.value = id;
        form.submit();
    }
</script>

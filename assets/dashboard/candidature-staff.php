<?php
$me        = $auth->user();
$canView   = $groupManager->userCan($me, 'view-candidatures', 'manage-candidatures');
$canDecide = $groupManager->userCan($me, 'manage-candidatures');
$cand      = $canView ? $characters->findCandidature((int) ($_GET['id'] ?? 0)) : false;

if ($cand) {
    $character = $characters->find((int) $cand['character_id']);
    $messages  = $characters->messages((int) $cand['id']);
    $charHist  = $characters->candidaturesFor((int) $character['id']);
    $isLatest  = (int) ($charHist[0]['id'] ?? 0) === (int) $cand['id'];
    $player    = $tickets->playerSheet($cand['user_id']);
    $account   = $cand['user_id'] ? $db->fetchOne('SELECT discord_id, discord_username, max_characters FROM users WHERE id = ?', [$cand['user_id']]) : false;
    $others    = $cand['user_id'] ? array_filter($characters->candidaturesByUser($cand['user_id']), fn($o) => (int) $o['id'] !== (int) $cand['id']) : [];
    $status    = CharacterManager::STATUSES[$cand['status']];
    $number    = CharacterManager::number($cand['id']);
    $p         = $cand['player'];

    // Référence pour surligner les changements : version validée remplacée (acceptée) ou en vigueur (sinon)
    $compareSheet = $cand['type'] === 'modification'
        ? ($cand['status'] === 'accepted' ? $cand['previous_sheet'] : $character['sheet'])
        : null;

    $csrf  = Auth::generateCsrfToken();
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
}

$e     = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
$date  = fn($v) => $v ? date('d/m/Y à H:i', strtotime($v)) : '—';
$label = 'block text-xs text-cyan-400 uppercase tracking-widest mb-2';
$base  = fn(string $action) => '<input type="hidden" name="csrf_token" value="' . $e($csrf ?? '') . '">'
                             . '<input type="hidden" name="return" value="staff">'
                             . '<input type="hidden" name="candidature_id" value="' . (int) ($cand['id'] ?? 0) . '">'
                             . '<input type="hidden" name="character_id" value="' . (int) ($cand['character_id'] ?? 0) . '">'
                             . '<input type="hidden" name="action" value="' . $action . '">';
?>
<style>
    .staff-panel { padding: 1.25rem; margin-bottom: 1.5rem; }
    .staff-panel h5 { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
    .staff-panel select, .staff-panel input[type=number], .staff-panel textarea { width: 100%; }
    .staff-dl { display: grid; grid-template-columns: auto 1fr; gap: 0.35rem 1rem; font-size: 0.8rem; }
    .staff-dl dt { color: #6b7280; text-transform: uppercase; letter-spacing: 0.08em; font-size: 10px; padding-top: 2px; }
    .staff-dl dd { color: #e5e7eb; margin: 0; overflow-wrap: anywhere; }
    .staff-list-item { display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; padding: 0.5rem 0; border-bottom: 1px solid #ffffff0d; font-size: 0.8rem; }
    .staff-list-item:last-child { border-bottom: none; }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 md:pb-40 fade-in-view">
        <a href="./dashboard?page=candidatures" class="inline-flex items-center gap-2 text-gray-400 hover:text-cyan-400 transition-colors mb-6 text-sm uppercase tracking-widest">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour à la gestion des candidatures
        </a>

<?php if (!$canView): ?>
        <h3 class="pb-4 text-cyan-400">Candidature</h3>
        <p class="text-sm text-red-400">Vous n'avez pas la permission de consulter les candidatures.</p>
<?php elseif (!$cand): ?>
        <h3 class="pb-4 text-cyan-400">Candidature introuvable</h3>
        <p class="text-sm text-gray-500">Cette candidature n'existe pas.</p>
<?php else: ?>
        <h3 class="pb-4 text-cyan-400"><?= $number ?> — <?= $e($cand['sheet']['name']) ?></h3>
        <p class="text-gray-500 text-xs uppercase tracking-widest pb-4">
            Vue staff — <?= CharacterManager::TYPES[$cand['type']] ?> déposée par <?= $e($cand['author_name']) ?> le <?= $date($cand['created_at']) ?>
            <?php if ($character['deleted_at']): ?> — <span class="text-red-400">personnage supprimé par le joueur</span><?php endif; ?>
        </p>
        <div class="flex flex-wrap items-center gap-2 pb-8">
            <div class="badge-glass badge-glass--<?= $status['badge'] ?>"><span class="badge-glass--text"><?= $status['label'] ?></span></div>
            <div class="badge-glass badge-glass--<?= $cand['type'] === 'modification' ? 'warning' : 'primary' ?>"><span class="badge-glass--text"><?= CharacterManager::TYPES[$cand['type']] ?></span></div>
            <div class="badge-glass badge-glass--<?= (CharacterManager::JOBS[$cand['sheet']['job']] ?? CharacterManager::JOBS['Autre'])['badge'] ?>"><span class="badge-glass--text"><?= $e($cand['sheet']['job']) ?></span></div>
        </div>

        <?php if ($flash): ?>
        <p class="text-sm mb-6 <?= $flash['type'] === 'success' ? 'text-cyan-400' : 'text-red-400' ?>"><?= $e($flash['message']) ?></p>
        <?php endif; ?>

        <div class="grid gap-8 xl:grid-cols-3">

            <!-- ================= Colonne principale ================= -->
            <div class="xl:col-span-2 min-w-0">

                <!-- Joueur & avertissements -->
                <h4 class="pb-4">Joueur</h4>
                <section class="card-holographic mb-8" style="<?= $p['epileptic'] || in_array('none', $p['violence'], true) ? 'border-left-color: #eab308;' : '' ?>">
                    <dl class="staff-dl">
                        <dt>Âge</dt><dd><?= (int) $p['age'] ?> ans</dd>
                        <dt>Micro</dt><dd><?= $e(CharacterManager::MICS[$p['mic']] ?? $p['mic']) ?></dd>
                        <dt>Minecraft</dt><dd class="font-mono text-cyan-400"><?= $e($p['minecraft']) ?></dd>
                        <dt>Discord</dt><dd><?= $p['discord'] ? $e($p['discord']) : '<span class="text-gray-500">non lié lors du dépôt</span>' ?></dd>
                        <dt>Épilepsie</dt>
                        <dd><?= $p['epileptic'] ? '<span class="text-yellow-400 font-bold">⚠ Oui — éviter les effets lumineux / clignotants</span>' : 'Non' ?></dd>
                        <dt>Violence</dt>
                        <dd>
                            <?php foreach ($p['violence'] as $v): ?>
                            <div class="<?= $v === 'none' ? 'text-yellow-400 font-bold' : '' ?>"><?= $v === 'none' ? '⚠ ' : '' ?><?= $e(CharacterManager::VIOLENCE[$v] ?? $v) ?></div>
                            <?php endforeach; ?>
                        </dd>
                    </dl>
                </section>

                <!-- Fiche -->
                <h4 class="pb-4">Fiche du personnage<?= $compareSheet !== null ? ' <span class="text-xs text-yellow-400 normal-case tracking-normal">— changements surlignés</span>' : '' ?></h4>
                <?php $sheetData = $cand['sheet']; include __DIR__ . '/../elements/character-sheet.php'; ?>

                <!-- Discussion -->
                <h4 class="pb-4">Discussion avec le joueur</h4>
                <?php $discCandidature = $cand; $discMessages = $messages; $discReturn = 'staff'; include __DIR__ . '/../elements/candidature-discussion.php'; ?>
            </div>

            <!-- ================= Colonne staff ================= -->
            <aside class="min-w-0">

                <!-- Décision -->
                <section class="card-glass-panel staff-panel">
                    <h5><i data-lucide="gavel" class="w-4 h-4 text-cyan-400"></i> Décision</h5>
                    <?php if ($cand['status'] === 'pending'): ?>
                        <?php if ($canDecide): ?>
                        <form method="POST" action="assets/handlers/character-action" class="mb-4">
                            <?= $base('decide') ?>
                            <input type="hidden" name="decision" value="accepted">
                            <label class="<?= $label ?>">Commentaire d'acceptation (optionnel)</label>
                            <textarea name="comment" maxlength="500" rows="2" class="w-full bg-black/50 border border-gray-700 rounded p-2 text-white text-sm focus:border-green-500 focus:outline-none mb-2"></textarea>
                            <button type="submit" class="origin-btn btn--full-graphic btn--success w-full justify-center" <?= $character['deleted_at'] ? 'disabled' : '' ?> onclick="return confirm('Accepter cette candidature ?')">
                                <span>Accepter</span><i data-lucide="check" class="btn--icon"></i>
                            </button>
                        </form>
                        <form method="POST" action="assets/handlers/character-action">
                            <?= $base('decide') ?>
                            <input type="hidden" name="decision" value="refused">
                            <label class="<?= $label ?>">Motif du refus (obligatoire)</label>
                            <textarea name="comment" required minlength="3" maxlength="500" rows="3" class="w-full bg-black/50 border border-gray-700 rounded p-2 text-white text-sm focus:border-red-500 focus:outline-none mb-2"></textarea>
                            <button type="submit" class="origin-btn btn--graphic btn--danger w-full justify-center" onclick="return confirm('Refuser cette candidature ?')">
                                <span>Refuser</span><i data-lucide="x" class="btn--icon"></i>
                            </button>
                        </form>
                        <p class="text-[10px] text-gray-500 mt-3">Les commentaires sont visibles par le joueur.</p>
                        <?php else: ?>
                        <p class="text-xs text-gray-400">En attente de décision. Vous pouvez échanger avec le joueur, mais seule la permission « manage-candidatures » permet de décider.</p>
                        <?php endif; ?>
                    <?php elseif ($cand['status'] === 'withdrawn'): ?>
                    <p class="text-xs text-gray-400">Candidature retirée (personnage supprimé par le joueur).</p>
                    <?php else: ?>
                    <p class="text-sm <?= $cand['status'] === 'accepted' ? 'text-green-400' : 'text-red-400' ?> mb-2"><?= $status['label'] ?> le <?= $date($cand['decided_at']) ?> par <?= $e($cand['decided_by']) ?></p>
                    <?php if ($cand['decision_comment']): ?>
                    <p class="text-xs text-gray-300 mb-4" style="white-space: pre-line;"><?= $e($cand['decision_comment']) ?></p>
                    <?php endif; ?>
                    <?php if ($canDecide && $isLatest): ?>
                    <form method="POST" action="assets/handlers/character-action" onsubmit="return confirm('Annuler la décision ? La candidature repassera en attente<?= $cand['status'] === 'accepted' ? ' et la version validée précédente sera restaurée' : '' ?>.')">
                        <?= $base('cancel') ?>
                        <button type="submit" class="origin-btn btn--graphic btn--secondary w-full justify-center">
                            <span>Annuler la décision</span><i data-lucide="rotate-ccw" class="btn--icon"></i>
                        </button>
                    </form>
                    <?php elseif (!$isLatest): ?>
                    <p class="text-[10px] text-gray-500">Une candidature plus récente existe pour ce personnage.</p>
                    <?php endif; ?>
                    <?php endif; ?>
                </section>

                <!-- Personnage -->
                <section class="card-glass-panel staff-panel">
                    <h5><i data-lucide="theater" class="w-4 h-4 text-cyan-400"></i> Personnage</h5>
                    <dl class="staff-dl mb-4">
                        <dt>Nom actuel</dt><dd><?= $e($character['name']) ?></dd>
                        <dt>Validé</dt><dd><?= $character['sheet'] !== null ? 'Oui, le ' . $date($character['validated_at']) : 'Non' ?></dd>
                        <dt>Statut</dt><dd><?= $character['sheet'] !== null ? ($character['active'] ? 'Actif' : 'Inactif') : '—' ?></dd>
                    </dl>
                    <?php if ($canDecide && !$character['deleted_at']): ?>
                    <form method="POST" action="assets/handlers/character-action">
                        <?= $base('set_level') ?>
                        <label class="<?= $label ?>">Niveau de spécialisation</label>
                        <div class="flex gap-2">
                            <select name="level">
                                <?php foreach (CharacterManager::LEVELS as $key => $lvl): ?>
                                <option value="<?= $key ?>" <?= $character['level'] === $key ? 'selected' : '' ?>><?= $lvl['label'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="origin-btn btn--graphic btn--primary" title="Enregistrer"><i data-lucide="save" class="btn--icon"></i></button>
                        </div>
                    </form>
                    <?php endif; ?>
                    <?php if (count($charHist) > 1): ?>
                    <div class="text-xs text-gray-500 uppercase tracking-widest mt-4 mb-1">Candidatures de ce personnage</div>
                    <?php foreach ($charHist as $h): $hs = CharacterManager::STATUSES[$h['status']]; ?>
                    <div class="staff-list-item">
                        <a href="./dashboard?page=candidature-staff&id=<?= (int) $h['id'] ?>" class="<?= (int) $h['id'] === (int) $cand['id'] ? 'text-cyan-400' : '' ?>"><?= CharacterManager::number($h['id']) ?> — <?= CharacterManager::TYPES[$h['type']] ?></a>
                        <div class="badge-glass badge-glass--<?= $hs['badge'] ?>"><span class="badge-glass--text"><?= $hs['label'] ?></span></div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </section>

                <!-- Joueur (compte) -->
                <section class="card-glass-panel staff-panel">
                    <h5><i data-lucide="contact-round" class="w-4 h-4 text-cyan-400"></i> Compte du joueur</h5>
                    <?php if (!$player): ?>
                    <p class="text-xs text-gray-500">Le compte de <?= $e($cand['author_name']) ?> n'existe plus.</p>
                    <?php else: $pStatus = UserManager::STATUSES[$player['status']]; ?>
                    <div class="flex items-center flex-wrap gap-2 mb-4">
                        <span class="text-white font-bold"><?= $e($player['pseudo']) ?></span>
                        <?php if ($player['group_name']): ?>
                        <div class="badge-glass badge-glass--<?= $e($player['group_badge']) ?>"><span class="badge-glass--text"><?= $e($player['group_name']) ?></span></div>
                        <?php endif; ?>
                        <div class="badge-glass badge-glass--<?= $pStatus['badge'] ?>"><span class="badge-glass--text"><?= $pStatus['label'] ?></span></div>
                    </div>
                    <dl class="staff-dl mb-4">
                        <dt>Discord</dt><dd><?= $account['discord_id'] ? $e($account['discord_username']) . ' <span class="text-gray-600">(' . $e($account['discord_id']) . ')</span>' : '<span class="text-red-400">non lié</span>' ?></dd>
                        <dt>Inscrit le</dt><dd><?= $date($player['created_at']) ?></dd>
                        <dt>Sanctions</dt><dd class="<?= $player['sanction_count'] ? 'text-red-400' : '' ?>"><?= $player['sanction_count'] ?></dd>
                        <dt>Tickets</dt><dd><?= $player['ticket_count'] ?></dd>
                    </dl>
                    <?php if ($canDecide): ?>
                    <form method="POST" action="assets/handlers/character-action" class="mb-4">
                        <?= $base('set_max') ?>
                        <input type="hidden" name="user_id" value="<?= $e($player['id']) ?>">
                        <label class="<?= $label ?>">Personnages autorisés <span class="text-gray-500 normal-case tracking-normal">(1 par défaut)</span></label>
                        <div class="flex gap-2">
                            <input type="number" name="max_characters" value="<?= (int) $account['max_characters'] ?>" min="1" max="10">
                            <button type="submit" class="origin-btn btn--graphic btn--primary" title="Enregistrer"><i data-lucide="save" class="btn--icon"></i></button>
                        </div>
                    </form>
                    <?php endif; ?>
                    <a href="./dashboard?page=profil-view&id=<?= $e(urlencode($player['id'])) ?>" class="origin-btn btn--graphic btn--primary" style="font-size: 11px;">
                        <span>Fiche joueur</span><i data-lucide="eye" class="btn--icon"></i>
                    </a>
                    <?php endif; ?>
                </section>

                <!-- Autres candidatures du joueur -->
                <section class="card-glass-panel staff-panel">
                    <h5><i data-lucide="files" class="w-4 h-4 text-cyan-400"></i> Autres candidatures du joueur</h5>
                    <?php if (!$others): ?>
                    <p class="text-xs text-gray-500">Aucune autre candidature.</p>
                    <?php endif; ?>
                    <?php foreach ($others as $o): $os = CharacterManager::STATUSES[$o['status']]; ?>
                    <div class="staff-list-item">
                        <a href="./dashboard?page=candidature-staff&id=<?= (int) $o['id'] ?>" class="min-w-0" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <span class="text-gray-500"><?= CharacterManager::number($o['id']) ?></span> <?= $e($o['character_name']) ?>
                        </a>
                        <div class="badge-glass badge-glass--<?= $os['badge'] ?>" style="flex-shrink: 0;"><span class="badge-glass--text"><?= $os['label'] ?></span></div>
                    </div>
                    <?php endforeach; ?>
                </section>
            </aside>
        </div>
<?php endif; ?>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<script>
    lucide.createIcons();
</script>

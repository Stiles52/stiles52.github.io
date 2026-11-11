<?php
$me          = $auth->user();
$pre         = $characters->prerequisites($me['id']);
$characterId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$character   = $characterId ? $characters->find($characterId) : null;
$notFound    = $characterId && (!$character || $character['user_id'] !== $me['id'] || $character['deleted_at'] !== null);
$limitHit    = !$characterId && $pre['count'] >= $pre['max'];

$latest = $character && !$notFound ? $characters->latestCandidature((int) $character['id']) : false;
$csrf   = Auth::generateCsrfToken();
$flash  = $_SESSION['flash'] ?? null;
$old    = $_SESSION['character_form'] ?? null;
unset($_SESSION['flash'], $_SESSION['character_form']);

// Valeurs du formulaire : saisie précédente (erreur) > dernière soumission > fiche validée
$sheet  = $latest['sheet'] ?? $character['sheet'] ?? [];
$player = $latest['player'] ?? [];
$v = function (string $key, string $src = 'sheet') use ($old, $sheet, $player, $pre) {
    if ($old !== null) {
        return $old[$key] ?? '';
    }
    return match ($key) {
        'char_age'         => $sheet['age'] ?? '',
        'player_age'       => $player['age'] ?? '',
        'mic'              => $player['mic'] ?? '',
        'epileptic'        => isset($player['epileptic']) ? ($player['epileptic'] ? 'yes' : 'no') : '',
        'violence'         => $player['violence'] ?? [],
        'hybrid'           => isset($sheet['hybrid']) ? ($sheet['hybrid'] ? '1' : '0') : '',
        'minecraft_pseudo' => $pre['minecraft'] ?? '',
        default            => $sheet[$key] ?? '',
    };
};

$e     = fn($val) => htmlspecialchars((string) $val, ENT_QUOTES);
$label = 'block text-xs text-cyan-400 uppercase tracking-widest mb-2';
$area  = 'w-full bg-black/50 border border-gray-700 rounded p-3 text-white focus:border-cyan-500 focus:outline-none transition-colors';
?>
<style>
    .char-form-section { padding: 1.5rem; margin-bottom: 1.5rem; }
    .char-form-section h5 { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem; }
    .char-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; }
    .char-form-grid input[type=text], .char-form-grid input[type=number], .char-form-grid select { width: 100%; }
    .prereq { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid #ffffff0d; font-size: 0.85rem; }
    .prereq:last-child { border-bottom: none; }
    .choice { display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #d1d5db; cursor: pointer; margin-bottom: 0.5rem; }
    .staff-only { font-size: 10px; color: #eab308; text-transform: none; letter-spacing: normal; }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 md:pb-40 fade-in-view">
        <a href="<?= $character && !$notFound ? './dashboard?page=personnage&id=' . (int) $character['id'] : './dashboard?page=personnages' ?>" class="inline-flex items-center gap-2 text-gray-400 hover:text-cyan-400 transition-colors mb-6 text-sm uppercase tracking-widest">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour
        </a>
        <h3 class="pb-4 text-cyan-400"><?= $character && !$notFound ? 'Modifier : ' . $e($character['name']) : 'Nouveau personnage' ?></h3>
        <p class="text-gray-500 text-xs uppercase tracking-widest pb-8">Candidature de personnage — examinée par le staff avant validation</p>

<?php if ($notFound): ?>
        <p class="text-sm text-gray-500">Ce personnage n'existe pas.</p>
<?php elseif ($limitHit): ?>
        <p class="text-sm text-red-400">
            Vous avez déjà <?= $pre['count'] ?> personnage<?= $pre['count'] > 1 ? 's' : '' ?> (limite : <?= $pre['max'] ?>).
            Pour en créer un autre, contactez le staff via un <a href="./dashboard?page=ticket-actions&ticket=recrute">ticket Recrutement</a>.
        </p>
<?php else: ?>
        <?php if ($flash): ?>
        <p class="text-sm mb-6 <?= $flash['type'] === 'success' ? 'text-cyan-400' : 'text-red-400' ?>"><?= $e($flash['message']) ?></p>
        <?php endif; ?>

        <?php if ($latest && $latest['status'] === 'pending'): ?>
        <p class="text-sm text-yellow-400 mb-6">Une candidature est déjà en attente pour ce personnage : vos changements remplaceront la fiche soumise (la discussion est conservée).</p>
        <?php elseif ($character && $character['sheet'] !== null): ?>
        <p class="text-sm text-yellow-400 mb-6">Ce personnage est validé : vos modifications repasseront en validation. La version actuelle reste en vigueur jusqu'à la décision du staff.</p>
        <?php endif; ?>

        <form method="POST" action="assets/handlers/character-action" style="max-width: 1000px;">
            <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
            <input type="hidden" name="action" value="submit">
            <input type="hidden" name="return" value="form">
            <?php if ($character): ?>
            <input type="hidden" name="character_id" value="<?= (int) $character['id'] ?>">
            <?php endif; ?>

            <!-- Prérequis -->
            <section class="card-glass-panel char-form-section">
                <h5><i data-lucide="list-checks" class="w-4 h-4 text-cyan-400"></i> Prérequis</h5>
                <div class="prereq">
                    <?php if ($pre['discord_skipped']): ?>
                    <i data-lucide="triangle-alert" class="w-5 h-5 text-yellow-400"></i>
                    <span class="flex-1">[DEV] Liaison Discord non configurée sur le site : exigence ignorée en développement.</span>
                    <?php elseif ($pre['discord']): ?>
                    <i data-lucide="circle-check" class="w-5 h-5 text-green-400"></i>
                    <span class="flex-1">Compte Discord lié : <strong class="text-white"><?= $e($pre['discord_username']) ?></strong></span>
                    <?php else: ?>
                    <i data-lucide="circle-x" class="w-5 h-5 text-red-400"></i>
                    <span class="flex-1">Votre compte Discord doit être lié à votre compte du site.</span>
                    <a href="assets/handlers/discord-link?from=form" class="origin-btn btn--glass btn--discord" style="font-size: 12px;">
                        <span>Lier mon Discord</span>
                    </a>
                    <?php endif; ?>
                </div>
                <div class="prereq" style="flex-wrap: wrap;">
                    <i data-lucide="monitor" class="w-5 h-5 text-cyan-400"></i>
                    <label class="flex-1" for="minecraft_pseudo">Pseudo Minecraft <span class="text-gray-500 text-xs">(enregistré sur votre compte)</span></label>
                    <input type="text" id="minecraft_pseudo" name="minecraft_pseudo" value="<?= $e($v('minecraft_pseudo')) ?>" required minlength="3" maxlength="16" pattern="[a-zA-Z0-9_]{3,16}" placeholder="Pseudo exact en jeu" style="max-width: 260px;">
                </div>
            </section>

            <!-- Joueur -->
            <section class="card-glass-panel char-form-section">
                <h5><i data-lucide="user" class="w-4 h-4 text-cyan-400"></i> Vous <span class="staff-only">— visible uniquement par le staff</span></h5>
                <div class="char-form-grid">
                    <div>
                        <label class="<?= $label ?>">Votre âge</label>
                        <input type="number" name="player_age" value="<?= $e($v('player_age')) ?>" required min="10" max="99">
                    </div>
                    <div>
                        <label class="<?= $label ?>">Qualité / état de votre micro</label>
                        <select name="mic" required>
                            <option value="">— Sélectionnez —</option>
                            <?php foreach (CharacterManager::MICS as $key => $lbl): ?>
                            <option value="<?= $key ?>" <?= $v('mic') === $key ? 'selected' : '' ?>><?= $e($lbl) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </section>

            <!-- Avertissements -->
            <section class="card-glass-panel char-form-section" style="border-top-color: #eab308;">
                <h5><i data-lucide="triangle-alert" class="w-4 h-4 text-yellow-400"></i> Avertissements <span class="staff-only">— visible uniquement par le staff, pour adapter les scènes</span></h5>
                <div class="char-form-grid">
                    <div>
                        <label class="<?= $label ?>">Êtes-vous épileptique ?</label>
                        <label class="choice"><input type="radio" name="epileptic" value="yes" required <?= $v('epileptic') === 'yes' ? 'checked' : '' ?>> Oui</label>
                        <label class="choice"><input type="radio" name="epileptic" value="no" <?= $v('epileptic') === 'no' ? 'checked' : '' ?>> Non</label>
                    </div>
                    <div>
                        <label class="<?= $label ?>">Votre rapport à la violence en jeu</label>
                        <?php foreach (CharacterManager::VIOLENCE as $key => $lbl): ?>
                        <label class="choice"><input type="checkbox" name="violence[]" value="<?= $key ?>" class="violence-choice" <?= in_array($key, (array) $v('violence'), true) ? 'checked' : '' ?>> <?= $e($lbl) ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <!-- Personnage : identité -->
            <section class="card-glass-panel char-form-section">
                <h5><i data-lucide="id-card" class="w-4 h-4 text-cyan-400"></i> Identité du personnage</h5>
                <div class="char-form-grid">
                    <div style="grid-column: 1 / -1;">
                        <label class="<?= $label ?>">Prénom & Nom du personnage</label>
                        <input type="text" name="name" value="<?= $e($v('name')) ?>" required minlength="3" maxlength="80" placeholder="Ex : Alaric Vorn">
                    </div>
                    <div>
                        <label class="<?= $label ?>">Métier</label>
                        <select name="job" required>
                            <option value="">— Sélectionnez —</option>
                            <?php foreach (array_keys(CharacterManager::JOBS) as $job): ?>
                            <option <?= $v('job') === $job ? 'selected' : '' ?>><?= $e($job) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="<?= $label ?>">Âge du personnage</label>
                        <input type="number" name="char_age" value="<?= $e($v('char_age')) ?>" required min="1" max="1000">
                    </div>
                    <div>
                        <label class="<?= $label ?>">Sexe</label>
                        <select name="gender" required>
                            <option value="">— Sélectionnez —</option>
                            <?php foreach (CharacterManager::GENDERS as $g): ?>
                            <option <?= $v('gender') === $g ? 'selected' : '' ?>><?= $e($g) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="<?= $label ?>">Origine</label>
                        <input type="text" name="origin" value="<?= $e($v('origin')) ?>" required minlength="2" maxlength="100" placeholder="Lieu de naissance, peuple…">
                    </div>
                    <div>
                        <label class="<?= $label ?>">Taille en jeu <span class="text-gray-500 normal-case tracking-normal">(en cm)</span></label>
                        <input type="number" name="height" value="<?= $e($v('height')) ?>" required min="<?= CharacterManager::HEIGHT_MIN ?>" max="<?= CharacterManager::HEIGHT_MAX ?>" step="1" placeholder="Ex : 180">
                    </div>
                    <div>
                        <label class="<?= $label ?>">Hybridation</label>
                        <label class="choice"><input type="radio" name="hybrid" value="0" required <?= $v('hybrid') !== '1' && $v('hybrid') !== '' ? 'checked' : '' ?> onchange="toggleHybrid()"> Non</label>
                        <label class="choice"><input type="radio" name="hybrid" value="1" <?= $v('hybrid') === '1' ? 'checked' : '' ?> onchange="toggleHybrid()"> Oui</label>
                    </div>
                    <div id="hybrid-detail-wrap" style="grid-column: 1 / -1;" class="<?= $v('hybrid') === '1' ? '' : 'hidden' ?>">
                        <label class="<?= $label ?>">Détail de l'hybridation</label>
                        <input type="text" name="hybrid_detail" value="<?= $e($v('hybrid_detail')) ?>" maxlength="300" placeholder="Nature de l'hybridation, traits visibles…" style="width: 100%;">
                    </div>
                </div>
            </section>

            <!-- Personnage : description -->
            <section class="card-glass-panel char-form-section">
                <h5><i data-lucide="scroll-text" class="w-4 h-4 text-cyan-400"></i> Fiche du personnage</h5>
                <?php foreach ([
                    ['appearance',  'Apparence physique',      50,  3000, 4, 'Silhouette, visage, cicatrices, tenue habituelle…'],
                    ['personality', 'Personnalité',            50,  3000, 4, 'Caractère, qualités, défauts, manies…'],
                    ['story',       'Histoire & motivations',  200, 8000, 10, 'Son passé, ce qui l\'a mené ici et ses objectifs sur le serveur…'],
                    ['description', 'Description courte (optionnel)', 0, 500, 2, 'Quelques mots pour présenter le personnage…'],
                ] as [$key, $lbl, $min, $max, $rows, $ph]): ?>
                <div class="w-full mb-6">
                    <label class="<?= $label ?>"><?= $lbl ?> <?php if ($min): ?><span class="text-gray-500 normal-case tracking-normal">(<?= $min ?> caractères min.)</span><?php endif; ?></label>
                    <textarea name="<?= $key ?>" rows="<?= $rows ?>" maxlength="<?= $max ?>" <?= $min ? "required minlength=\"$min\"" : '' ?> class="<?= $area ?> char-counted" placeholder="<?= $e($ph) ?>"><?= $e($v($key)) ?></textarea>
                    <div class="text-right text-[10px] text-gray-500 mt-1 font-mono char-count">0 / <?= $max ?></div>
                </div>
                <?php endforeach; ?>
            </section>

            <button type="submit" class="w-full bg-cyan-500 hover:bg-cyan-400 text-black font-bold py-3 rounded uppercase tracking-widest transition-colors flex justify-center items-center gap-2" <?= $pre['discord'] ? '' : 'disabled style="opacity: .4; cursor: not-allowed;"' ?>>
                <span><?= $latest && $latest['status'] === 'pending' ? 'Mettre à jour la candidature' : 'Envoyer la candidature' ?></span>
                <i data-lucide="send" class="w-4 h-4"></i>
            </button>
        </form>
<?php endif; ?>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<script>
    lucide.createIcons();

    function toggleHybrid() {
        const yes = document.querySelector('input[name="hybrid"][value="1"]');
        const wrap = document.getElementById('hybrid-detail-wrap');
        if (!yes || !wrap) return;
        wrap.classList.toggle('hidden', !yes.checked);
        wrap.querySelector('input').required = yes.checked;
    }
    toggleHybrid();

    // « Ne souhaite pas subir de scènes violentes » exclut les deux autres choix
    document.querySelectorAll('.violence-choice').forEach(cb => cb.addEventListener('change', () => {
        if (!cb.checked) return;
        document.querySelectorAll('.violence-choice').forEach(other => {
            if (other !== cb && (cb.value === 'none' || other.value === 'none')) other.checked = false;
        });
    }));

    // Compteurs de caractères
    document.querySelectorAll('.char-counted').forEach(area => {
        const counter = area.parentElement.querySelector('.char-count');
        const update  = () => {
            const max = area.getAttribute('maxlength');
            counter.textContent = `${area.value.length} / ${max}`;
            counter.classList.toggle('text-red-500', area.value.length >= max);
        };
        area.addEventListener('input', update);
        update();
    });
</script>

<?php
$me     = $auth->user();
$canUse = $groupManager->userCan($me, 'manage-tickets');
$ticket = $canUse ? $tickets->find((int) ($_GET['id'] ?? 0)) : false;

if ($ticket) {
    $messages     = $tickets->messages((int) $ticket['id']);
    $participants = $tickets->participants((int) $ticket['id']);
    $notes        = $tickets->notes((int) $ticket['id']);
    $events       = $tickets->events((int) $ticket['id']);
    $templates    = $tickets->templates();
    $staff        = $tickets->staffMembers();
    $player       = $tickets->playerSheet($ticket['user_id']);
    $others       = $tickets->otherTickets($ticket);
    $current      = $player && $player['status'] !== 'active' ? $sanctions->current($player['id']) : false;
    $staffStatus  = TicketManager::STAFF_STATUSES[TicketManager::staffStatus($ticket)];
    $category     = TicketManager::CATEGORIES[$ticket['category']] ?? ['label' => $ticket['category'], 'badge' => 'secondary'];
    $isOpen       = $tickets->isOpen($ticket);
    $number       = TicketManager::number($ticket['id']);
    $canSeeUsers  = $groupManager->userCan($me, 'view-users', 'manage-users');
    $accept       = implode(',', array_map(fn($x) => '.' . $x, TicketManager::allowedExtensions($ticket['category'])));
    $csrf         = Auth::generateCsrfToken();
    $flash        = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
}

$e     = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
$date  = fn($v) => $v ? date('d/m/Y à H:i', strtotime($v)) : '—';
// Champs cachés communs aux formulaires de la page
$base  = fn(string $action) => '<input type="hidden" name="csrf_token" value="' . $e($csrf ?? '') . '">'
                             . '<input type="hidden" name="return" value="staff">'
                             . '<input type="hidden" name="ticket_id" value="' . (int) ($ticket['id'] ?? 0) . '">'
                             . '<input type="hidden" name="action" value="' . $action . '">';
$label = 'block text-xs text-cyan-400 uppercase tracking-widest mb-2';
?>
<style>
    .staff-panel { padding: 1.25rem; margin-bottom: 1.5rem; }
    .staff-panel h5 { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
    .staff-panel select, .staff-panel input[type=text], .staff-panel textarea { width: 100%; }
    .staff-dl { display: grid; grid-template-columns: auto 1fr; gap: 0.35rem 1rem; font-size: 0.8rem; }
    .staff-dl dt { color: #6b7280; text-transform: uppercase; letter-spacing: 0.08em; font-size: 10px; padding-top: 2px; }
    .staff-dl dd { color: #e5e7eb; margin: 0; overflow-wrap: anywhere; }
    .staff-note { border-left: 2px solid #eab30880; padding: 0.25rem 0 0.25rem 0.75rem; margin-bottom: 1rem; }
    .staff-list-item { display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; padding: 0.5rem 0; border-bottom: 1px solid #ffffff0d; font-size: 0.8rem; }
    .staff-list-item:last-child { border-bottom: none; }
    .staff-event { font-size: 0.75rem; padding: 0.35rem 0; border-bottom: 1px solid #ffffff0d; }
    .staff-event:last-child { border-bottom: none; }
    details.staff-templates summary { cursor: pointer; list-style: none; }
    details.staff-templates summary::-webkit-details-marker { display: none; }
</style>

<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 md:pb-40 fade-in-view">
        <a href="./dashboard?page=manage-tickets" class="inline-flex items-center gap-2 text-gray-400 hover:text-cyan-400 transition-colors mb-6 text-sm uppercase tracking-widest">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour à la gestion des tickets
        </a>

<?php if (!$canUse): ?>
        <h3 class="pb-4 text-cyan-400">Vue staff</h3>
        <p class="text-sm text-red-400">Vous n'avez pas la permission de gérer les tickets.</p>
<?php elseif (!$ticket): ?>
        <h3 class="pb-4 text-cyan-400">Ticket introuvable</h3>
        <p class="text-sm text-gray-500">Ce ticket n'existe pas.</p>
<?php else: ?>
        <h3 class="pb-4 text-cyan-400"><?= $number ?> — <?= $e($ticket['title']) ?></h3>
        <p class="text-gray-500 text-xs uppercase tracking-widest pb-4">
            Vue staff — ouvert par <?= $e($ticket['author_name']) ?> le <?= $date($ticket['created_at']) ?>
            <?php if ($participants): ?> — Participants : <?= $e(implode(', ', $participants)) ?><?php endif; ?>
        </p>
        <div class="flex flex-wrap items-center gap-2 pb-8">
            <div class="badge-glass badge-glass--<?= $staffStatus['badge'] ?>"><span class="badge-glass--text"><?= $e($staffStatus['label']) ?></span></div>
            <?php if ($ticket['priority']): ?>
            <div class="badge-glass badge-glass--<?= TicketManager::PRIORITIES[$ticket['priority']]['badge'] ?>"><span class="badge-glass--text"><?= $e(TicketManager::PRIORITIES[$ticket['priority']]['label']) ?></span></div>
            <?php endif; ?>
            <div class="badge-glass badge-glass--<?= $e($category['badge']) ?>"><span class="badge-glass--text">Ticket <?= $e($category['label']) ?></span></div>
        </div>

        <?php if ($flash): ?>
        <p class="text-sm mb-6 <?= $flash['type'] === 'success' ? 'text-cyan-400' : 'text-red-400' ?>"><?= $e($flash['message']) ?></p>
        <?php endif; ?>

        <div class="grid gap-8 xl:grid-cols-3">

            <!-- ================= Colonne principale : conversation ================= -->
            <div class="xl:col-span-2 min-w-0">

                <?php if ($ticket['fields']): ?>
                <h4 class="pb-4">Informations</h4>
                <section class="card-holographic mb-8">
                    <dl class="staff-dl">
                        <?php foreach ($ticket['fields'] as $f): ?>
                        <dt><?= $e($f['label']) ?></dt>
                        <dd>
                            <?php if ($f['label'] === 'Vidéo'): ?>
                            <a href="<?= $e($f['value']) ?>" target="_blank" rel="noopener noreferrer"><?= $e($f['value']) ?></a>
                            <?php else: ?>
                            <?= $e($f['value']) ?>
                            <?php endif; ?>
                        </dd>
                        <?php endforeach; ?>
                    </dl>
                </section>
                <?php endif; ?>

                <h4 class="pb-4">Conversation</h4>
                <?php foreach ($messages as $m): ?>
                <section class="card-holographic mb-8" <?= $m['is_staff'] ? 'style="border-left-color: #22d3ee;"' : '' ?>>
                    <div class="card-holographic--title" style="margin-bottom: 0.75rem; flex-wrap: wrap;">
                        <i data-lucide="<?= $m['is_staff'] ? 'message-circle-warning' : 'message-circle' ?>" class="w-5 h-5 text-cyan-400"></i>
                        <span>Message de : <?= $e($m['author_name']) ?></span>
                        <?php if ($m['is_staff']): ?>
                        <div class="badge-glass badge-glass--primary"><span class="badge-glass--text">Staff</span></div>
                        <?php if ($m['author_group']): ?>
                        <div class="badge-glass badge-glass--<?= $e($m['author_badge'] ?? 'secondary') ?>"><span class="badge-glass--text"><?= $e($m['author_group']) ?></span></div>
                        <?php endif; ?>
                        <?php endif; ?>
                        <span class="text-xs text-gray-500" style="margin-left: auto; text-transform: none;"><?= $date($m['created_at']) ?></span>
                    </div>
                    <p class="text-sm text-gray-300" style="white-space: pre-line; overflow-wrap: anywhere;"><?= $e($m['body']) ?></p>
                    <?php if ($m['attachments']): ?>
                    <div class="flex flex-wrap gap-3 mt-4">
                        <?php foreach ($m['attachments'] as $a): ?>
                        <a href="assets/handlers/ticket-attachment?id=<?= (int) $a['id'] ?>" class="inline-flex items-center gap-2 text-xs bg-gray-900 border border-gray-700 px-2 py-1 text-gray-300 hover:text-cyan-400">
                            <i data-lucide="paperclip" class="w-3 h-3"></i>
                            <?= $e($a['original_name']) ?> <span class="text-gray-600">(<?= max(1, round($a['size'] / 1024)) ?> Ko)</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </section>
                <?php endforeach; ?>

                <?php if ($isOpen): ?>
                <!-- Réponse -->
                <form method="POST" action="assets/handlers/ticket-action" enctype="multipart/form-data" class="card-glass-panel" style="padding: 1.5rem;">
                    <?= $base('reply') ?>

                    <div class="flex flex-wrap items-end gap-4 mb-4">
                        <div style="flex: 1; min-width: 220px;">
                            <label class="<?= $label ?>">Réponse prédéfinie</label>
                            <select id="template-select" style="width: 100%;" onchange="insertTemplate(this)">
                                <option value="">— Insérer un modèle —</option>
                                <?php foreach ($templates as $tpl): ?>
                                <option value="<?= (int) $tpl['id'] ?>" data-body="<?= $e($tpl['body']) ?>"><?= $e($tpl['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="w-full">
                        <label class="<?= $label ?>">Votre réponse</label>
                        <textarea id="message" name="message" required minlength="10" rows="7" maxlength="4000" class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white focus:border-cyan-500 focus:outline-none transition-colors" placeholder="Votre réponse au joueur... (Max 4000 caractères)"></textarea>
                        <div id="char-count" class="text-right text-[10px] text-gray-500 mt-1 font-mono">0 / 4000</div>
                    </div>
                    <div class="w-full mt-4">
                        <label class="<?= $label ?>">Pièces jointes</label>
                        <input type="file" name="attachments[]" multiple accept="<?= $e($accept) ?>" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-cyan-500 file:text-black hover:file:bg-cyan-400 transition-colors">
                    </div>
                    <div class="flex gap-4 flex-wrap justify-end mt-6">
                        <button type="submit" name="resolve" value="1" class="origin-btn btn--graphic btn--success" onclick="return confirm('Envoyer la réponse et marquer le ticket comme résolu ?')">
                            <span>Envoyer et résoudre</span>
                            <i data-lucide="check-check" class="btn--icon"></i>
                        </button>
                        <button type="submit" class="origin-btn btn--full-graphic btn--success">
                            <span>Envoyer la réponse</span>
                            <i data-lucide="send" class="btn--icon"></i>
                        </button>
                    </div>
                </form>
                <?php else: ?>
                <p class="text-sm text-gray-500 text-center py-6">
                    Ce ticket est <?= $ticket['status'] === 'resolved' ? 'résolu' : 'fermé par le joueur' ?><?= $ticket['closed_at'] ? ' depuis le ' . $date($ticket['closed_at']) : '' ?>.
                    Changez son statut dans « Actions » pour le rouvrir.
                </p>
                <?php endif; ?>

                <!-- Gestion des réponses prédéfinies -->
                <details class="staff-templates card-glass-panel staff-panel mt-8">
                    <summary class="text-xs text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="library" class="w-4 h-4 text-cyan-400"></i>
                        Gérer les réponses prédéfinies (<?= count($templates) ?>)
                    </summary>
                    <div class="mt-4">
                        <?php foreach ($templates as $tpl): ?>
                        <div class="staff-list-item">
                            <div class="min-w-0">
                                <div class="text-white"><?= $e($tpl['title']) ?></div>
                                <div class="text-xs text-gray-500" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= $e(mb_strimwidth($tpl['body'], 0, 120, '…')) ?></div>
                            </div>
                            <form method="POST" action="assets/handlers/ticket-action" onsubmit="return confirm('Supprimer ce modèle pour tout le staff ?')">
                                <?= $base('template_delete') ?>
                                <input type="hidden" name="template_id" value="<?= (int) $tpl['id'] ?>">
                                <button type="submit" class="text-gray-500 hover:text-red-400" title="Supprimer"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                            </form>
                        </div>
                        <?php endforeach; ?>

                        <form method="POST" action="assets/handlers/ticket-action" class="mt-6">
                            <?= $base('template_create') ?>
                            <label class="<?= $label ?>">Nouveau modèle</label>
                            <input type="text" name="template_title" required minlength="2" maxlength="80" placeholder="Nom du modèle" class="mb-3" style="width: 100%;">
                            <textarea name="template_body" required minlength="10" maxlength="4000" rows="4" class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white focus:border-cyan-500 focus:outline-none transition-colors" placeholder="Texte du modèle. Variables : {joueur}, {staff}, {ticket}"></textarea>
                            <div class="flex justify-end mt-3">
                                <button type="submit" class="origin-btn btn--graphic btn--primary">
                                    <span>Ajouter le modèle</span>
                                    <i data-lucide="plus" class="btn--icon"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </details>
            </div>

            <!-- ================= Colonne staff ================= -->
            <aside class="min-w-0">

                <!-- Actions -->
                <form method="POST" action="assets/handlers/ticket-action" class="card-glass-panel staff-panel">
                    <?= $base('settings') ?>
                    <h5><i data-lucide="sliders-horizontal" class="w-4 h-4 text-cyan-400"></i> Actions</h5>
                    <div class="mb-4">
                        <label class="<?= $label ?>">Assigné à</label>
                        <select name="assignee" <?= $isOpen ? '' : 'disabled' ?>>
                            <option value="">— Non assigné</option>
                            <?php foreach ($staff as $m): ?>
                            <option value="<?= $e($m['id']) ?>" <?= $ticket['assigned_to'] === $m['id'] ? 'selected' : '' ?>><?= $e($m['pseudo']) ?><?= $m['id'] === $me['id'] ? ' (moi)' : '' ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="<?= $label ?>">Statut</label>
                        <select name="status">
                            <?php if ($ticket['status'] === 'closed'): ?>
                            <option value="closed" selected>Fermé par le joueur</option>
                            <?php endif; ?>
                            <?php foreach (TicketManager::STAFF_SETTABLE_STATUSES as $key => $lbl): ?>
                            <option value="<?= $key ?>" <?= $ticket['status'] === $key ? 'selected' : '' ?>><?= $e($lbl) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="<?= $label ?>">Priorité</label>
                        <select name="priority">
                            <option value="">Aucune</option>
                            <?php foreach (TicketManager::PRIORITIES as $key => $pr): ?>
                            <option value="<?= $key ?>" <?= $ticket['priority'] === $key ? 'selected' : '' ?>><?= $e($pr['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="<?= $label ?>">Catégorie</label>
                        <select name="category">
                            <?php foreach (TicketManager::CATEGORIES as $key => $c): ?>
                            <option value="<?= $key ?>" <?= $ticket['category'] === $key ? 'selected' : '' ?>><?= $e($c['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="origin-btn btn--full-graphic btn--primary w-full justify-center">
                        <span>Mettre à jour</span>
                        <i data-lucide="save" class="btn--icon"></i>
                    </button>
                </form>

                <!-- Fiche joueur -->
                <section class="card-glass-panel staff-panel">
                    <h5><i data-lucide="contact-round" class="w-4 h-4 text-cyan-400"></i> Fiche du joueur</h5>
                    <?php if (!$player): ?>
                    <p class="text-xs text-gray-500">Le compte de <?= $e($ticket['author_name']) ?> n'existe plus.</p>
                    <?php else:
                        $pStatus = UserManager::STATUSES[$player['status']];
                    ?>
                    <div class="flex items-center flex-wrap gap-2 mb-4">
                        <span class="text-white font-bold"><?= $e($player['pseudo']) ?></span>
                        <?php if ($player['group_name']): ?>
                        <div class="badge-glass badge-glass--<?= $e($player['group_badge']) ?>"><span class="badge-glass--text"><?= $e($player['group_name']) ?></span></div>
                        <?php endif; ?>
                        <div class="badge-glass badge-glass--<?= $pStatus['badge'] ?>"><span class="badge-glass--text"><?= $pStatus['label'] ?></span></div>
                    </div>
                    <dl class="staff-dl mb-4">
                        <dt>Minecraft</dt><dd class="font-mono text-cyan-400"><?= $player['minecraft_pseudo'] ? $e($player['minecraft_pseudo']) : '<span class="text-gray-600">—</span>' ?></dd>
                        <?php if ($canSeeUsers): ?>
                        <dt>E-mail</dt><dd><?= $e($player['email']) ?></dd>
                        <?php endif; ?>
                        <dt>Inscrit le</dt><dd><?= $date($player['created_at']) ?></dd>
                        <dt>Connexion</dt><dd><?= $date($player['last_login_at']) ?></dd>
                        <dt>Activité</dt>
                        <dd><?= $player['online'] ? '<span class="text-green-400">● En ligne</span>' : $date($player['last_seen_at']) ?></dd>
                        <dt>Tickets</dt><dd><?= $player['ticket_count'] ?></dd>
                        <dt>Sanctions</dt><dd class="<?= $player['sanction_count'] ? 'text-red-400' : '' ?>"><?= $player['sanction_count'] ?></dd>
                    </dl>

                    <?php if ($current): ?>
                    <p class="text-xs text-red-400 mb-4">
                        <?= $e(SanctionManager::TYPES[$current['type']]['label']) ?> en cours
                        <?= $current['expires_at'] ? "jusqu'au " . $date($current['expires_at']) : '(définitif)' ?> — <?= $e($current['reason']) ?>
                    </p>
                    <?php endif; ?>

                    <?php if ($player['sanctions']): ?>
                    <div class="text-xs text-gray-500 uppercase tracking-widest mb-1">Dernières sanctions</div>
                    <?php foreach ($player['sanctions'] as $sa): ?>
                    <div class="staff-event">
                        <span class="<?= $sa['active'] ? 'text-red-400' : 'text-gray-400' ?>"><?= $e(SanctionManager::TYPES[$sa['type']]['label']) ?></span>
                        <span class="text-gray-600">— <?= date('d/m/Y', strtotime($sa['created_at'])) ?><?= $sa['issued_by_name'] ? ' par ' . $e($sa['issued_by_name']) : '' ?><?= $sa['revoked_at'] ? ' (levée)' : '' ?></span>
                        <div class="text-gray-400"><?= $e($sa['reason']) ?></div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="flex flex-wrap gap-2 mt-4">
                        <a href="./dashboard?page=profil-view&id=<?= $e(urlencode($player['id'])) ?>" class="origin-btn btn--graphic btn--primary" style="font-size: 11px;">
                            <span>Profil</span><i data-lucide="eye" class="btn--icon"></i>
                        </a>
                        <?php if ($canSeeUsers): ?>
                        <a href="./dashboard?page=users&q=<?= $e(urlencode($player['pseudo'])) ?>" class="origin-btn btn--graphic btn--secondary" style="font-size: 11px;">
                            <span>Gestion</span><i data-lucide="users" class="btn--icon"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </section>

                <!-- Notes internes -->
                <section class="card-glass-panel staff-panel" style="border-top-color: #eab308;">
                    <h5><i data-lucide="sticky-note" class="w-4 h-4 text-yellow-400"></i> Notes internes <span class="text-[10px] text-gray-500 normal-case tracking-normal">— invisibles pour le joueur</span></h5>
                    <?php if (!$notes): ?>
                    <p class="text-xs text-gray-500 mb-4">Aucune note pour le moment.</p>
                    <?php endif; ?>
                    <?php foreach ($notes as $note): ?>
                    <div class="staff-note">
                        <div class="flex items-center flex-wrap gap-2 text-xs mb-1">
                            <span class="text-white font-bold"><?= $e($note['author_name']) ?></span>
                            <?php if ($note['author_group']): ?>
                            <div class="badge-glass badge-glass--<?= $e($note['author_badge'] ?? 'secondary') ?>"><span class="badge-glass--text"><?= $e($note['author_group']) ?></span></div>
                            <?php endif; ?>
                            <span class="text-gray-600"><?= $date($note['created_at']) ?></span>
                            <?php if ($note['user_id'] === $me['id']): ?>
                            <form method="POST" action="assets/handlers/ticket-action" style="margin-left: auto;" onsubmit="return confirm('Supprimer cette note ?')">
                                <?= $base('note_delete') ?>
                                <input type="hidden" name="note_id" value="<?= (int) $note['id'] ?>">
                                <button type="submit" class="text-gray-600 hover:text-red-400" title="Supprimer"><i data-lucide="trash-2" class="w-3 h-3"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-gray-300" style="white-space: pre-line; overflow-wrap: anywhere;"><?= $e($note['body']) ?></p>
                    </div>
                    <?php endforeach; ?>
                    <form method="POST" action="assets/handlers/ticket-action">
                        <?= $base('note') ?>
                        <textarea name="note" required minlength="2" maxlength="2000" rows="3" class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white text-sm focus:border-yellow-500 focus:outline-none transition-colors" placeholder="Note interne pour le staff..."></textarea>
                        <div class="flex justify-end mt-2">
                            <button type="submit" class="origin-btn btn--graphic btn--warning" style="font-size: 11px;">
                                <span>Ajouter la note</span><i data-lucide="plus" class="btn--icon"></i>
                            </button>
                        </div>
                    </form>
                </section>

                <!-- Autres tickets du joueur -->
                <section class="card-glass-panel staff-panel">
                    <h5><i data-lucide="tickets" class="w-4 h-4 text-cyan-400"></i> Autres tickets du joueur</h5>
                    <?php if (!$others): ?>
                    <p class="text-xs text-gray-500">Aucun autre ticket.</p>
                    <?php endif; ?>
                    <?php foreach ($others as $o):
                        $os = TicketManager::STAFF_STATUSES[TicketManager::staffStatus($o)];
                    ?>
                    <div class="staff-list-item">
                        <a href="./dashboard?page=ticket-staff&id=<?= (int) $o['id'] ?>" class="min-w-0" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <span class="text-gray-500"><?= TicketManager::number($o['id']) ?></span> <?= $e($o['title']) ?>
                        </a>
                        <div class="badge-glass badge-glass--<?= $os['badge'] ?>" style="flex-shrink: 0;"><span class="badge-glass--text"><?= $e($os['label']) ?></span></div>
                    </div>
                    <?php endforeach; ?>
                </section>

                <!-- Historique -->
                <section class="card-glass-panel staff-panel">
                    <h5><i data-lucide="history" class="w-4 h-4 text-cyan-400"></i> Historique</h5>
                    <?php if (!$events): ?>
                    <p class="text-xs text-gray-500">Aucun événement enregistré.</p>
                    <?php endif; ?>
                    <?php foreach ($events as $ev): ?>
                    <div class="staff-event">
                        <div class="text-gray-300"><?= $e($ev['text']) ?></div>
                        <div class="text-gray-600"><?= date('d/m/Y H:i', strtotime($ev['created_at'])) ?> — <?= $ev['actor_name'] !== null ? $e($ev['actor_name']) : 'Système' ?></div>
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

<?php if ($ticket): ?>
    const messageInput = document.getElementById('message');
    const charCount    = document.getElementById('char-count');
    function updateCount() {
        if (!messageInput || !charCount) return;
        const max = messageInput.getAttribute('maxlength');
        charCount.textContent = `${messageInput.value.length} / ${max}`;
        charCount.classList.toggle('text-red-500', messageInput.value.length >= max);
        charCount.classList.toggle('text-gray-500', messageInput.value.length < max);
    }
    if (messageInput) messageInput.addEventListener('input', updateCount);

    // Variables des réponses prédéfinies
    const TEMPLATE_VARS = <?= json_encode([
        '{joueur}' => $ticket['author_name'],
        '{staff}'  => $me['pseudo'],
        '{ticket}' => $number,
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;

    function insertTemplate(select) {
        const option = select.selectedOptions[0];
        if (!option || !option.value || !messageInput) return;
        let text = option.dataset.body;
        for (const [k, v] of Object.entries(TEMPLATE_VARS)) text = text.split(k).join(v);

        // Insère à la position du curseur (ou remplace la sélection)
        const start = messageInput.selectionStart ?? messageInput.value.length;
        const end   = messageInput.selectionEnd ?? messageInput.value.length;
        messageInput.value = messageInput.value.slice(0, start) + text + messageInput.value.slice(end);
        messageInput.focus();
        messageInput.selectionStart = messageInput.selectionEnd = start + text.length;
        select.value = '';
        updateCount();
    }
<?php endif; ?>
</script>

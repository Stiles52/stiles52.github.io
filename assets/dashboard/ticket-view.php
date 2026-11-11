<?php
$me      = $auth->user();
$isStaff = $tickets->isStaff($me, $groupManager);
$ticket  = $tickets->find((int) ($_GET['id'] ?? 0));

// Même message que le ticket n'existe pas ou ne soit pas accessible (pas d'énumération)
$canSee = $ticket
    && ($isStaff || $groupManager->userCan($me, 'view-own-tickets'))
    && $tickets->canView($ticket, $me, $isStaff);

if ($canSee) {
    $messages     = $tickets->messages((int) $ticket['id']);
    $participants = $tickets->participants((int) $ticket['id']);
    $status       = TicketManager::STATUSES[$ticket['status']];
    $category     = TicketManager::CATEGORIES[$ticket['category']] ?? ['label' => $ticket['category'], 'badge' => 'secondary', 'form' => 'generic'];
    $canEdit      = $tickets->canEdit($ticket, $me);
    $canClose     = $tickets->canClose($ticket, $me, $isStaff);
    $isOpen       = $tickets->isOpen($ticket);
    $editMode     = $canEdit && isset($_GET['edit']);
    $accept       = implode(',', array_map(fn($x) => '.' . $x, TicketManager::allowedExtensions($ticket['category'])));
    // Membre du staff consultant le ticket d'un autre joueur
    $staffView    = $isStaff && $ticket['user_id'] !== $me['id'] && !$tickets->isParticipant($ticket, $me);
    $assignee     = $ticket['assigned_to'] ? $db->fetchOne('SELECT pseudo FROM users WHERE id = ?', [$ticket['assigned_to']]) : false;
    $csrf         = Auth::generateCsrfToken();
    $flash        = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
}

$e    = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
$date = fn($v) => date('d/m/Y à H:i', strtotime($v));
?>
<main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
    <div class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 md:pb-40 fade-in-view">
        <?php if (!empty($staffView)): ?>
        <a href="./dashboard?page=manage-tickets" class="inline-flex items-center gap-2 text-gray-400 hover:text-cyan-400 transition-colors mb-6 text-sm uppercase tracking-widest">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour à la gestion des tickets
        </a>
        <?php else: ?>
        <a href="./dashboard?page=tickets" class="inline-flex items-center gap-2 text-gray-400 hover:text-cyan-400 transition-colors mb-6 text-sm uppercase tracking-widest">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour à vos tickets
        </a>
        <?php endif; ?>

<?php if (!$canSee): ?>
        <h3 class="pb-4 text-cyan-400">Ticket introuvable</h3>
        <p class="text-sm text-gray-500">Ce ticket n'existe pas ou vous n'y avez pas accès.</p>
<?php else: ?>
        <h3 class="pb-4 text-cyan-400"><?= TicketManager::number($ticket['id']) ?> — <?= $e($ticket['title']) ?></h3>
        <p class="text-gray-500 text-xs uppercase tracking-widest pb-4">
            Ouvert par <?= $e($ticket['author_name']) ?> le <?= $date($ticket['created_at']) ?>
            <?php if ($participants): ?> — Participants : <?= $e(implode(', ', $participants)) ?><?php endif; ?>
            <?php if ($isStaff): ?> — Assigné à : <?= $assignee ? $e($assignee['pseudo']) : 'personne' ?><?php endif; ?>
        </p>

        <div class="flex flex-wrap items-center gap-2 pb-8">
            <div class="badge-glass badge-glass--<?= $status['badge'] ?>"><span class="badge-glass--text"><?= $e($status['label']) ?></span></div>
            <?php if ($ticket['priority']): ?>
            <div class="badge-glass badge-glass--<?= TicketManager::PRIORITIES[$ticket['priority']]['badge'] ?>"><span class="badge-glass--text"><?= $e(TicketManager::PRIORITIES[$ticket['priority']]['label']) ?></span></div>
            <?php endif; ?>
            <div class="badge-glass badge-glass--<?= $e($category['badge']) ?>"><span class="badge-glass--text">Ticket <?= $e($category['label']) ?></span></div>
        </div>

        <?php if ($isStaff): ?>
        <a href="./dashboard?page=ticket-staff&id=<?= (int) $ticket['id'] ?>" class="origin-btn btn--graphic btn--primary mb-8">
            <span>Ouvrir la vue staff</span>
            <i data-lucide="shield" class="btn--icon"></i>
        </a>
        <?php endif; ?>

        <?php if ($flash): ?>
        <p class="text-sm mb-6 <?= $flash['type'] === 'success' ? 'text-cyan-400' : 'text-red-400' ?>"><?= $e($flash['message']) ?></p>
        <?php endif; ?>

        <?php if ($editMode):
            $first = $messages[0];
        ?>
        <!-- Édition (tant que le staff n'a pas répondu) -->
        <form method="POST" action="assets/handlers/ticket-action" class="card-glass-panel mb-8" style="padding: 1.5rem;">
            <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="ticket_id" value="<?= (int) $ticket['id'] ?>">
            <h4 class="pb-4">Modifier votre ticket</h4>
            <?php if ($category['form'] !== 'special'): ?>
            <div class="w-full mb-6">
                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Titre</label>
                <input type="text" name="subject" required minlength="5" maxlength="150" style="width: 100%;" value="<?= $e($ticket['title']) ?>">
            </div>
            <?php endif; ?>
            <div class="w-full mb-6">
                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Détails</label>
                <textarea id="message" name="message" required minlength="10" rows="8" maxlength="4000" class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white focus:border-cyan-500 focus:outline-none transition-colors"><?= $e($first['body']) ?></textarea>
                <div id="char-count" class="text-right text-[10px] text-gray-500 mt-1 font-mono">0 / 4000</div>
            </div>
            <div class="flex gap-4 flex-wrap">
                <a href="./dashboard?page=ticket-view&id=<?= (int) $ticket['id'] ?>" class="origin-btn btn--graphic btn--secondary">Annuler</a>
                <button type="submit" class="origin-btn btn--full-graphic btn--success">
                    <span>Enregistrer</span>
                    <i data-lucide="save" class="btn--icon"></i>
                </button>
            </div>
        </form>
        <?php else: ?>

        <?php if ($ticket['fields']): ?>
        <!-- Informations propres à la catégorie -->
        <h4 class="pb-4">Informations</h4>
        <section class="card-holographic mb-8">
            <div class="card-holographic--multi-body" style="display: flex; flex-direction: column; gap: 0.75rem;">
                <?php foreach ($ticket['fields'] as $f): ?>
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-widest"><?= $e($f['label']) ?></div>
                    <div class="text-sm text-white">
                        <?php if ($f['label'] === 'Vidéo'): ?>
                        <a href="<?= $e($f['value']) ?>" target="_blank" rel="noopener noreferrer"><?= $e($f['value']) ?></a>
                        <?php else: ?>
                        <?= $e($f['value']) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Conversation -->
        <div class="flex items-center justify-between flex-wrap gap-4 pb-4">
            <h4 class="m-0">Conversation</h4>
            <?php if ($canEdit): ?>
            <a href="./dashboard?page=ticket-view&id=<?= (int) $ticket['id'] ?>&edit=1" class="origin-btn btn--graphic btn--primary">
                <span>Éditer</span>
                <i data-lucide="pen" class="btn--icon"></i>
            </a>
            <?php endif; ?>
        </div>

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
            <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
            <input type="hidden" name="action" value="reply">
            <input type="hidden" name="ticket_id" value="<?= (int) $ticket['id'] ?>">

            <div class="w-full">
                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Votre réponse</label>
                <textarea id="message" name="message" required minlength="10" rows="5" maxlength="4000" class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white focus:border-cyan-500 focus:outline-none transition-colors" placeholder="Votre message... (Max 4000 caractères)"></textarea>
                <div id="char-count" class="text-right text-[10px] text-gray-500 mt-1 font-mono">0 / 4000</div>
            </div>
            <div class="w-full mt-4">
                <label class="block text-cyan-400 uppercase tracking-widest mb-2" style="font-size: 14px;">Des documents à nous fournir ?</label>
                <input type="file" name="attachments[]" multiple accept="<?= $e($accept) ?>" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-cyan-500 file:text-black hover:file:bg-cyan-400 transition-colors">
                <cite class="mt-3 mb-8">
                    Formats acceptés : <?= $e(strtoupper(implode(', ', array_diff(TicketManager::allowedExtensions($ticket['category']), ['jpeg'])))) ?><br>
                    Nombre maximum de fichier : <?= TicketManager::MAX_FILES ?> fichiers<br>
                    Taille maximum : 80Ko
                </cite>
            </div>
            <div class="flex gap-4 flex-wrap justify-end mt-6">
                <?php if ($canClose): ?>
                <button type="button" onclick="closeTicket()" class="origin-btn btn--graphic btn--danger">
                    <span><?= $staffView ? 'Marquer comme résolu' : 'Fermer le ticket' ?></span>
                    <i data-lucide="ticket-x" class="btn--icon"></i>
                </button>
                <?php endif; ?>
                <button type="submit" class="origin-btn btn--full-graphic btn--success">
                    <span>Envoyer la réponse</span>
                    <i data-lucide="send" class="btn--icon"></i>
                </button>
            </div>
        </form>

        <form id="ticket-close-form" method="POST" action="assets/handlers/ticket-action" class="hidden">
            <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
            <input type="hidden" name="action" value="close">
            <input type="hidden" name="ticket_id" value="<?= (int) $ticket['id'] ?>">
        </form>
        <?php else: ?>
        <p class="text-sm text-gray-500 text-center py-6">
            Ce ticket est <?= $ticket['status'] === 'resolved' ? 'résolu' : 'fermé' ?><?= $ticket['closed_at'] ? ' depuis le ' . $date($ticket['closed_at']) : '' ?> : il n'est plus possible d'y répondre.
        </p>
        <?php if ($isStaff): ?>
        <form method="POST" action="assets/handlers/ticket-action" class="flex justify-center">
            <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">
            <input type="hidden" name="action" value="reopen">
            <input type="hidden" name="ticket_id" value="<?= (int) $ticket['id'] ?>">
            <button type="submit" class="origin-btn btn--graphic btn--primary">
                <span>Rouvrir le ticket</span>
                <i data-lucide="rotate-ccw" class="btn--icon"></i>
            </button>
        </form>
        <?php endif; ?>
        <?php endif; ?>

        <?php endif; ?>
<?php endif; ?>
    </div>

    <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
        <p class="text-xs text-gray-600 tracking-widest text-center">© OriginRp, <?php echo date('Y'); ?>. Tous droits réservés. Reproduction strictement interdite.</p>
    </footer>
</main>

<script>
    lucide.createIcons();

    // Compteur de caractères (même comportement que la création de ticket)
    const messageInput = document.getElementById('message');
    const charCount    = document.getElementById('char-count');
    if (messageInput && charCount) {
        const update = () => {
            const max = messageInput.getAttribute('maxlength');
            charCount.textContent = `${messageInput.value.length} / ${max}`;
            charCount.classList.toggle('text-red-500', messageInput.value.length >= max);
            charCount.classList.toggle('text-gray-500', messageInput.value.length < max);
        };
        messageInput.addEventListener('input', update);
        update();
    }

    function closeTicket() {
        if (!confirm(<?= json_encode(!empty($staffView) ? 'Marquer ce ticket comme résolu ?' : "Fermer ce ticket ? Il ne sera plus possible d'y répondre.") ?>)) return;
        document.getElementById('ticket-close-form').submit();
    }
</script>

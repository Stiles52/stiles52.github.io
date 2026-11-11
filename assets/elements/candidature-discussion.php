<?php
/**
 * Discussion d'une candidature.
 * Attend : $discCandidature, $discMessages, $discReturn ('character' | 'staff'), $csrf
 */
$hd       = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
$canReply = $discCandidature['status'] !== 'withdrawn';
?>
<?php if (!$discMessages): ?>
<p class="text-xs text-gray-500 mb-6"><?= $discReturn === 'staff' ? 'Aucun message pour le moment.' : 'Aucun message pour le moment. Le staff peut vous poser des questions ici ; vous pouvez aussi lui écrire.' ?></p>
<?php endif; ?>

<?php foreach ($discMessages as $m): ?>
<section class="card-holographic mb-6" <?= $m['is_staff'] ? 'style="border-left-color: #22d3ee;"' : '' ?>>
    <div class="card-holographic--title" style="margin-bottom: 0.75rem; flex-wrap: wrap;">
        <i data-lucide="<?= $m['is_staff'] ? 'message-circle-warning' : 'message-circle' ?>" class="w-5 h-5 text-cyan-400"></i>
        <span>Message de : <?= $hd($m['author_name']) ?></span>
        <?php if ($m['is_staff']): ?>
        <div class="badge-glass badge-glass--primary"><span class="badge-glass--text">Staff</span></div>
        <?php if ($m['author_group']): ?>
        <div class="badge-glass badge-glass--<?= $hd($m['author_badge'] ?? 'secondary') ?>"><span class="badge-glass--text"><?= $hd($m['author_group']) ?></span></div>
        <?php endif; ?>
        <?php endif; ?>
        <span class="text-xs text-gray-500" style="margin-left: auto; text-transform: none;"><?= date('d/m/Y à H:i', strtotime($m['created_at'])) ?></span>
    </div>
    <p class="text-sm text-gray-300" style="white-space: pre-line; overflow-wrap: anywhere;"><?= $hd($m['body']) ?></p>
</section>
<?php endforeach; ?>

<?php if ($canReply): ?>
<form method="POST" action="assets/handlers/character-action" class="card-glass-panel" style="padding: 1.25rem;">
    <input type="hidden" name="csrf_token" value="<?= $hd($csrf) ?>">
    <input type="hidden" name="action" value="reply">
    <input type="hidden" name="return" value="<?= $discReturn ?>">
    <input type="hidden" name="candidature_id" value="<?= (int) $discCandidature['id'] ?>">
    <input type="hidden" name="character_id" value="<?= (int) $discCandidature['character_id'] ?>">
    <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2"><?= $discReturn === 'staff' ? 'Répondre au joueur' : 'Écrire au staff' ?></label>
    <textarea name="message" required minlength="2" maxlength="4000" rows="4" class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white focus:border-cyan-500 focus:outline-none transition-colors" placeholder="Votre message..."></textarea>
    <div class="flex justify-end mt-4">
        <button type="submit" class="origin-btn btn--full-graphic btn--success">
            <span>Envoyer</span>
            <i data-lucide="send" class="btn--icon"></i>
        </button>
    </div>
</form>
<?php else: ?>
<p class="text-xs text-gray-500">Cette candidature a été retirée : la discussion est close.</p>
<?php endif; ?>

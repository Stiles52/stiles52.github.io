<?php
/**
 * Lignes du tableau « tickets du joueur » (Gérer vos tickets, Dashboard).
 * Attend : $ticketRows (TicketManager::listForUser()['rows']), $me, $isStaff, $tickets, $e
 * La page doit fournir closeTicket(id, number) pour l'action « Fermer le ticket ».
 */
?>
    <?php foreach ($ticketRows as $t):
        $status   = TicketManager::STATUSES[$t['status']];
        $category = TicketManager::CATEGORIES[$t['category']] ?? ['label' => $t['category'], 'badge' => 'secondary'];
        $viewUrl  = './dashboard?page=ticket-view&id=' . (int) $t['id'];
    ?>
    <tr>
        <td><?= TicketManager::number($t['id']) ?></td>
        <td>
            <div class="badge-glass badge-glass--<?= $status['badge'] ?>">
                <span class="badge-glass--text"><?= $e($status['label']) ?></span>
            </div>
        </td>
        <td><a href="<?= $e($viewUrl) ?>"><?= $e($t['title']) ?></a></td>
        <td>
            <section class="card-holographic">
                <div class="card-holographic--title">
                    <i data-lucide="<?= $t['last_is_staff'] ? 'message-circle-warning' : 'message-circle' ?>" class="w-5 h-5 text-cyan-400"></i>
                    <span>Réponse de : <?= $e($t['last_author'] ?? '—') ?><?= $t['last_is_staff'] ? ' (Staff' . ($t['last_group'] ? ' — ' . $e($t['last_group']) : '') . ')' : '' ?></span>
                </div>
                <div>
                    <div>
                        <p>
                            <?= $e(mb_strimwidth((string) $t['last_body'], 0, 280, '…')) ?>
                        </p>
                    </div>
                </div>
            </section>
        </td>
        <td>
            <?php if ($t['priority']): ?>
            <div class="badge-glass badge-glass--<?= TicketManager::PRIORITIES[$t['priority']]['badge'] ?> mb-2">
                <span class="badge-glass--text"><?= $e(TicketManager::PRIORITIES[$t['priority']]['label']) ?></span>
            </div>
            <?php endif; ?>
            <div class="badge-glass badge-glass--<?= $e($category['badge']) ?> mb-2">
                <span class="badge-glass--text">Ticket <?= $e($category['label']) ?></span>
            </div>
        </td>
        <td>
            <button type="button" class="dropdown">
                <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                <div class="dropdown-container dropdown-container--popup hidden">
                    <a href="<?= $e($viewUrl) ?>">
                        <i data-lucide="eye" class="btn--icon"></i>
                        Voir le ticket
                    </a>
                    <?php if ($tickets->canEdit($t, $me)): ?>
                    <a href="<?= $e($viewUrl . '&edit=1') ?>">
                        <i data-lucide="pen" class="btn--icon"></i>
                        Éditer
                    </a>
                    <?php endif; ?>
                    <?php if ($tickets->canClose($t, $me, $isStaff)): ?>
                    <a href="#" onclick="closeTicket(<?= (int) $t['id'] ?>, '<?= TicketManager::number($t['id']) ?>'); return false;">
                        <i data-lucide="ticket-x" class="btn--icon"></i>
                        Fermer le ticket
                    </a>
                    <?php endif; ?>
                </div>
            </button>
        </td>
    </tr>
    <?php endforeach; ?>

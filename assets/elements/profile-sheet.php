<?php
/**
 * Fiche profil (reprise de la maquette du visualisateur des profils).
 * Attend :
 *   $profile        UserManager::profile()
 *   $history        SanctionManager::history()
 *   $ticketData     TicketManager::byAuthor()
 *   $sheetMode      'staff' (fiche staff) | 'self' (« Votre profil »)
 *   $ticketLink     fn(int $id): ?string — lien « Voir le ticket » (null = pas de lien)
 *   $profileActions fn(): void — boutons d'action dans l'en-tête (facultatif)
 *   $candidatureCount int — nombre de candidatures de personnage (facultatif)
 */
$h      = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
$fmt    = fn($v, $f = 'd/m/Y à H:i') => $v ? date($f, strtotime($v)) : '—';
$status = UserManager::STATUSES[$profile['status']];
$online = $profile['last_seen_at'] && strtotime($profile['last_seen_at']) > time() - 300;
$isStaffSheet = $sheetMode === 'staff';
$sanctionCount = count($history);

$sanctionState = function (array $s) use ($fmt, $h, $isStaffSheet): array {
    return match ($s['state']) {
        'active'  => ['danger', $s['expires_at'] ? "Active jusqu'au " . $fmt($s['expires_at'], 'd/m/Y') : 'Active (définitive)'],
        'revoked' => ['success', 'Levée' . ($isStaffSheet && $s['revoked_by_name'] ? ' par ' . $h($s['revoked_by_name']) : '')],
        'expired' => ['secondary', 'Expirée'],
        default   => ['secondary', 'Consigné'],
    };
};
?>
<style>
    .profile-avatar {
        width: 80px; height: 80px;
        background: rgba(34, 211, 238, 0.1);
        border: 2px solid rgba(34, 211, 238, 0.3);
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; font-weight: bold; color: #22d3ee;
        flex-shrink: 0;
    }
    .table-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    @media (max-width: 768px) {
        .profile-header-inner { gap: 1rem !important; }
        .profile-avatar { width: 60px; height: 60px; font-size: 1.5rem; }
        .profile-actions { width: 100%; }
        .profile-actions .origin-btn { flex: 1; justify-content: center; }
    }
    @media (max-width: 480px) {
        .profile-actions { flex-direction: column; }
        .profile-actions .origin-btn { width: 100%; }
    }
</style>

<!-- En-tête profil -->
<div class="card-glass-panel mb-6 profile-header-inner" style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
    <div class="profile-avatar"><?= $h(mb_strtoupper(mb_substr($profile['pseudo'], 0, 1))) ?></div>
    <div style="flex: 1; min-width: 200px;">
        <div class="flex items-center gap-3 mb-2 flex-wrap">
            <h4 class="text-white"><?= $h($profile['pseudo']) ?></h4>
            <?php if ($profile['group_name']): ?>
            <div class="badge-glass badge-glass--<?= $h($profile['group_badge']) ?>"><span class="badge-glass--text"><?= $h($profile['group_name']) ?></span></div>
            <?php endif; ?>
            <div class="badge-glass badge-glass--<?= $status['badge'] ?>"><span class="badge-glass--text"><?= $status['label'] ?></span></div>
            <?php if ($online): ?>
            <span class="text-[10px] text-green-500 uppercase tracking-widest flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>En ligne</span>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-2 mb-3">
            <i data-lucide="monitor" class="w-4 h-4 text-gray-500"></i>
            <span class="text-xs text-cyan-400 font-mono"><?= $profile['minecraft_pseudo'] ? $h($profile['minecraft_pseudo']) : '—' ?></span>
            <span class="text-xs text-gray-600">— Pseudo Minecraft</span>
        </div>
        <?php if (!empty($showEmail)): ?>
        <div class="flex items-center gap-2 mb-3">
            <i data-lucide="mail" class="w-4 h-4 text-gray-500"></i>
            <span class="text-xs text-gray-400"><?= $h($profile['email']) ?></span>
        </div>
        <?php endif; ?>
        <div class="flex items-center gap-2">
            <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
            <span class="text-xs text-gray-400">Inscrit le <?= $fmt($profile['created_at'], 'd/m/Y') ?> — Dernière connexion : <?= $fmt($profile['last_login_at']) ?></span>
        </div>
    </div>
    <?php if (!empty($profileActions)): ?>
    <div class="flex gap-3 flex-wrap profile-actions">
        <?php $profileActions(); ?>
    </div>
    <?php endif; ?>
</div>

<!-- Stats -->
<div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 2rem;">
    <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
        <div class="text-2xl font-bold text-cyan-400 mb-1"><?= $ticketData['total'] ?></div>
        <div class="text-xs text-gray-500 uppercase tracking-widest">Tickets créés</div>
    </div>
    <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
        <div class="text-2xl font-bold text-green-400 mb-1"><?= $ticketData['resolved'] ?></div>
        <div class="text-xs text-gray-500 uppercase tracking-widest">Tickets résolus</div>
    </div>
    <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
        <div class="text-2xl font-bold text-red-400 mb-1"><?= $sanctionCount ?></div>
        <div class="text-xs text-gray-500 uppercase tracking-widest">Sanctions</div>
    </div>
    <div class="card-glass-panel" style="min-width: 150px; flex: 1; padding: 1rem;">
        <div class="text-2xl font-bold text-purple-400 mb-1"><?= $candidatureCount ?? '—' ?></div>
        <div class="text-xs text-gray-500 uppercase tracking-widest">Candidatures</div>
    </div>
</div>

<!-- Historique de sanctions -->
<h4 class="pb-2">Historique des sanctions</h4>
<p class="text-gray-500 text-xs uppercase tracking-widest pb-4">Avertissements, suspensions et bannissements</p>
<div class="mb-8 table-scroll">
    <table>
        <thead>
            <tr>
                <td width="160px">Date</td>
                <td width="140px">Type</td>
                <td>Raison</td>
                <?php if ($isStaffSheet): ?>
                <td width="160px">Infligé par</td>
                <?php endif; ?>
                <td width="180px">Statut</td>
            </tr>
        </thead>
        <tbody>
            <?php if (!$history): ?>
            <tr>
                <td colspan="5" class="text-center text-xs text-gray-600 py-6">Aucune sanction enregistrée.</td>
            </tr>
            <?php endif; ?>
            <?php foreach ($history as $s):
                $type = SanctionManager::TYPES[$s['type']];
                [$stateBadge, $stateLabel] = $sanctionState($s);
            ?>
            <tr>
                <td class="text-xs text-gray-400"><?= $fmt($s['created_at']) ?></td>
                <td><div class="badge-glass badge-glass--<?= $s['type'] === 'ban' ? 'danger' : 'warning' ?>"><span class="badge-glass--text"><?= $h($type['label']) ?></span></div></td>
                <td class="text-sm"><?= $h($s['reason']) ?></td>
                <?php if ($isStaffSheet): ?>
                <td class="text-xs text-gray-400"><?= $s['issued_by_name'] ? $h($s['issued_by_name']) : 'Système' ?></td>
                <?php endif; ?>
                <td><div class="badge-glass badge-glass--<?= $stateBadge ?>"><span class="badge-glass--text"><?= $stateLabel ?></span></div></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Historique des tickets -->
<h4 class="pb-2">Historique des tickets</h4>
<p class="text-gray-500 text-xs uppercase tracking-widest pb-4"><?= $isStaffSheet ? 'Tickets ouverts par ce joueur' : 'Les tickets que vous avez ouverts' ?></p>
<!-- Pas de .table-scroll ici : son overflow rognerait le menu « Actions » (positionné en absolute).
     Sur écran étroit, la règle globale « table { overflow-x: auto } » de style.css prend le relais. -->
<div>
    <table>
        <thead>
            <tr>
                <td width="90px">ID#</td>
                <td width="140px">Statut</td>
                <td>Titre</td>
                <td width="150px">Catégorie</td>
                <td width="160px">Date</td>
                <td width="80px">Actions</td>
            </tr>
        </thead>
        <tbody>
            <?php if (!$ticketData['rows']): ?>
            <tr>
                <td colspan="6" class="text-center text-xs text-gray-600 py-6">Aucun ticket.</td>
            </tr>
            <?php endif; ?>
            <?php foreach ($ticketData['rows'] as $t):
                $st   = $isStaffSheet ? TicketManager::STAFF_STATUSES[TicketManager::staffStatus($t)] : TicketManager::STATUSES[$t['status']];
                $cat  = TicketManager::CATEGORIES[$t['category']] ?? ['label' => $t['category'], 'badge' => 'secondary'];
                $link = $ticketLink((int) $t['id']);
            ?>
            <tr>
                <td class="text-xs text-gray-500"><?= TicketManager::number($t['id']) ?></td>
                <td><div class="badge-glass badge-glass--<?= $st['badge'] ?>"><span class="badge-glass--text"><?= $h($st['label']) ?></span></div></td>
                <td class="text-sm"><?= $link ? '<a href="' . $h($link) . '">' . $h($t['title']) . '</a>' : $h($t['title']) ?></td>
                <td><div class="badge-glass badge-glass--<?= $h($cat['badge']) ?>"><span class="badge-glass--text"><?= $h($cat['label']) ?></span></div></td>
                <td class="text-xs text-gray-400"><?= $fmt($t['created_at'], 'd/m/Y') ?></td>
                <td>
                    <?php if ($link): ?>
                    <button type="button" class="dropdown">
                        <i data-lucide="ellipsis-vertical" class="w-6 h-6 text-white"></i>
                        <div class="dropdown-container dropdown-container--popup hidden">
                            <a href="<?= $h($link) ?>">
                                <i data-lucide="eye" class="btn--icon"></i>
                                Voir le ticket
                            </a>
                        </div>
                    </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

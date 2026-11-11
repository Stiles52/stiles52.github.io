<?php
/**
 * Pagination du dashboard.
 * Attend : $current (page courante), $last (nombre de pages), $pageUrl (fn(int $p): string)
 */
if ($last <= 1) {
    return;
}
// Pages affichées : 1, la dernière et la fenêtre autour de la page courante
$shown = array_unique(array_filter([1, $current - 1, $current, $current + 1, $last], fn($p) => $p >= 1 && $p <= $last));
sort($shown);
$h = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
?>
<div class="pagination">
    <a href="<?= $current > 1 ? $h($pageUrl($current - 1)) : '#' ?>" class="origin-btn btn--graphic btn--<?= $current > 1 ? 'primary' : 'secondary' ?>">
        <i data-lucide="arrow-big-left" class="btn--icon"></i>
    </a>
    <?php $prev = 0; foreach ($shown as $p): ?>
        <?php if ($p - $prev > 1): ?>
        <span class="pagination--ellipsis origin-btn btn--full-graphic btn--secondary"><span>...</span></span>
        <?php endif; ?>
        <a href="<?= $h($pageUrl($p)) ?>" class="pagination--page <?= $p === $current ? 'pagination--current' : '' ?> origin-btn btn--full-graphic btn--<?= $p === $current ? 'primary' : 'secondary' ?>"><span><?= $p ?></span></a>
    <?php $prev = $p; endforeach; ?>
    <a href="<?= $current < $last ? $h($pageUrl($current + 1)) : '#' ?>" class="origin-btn btn--graphic btn--<?= $current < $last ? 'primary' : 'secondary' ?>">
        <i data-lucide="arrow-big-right" class="btn--icon"></i>
    </a>
</div>

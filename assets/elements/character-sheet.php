<?php
/**
 * Affichage d'une fiche de personnage.
 * Attend : $sheetData (fiche décodée), $compareSheet (fiche de référence ou null) — les champs différents sont surlignés.
 */
$hs        = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
$longKeys  = ['appearance', 'personality', 'story', 'description'];
$changedOf = fn(string $k) => $compareSheet !== null && ($compareSheet[$k] ?? null) !== ($sheetData[$k] ?? null);
?>
<section class="card-holographic mb-8">
    <dl style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem 1.5rem;">
        <?php foreach (CharacterManager::SHEET_FIELDS as $key => $lbl):
            if (in_array($key, $longKeys, true) || ($key === 'hybrid_detail' && empty($sheetData['hybrid']))) {
                continue;
            }
            $changed = $changedOf($key);
        ?>
        <div <?= $changed ? 'style="border-left: 2px solid #eab308; padding-left: 0.5rem;"' : '' ?>>
            <dt class="text-[10px] text-gray-500 uppercase tracking-widest"><?= $lbl ?><?= $changed ? ' <span class="text-yellow-400">— modifié</span>' : '' ?></dt>
            <dd class="text-sm text-white" style="overflow-wrap: anywhere;"><?= $hs(CharacterManager::displayValue($key, $sheetData[$key] ?? null)) ?></dd>
            <?php if ($changed): ?>
            <dd class="text-xs text-gray-600 line-through" style="overflow-wrap: anywhere;"><?= $hs(CharacterManager::displayValue($key, $compareSheet[$key] ?? null)) ?></dd>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </dl>
</section>

<?php foreach ($longKeys as $key):
    if (($sheetData[$key] ?? '') === '' && !$changedOf($key)) {
        continue;
    }
    $changed = $changedOf($key);
?>
<h5 class="pb-2"><?= CharacterManager::SHEET_FIELDS[$key] ?><?= $changed ? ' <span class="text-xs text-yellow-400 normal-case tracking-normal">— modifié</span>' : '' ?></h5>
<section class="card-holographic mb-8" <?= $changed ? 'style="border-left-color: #eab308;"' : '' ?>>
    <p class="text-sm text-gray-300" style="white-space: pre-line; overflow-wrap: anywhere;"><?= $hs($sheetData[$key] ?? '') ?></p>
    <?php if ($changed && ($compareSheet[$key] ?? '') !== ''): ?>
    <details class="mt-4">
        <summary class="text-xs text-gray-500 cursor-pointer">Voir la version précédente</summary>
        <p class="text-xs text-gray-500 mt-2" style="white-space: pre-line; overflow-wrap: anywhere;"><?= $hs($compareSheet[$key]) ?></p>
    </details>
    <?php endif; ?>
</section>
<?php endforeach; ?>

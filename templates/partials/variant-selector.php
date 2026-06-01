<?php
// Expects: $product (with variant_axes and variants)
// Builds grouped selectors for each axis (e.g., color, size)
if (empty($product['variant_axes'])) {
    // Single variant — no selector needed, emit hidden input
    $only = $product['variants'][0];
    ?>
    <input type="hidden" id="selected-sku" value="<?= htmlspecialchars($only['sku']) ?>">
    <?php
    return;
}

// Build unique values per axis
$axes_values = [];
foreach ($product['variant_axes'] as $axis) {
    $axes_values[$axis] = [];
    foreach ($product['variants'] as $v) {
        $val = $v['attributes'][$axis] ?? null;
        if ($val !== null && !in_array($val, $axes_values[$axis], true)) {
            $axes_values[$axis][] = $val;
        }
    }
}
?>
<div id="variant-selector" class="space-y-4"
     data-variants='<?= htmlspecialchars(json_encode($product['variants']), ENT_QUOTES) ?>'
     data-axes='<?= htmlspecialchars(json_encode($product['variant_axes']), ENT_QUOTES) ?>'>
    <?php foreach ($axes_values as $axis => $values): ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2 capitalize"><?= htmlspecialchars($axis) ?></label>
            <div class="flex flex-wrap gap-2" data-axis="<?= htmlspecialchars($axis) ?>">
                <?php foreach ($values as $i => $val): ?>
                    <button type="button"
                            class="variant-btn px-4 py-2 border rounded-md text-sm font-medium
                                   <?= $i === 0 ? 'border-brand-green bg-green-50 text-brand-green' : 'border-gray-300 text-gray-700 hover:border-brand-green' ?>"
                            data-axis="<?= htmlspecialchars($axis) ?>"
                            data-value="<?= htmlspecialchars($val) ?>">
                        <?= htmlspecialchars($val) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <input type="hidden" id="selected-sku" value="">
</div>

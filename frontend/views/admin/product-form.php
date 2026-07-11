<?php
$productId = $productId ?? null;
$product = null;
if (!empty($productId)) {
    $res = api_get("/products/$productId");
    $product = $res['data'] ?? null;
}

$cats = api_get('/categories');
$categories = $cats['data'] ?? [];
$tags = api_get('/tags');
$allTags = $tags['data'] ?? [];

$isEdit = $product !== null;
?>

<div class="section" style="max-width: 800px; margin: 0 auto;">
    <a href="/admin/products" class="btn btn-small" style="margin-bottom: 30px;">&larr; Back to Products</a>
    
    <h1 style="font-family: var(--font-serif); font-size: 2.5rem; font-weight: 500; margin-bottom: 40px; border-bottom: 1px solid var(--color-gray-light); padding-bottom: 15px;"><?= $isEdit ? 'Edit Product' : 'New Product' ?></h1>

    <form method="POST" class="admin-form" style="border: none; padding: 0; box-shadow: none; display: flex; flex-direction: column; gap: 24px;">
        <div class="form-row" style="display: flex; gap: 20px; flex-wrap: wrap;">
            <label style="flex: 1; display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
                Name
                <input type="text" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
            </label>
            <label style="flex: 1; display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
                Brand
                <input type="text" name="brand" value="<?= htmlspecialchars($product['brand'] ?? '') ?>" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
            </label>
        </div>

        <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
            Description
            <textarea name="description" rows="4" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; font-family: var(--font-sans); resize: vertical;"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </label>

        <div class="form-row" style="display: flex; gap: 20px; flex-wrap: wrap;">
            <label style="flex: 1; display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
                Base Price ($)
                <input type="number" name="base_price" step="0.01" value="<?= $product['base_price'] ?? '' ?>" required style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
            </label>
            <label style="flex: 1; display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
                Discount Percentage (%)
                <input type="number" name="discount_percent" min="0" max="100" value="<?= $product['discount_percent'] ?? 0 ?>" required style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
            </label>
            <label style="flex: 1; display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
                Gender Section
                <select name="gender" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; background: #fff; color: var(--color-dark);">
                    <option value="unisex" <?= ($product['gender'] ?? '') === 'unisex' ? 'selected' : '' ?>>Unisex</option>
                    <option value="men" <?= ($product['gender'] ?? '') === 'men' ? 'selected' : '' ?>>Men</option>
                    <option value="women" <?= ($product['gender'] ?? '') === 'women' ? 'selected' : '' ?>>Women</option>
                    <option value="kids" <?= ($product['gender'] ?? '') === 'kids' ? 'selected' : '' ?>>Kids</option>
                </select>
            </label>
        </div>

        <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
            Product Gallery Image URLs (Comma-separated)
            <textarea name="images" rows="2" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; font-family: var(--font-sans); resize: vertical;" placeholder="e.g. /assets/images/prod1_1.png, /assets/images/prod1_2.png"><?= htmlspecialchars($product['images'] ?? '') ?></textarea>
        </label>

        <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
            Categories
            <select name="category_ids[]" multiple style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; background: #fff; height: 120px;">
                <?php foreach ($categories as $cat): ?>
                    <?php
                    $selected = '';
                    if ($isEdit && !empty($product['categories'])) {
                        $ids = array_column($product['categories'], 'id');
                        $selected = in_array($cat['id'], $ids) ? 'selected' : '';
                    }
                    ?>
                    <option value="<?= $cat['id'] ?>" <?= $selected ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span style="font-size: 11px; color: var(--color-gray); margin-top: 4px;">Hold Ctrl (or Cmd) to select multiple categories.</span>
        </label>

        <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
            Tags
            <select name="tag_ids[]" multiple style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; background: #fff; height: 100px;">
                <?php foreach ($allTags as $tag): ?>
                    <?php
                    $selected = '';
                    if ($isEdit && !empty($product['tags'])) {
                        $ids = array_column($product['tags'], 'id');
                        $selected = in_array($tag['id'], $ids) ? 'selected' : '';
                    }
                    ?>
                    <option value="<?= $tag['id'] ?>" <?= $selected ?>>
                        <?= htmlspecialchars($tag['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span style="font-size: 11px; color: var(--color-gray); margin-top: 4px;">Hold Ctrl (or Cmd) to select multiple tags.</span>
        </label>

        <h3 style="font-family: var(--font-serif); font-size: 1.4rem; font-weight: 500; margin-top: 30px; margin-bottom: 12px; border-bottom: 1px solid var(--color-gray-light); padding-bottom: 8px;">Product Variants</h3>
        
        <table class="admin-table" id="variants-table" style="box-shadow: var(--shadow-soft); border-radius: var(--border-radius); border: 1px solid var(--color-gray-light); overflow: hidden; width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <thead>
                <tr style="background: var(--color-gray-bg); border-bottom: 1px solid var(--color-gray-light);">
                    <th style="padding: 12px 15px; font-size: 11px; text-transform: uppercase; color: var(--color-gray-dark); font-weight: 600;">SKU*</th>
                    <th style="padding: 12px 15px; font-size: 11px; text-transform: uppercase; color: var(--color-gray-dark); font-weight: 600;">Price*</th>
                    <th style="padding: 12px 15px; font-size: 11px; text-transform: uppercase; color: var(--color-gray-dark); font-weight: 600;">Stock Qty*</th>
                    <th style="padding: 12px 15px; font-size: 11px; text-transform: uppercase; color: var(--color-gray-dark); font-weight: 600;">Size</th>
                    <th style="padding: 12px 15px; font-size: 11px; text-transform: uppercase; color: var(--color-gray-dark); font-weight: 600;">Color</th>
                    <th style="padding: 12px 15px; font-size: 11px; text-transform: uppercase; color: var(--color-gray-dark); font-weight: 600; width: 80px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $variants = $product['variants'] ?? [];
                $index = 0;
                foreach ($variants as $v):
                    $attrs = json_decode($v['attributes'] ?? '{}', true);
                    $sizeVal = $attrs['size'] ?? '';
                    if (is_array($sizeVal)) {
                        $sizeVal = implode(', ', $sizeVal);
                    }
                    $colorVal = $attrs['color'] ?? '';
                    if (is_array($colorVal)) {
                        $colorVal = implode(', ', $colorVal);
                    }
                ?>
                    <tr style="border-bottom: 1px solid var(--color-gray-light);">
                         <td style="padding: 10px 12px;">
                             <input type="hidden" name="variants[<?= $index ?>][id]" value="<?= $v['id'] ?>">
                             <input type="text" name="variants[<?= $index ?>][sku]" value="<?= htmlspecialchars($v['sku']) ?>" required style="width: 100%; padding: 8px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
                         </td>
                         <td style="padding: 10px 12px;">
                             <input type="number" name="variants[<?= $index ?>][price]" step="0.01" value="<?= htmlspecialchars($v['price']) ?>" required style="width: 100%; padding: 8px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
                         </td>
                         <td style="padding: 10px 12px;">
                             <input type="number" name="variants[<?= $index ?>][stock_qty]" value="<?= htmlspecialchars($v['stock_qty']) ?>" required style="width: 100%; padding: 8px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
                         </td>
                         <td style="padding: 10px 12px;">
                             <input type="text" name="variants[<?= $index ?>][attributes][size]" value="<?= htmlspecialchars($sizeVal) ?>" placeholder="e.g. M" style="width: 100%; padding: 8px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
                         </td>
                         <td style="padding: 10px 12px;">
                             <input type="text" name="variants[<?= $index ?>][attributes][color]" value="<?= htmlspecialchars($colorVal) ?>" placeholder="e.g. Black" style="width: 100%; padding: 8px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
                         </td>
                         <td style="padding: 10px 12px; text-align: center;">
                            <button type="button" class="btn btn-small btn-danger" onclick="removeVariantRow(this)" style="background: none; border: none; color: var(--color-error); text-transform: uppercase; font-size: 10px; font-weight: 600; cursor: pointer; padding: 0;">Remove</button>
                        </td>
                    </tr>
                <?php
                    $index++;
                endforeach;
                ?>
            </tbody>
        </table>
        
        <div style="margin-bottom: 40px;">
            <button type="button" class="btn btn-small" onclick="addVariantRow()">+ Add Variant Row</button>
        </div>

        <div style="display: flex; gap: 12px; border-top: 1px solid var(--color-gray-light); padding-top: 30px;">
            <button type="submit" class="btn btn-primary btn-large" style="flex: 1; justify-content: center;"><?= $isEdit ? 'Update' : 'Create' ?> Product</button>
            <a href="/admin/products" class="btn btn-large" style="flex: 1; justify-content: center; background: transparent; border: 1px solid var(--color-gray-light); color: var(--color-gray-dark);">Cancel</a>
        </div>
    </form>
</div>

<script>
let variantIndex = <?= $index ?>;

function addVariantRow() {
    const tbody = document.querySelector('#variants-table tbody');
    const tr = document.createElement('tr');
    tr.style.borderBottom = '1px solid var(--color-gray-light)';
    tr.innerHTML = `
        <td style="padding: 10px 12px;">
            <input type="text" name="variants[\${variantIndex}][sku]" placeholder="SKU-NEW" required style="width: 100%; padding: 8px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
        </td>
        <td style="padding: 10px 12px;">
            <input type="number" name="variants[\${variantIndex}][price]" step="0.01" placeholder="0.00" required style="width: 100%; padding: 8px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
        </td>
        <td style="padding: 10px 12px;">
            <input type="number" name="variants[\${variantIndex}][stock_qty]" value="0" required style="width: 100%; padding: 8px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
        </td>
        <td style="padding: 10px 12px;">
            <input type="text" name="variants[\${variantIndex}][attributes][size]" placeholder="e.g. M" style="width: 100%; padding: 8px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
        </td>
        <td style="padding: 10px 12px;">
            <input type="text" name="variants[\${variantIndex}][attributes][color]" placeholder="e.g. Black" style="width: 100%; padding: 8px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
        </td>
        <td style="padding: 10px 12px; text-align: center;">
            <button type="button" class="btn btn-small btn-danger" onclick="removeVariantRow(this)" style="background: none; border: none; color: var(--color-error); text-transform: uppercase; font-size: 10px; font-weight: 600; cursor: pointer; padding: 0;">Remove</button>
        </td>
    `;
    tbody.appendChild(tr);
    variantIndex++;
}

function removeVariantRow(button) {
    button.closest('tr').remove();
}
</script>

<?php
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

<div class="section">
    <h1><?= $isEdit ? 'Edit Product' : 'New Product' ?></h1>

    <form method="POST" class="admin-form">
        <div class="form-row">
            <label>
                Name
                <input type="text" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
            </label>
            <label>
                Brand
                <input type="text" name="brand" value="<?= htmlspecialchars($product['brand'] ?? '') ?>">
            </label>
        </div>

        <label>
            Description
            <textarea name="description" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </label>

        <div class="form-row">
            <label>
                Base Price
                <input type="number" name="base_price" step="0.01" value="<?= $product['base_price'] ?? '' ?>" required>
            </label>
        </div>

        <label>
            Categories
            <select name="category_ids[]" multiple>
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
        </label>

        <label>
            Tags
            <select name="tag_ids[]" multiple>
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
        </label>

        <button type="submit" class="btn btn-primary">
            <?= $isEdit ? 'Update' : 'Create' ?> Product
        </button>
        <a href="/admin/products" class="btn">Cancel</a>
    </form>
</div>

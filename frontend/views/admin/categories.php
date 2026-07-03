<?php
$cats = api_get('/categories');
$categories = $cats['data'] ?? [];
$tree = api_get('/categories/tree');
$categoryTree = $tree['data'] ?? [];
?>

<div class="section">
    <h1>Categories</h1>

    <h3>Add Category</h3>
    <form method="POST" class="admin-form">
        <label>
            Name
            <input type="text" name="name" required>
        </label>
        <label>
            Parent Category
            <select name="parent_id">
                <option value="">None (top level)</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>

    <h3>Current Categories</h3>
    <?php if (empty($categoryTree)): ?>
        <p>No categories.</p>
    <?php else: ?>
        <ul class="category-tree">
            <?php foreach ($categoryTree as $cat): ?>
                <li><strong><?= htmlspecialchars($cat['name']) ?></strong></li>
                <?php if (!empty($cat['children'])): ?>
                    <ul>
                        <?php foreach ($cat['children'] as $child): ?>
                            <li><?= htmlspecialchars($child['name']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

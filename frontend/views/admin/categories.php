<?php
$cats = api_get('/categories');
$categories = $cats['data'] ?? [];
$tree = api_get('/categories/tree');
$categoryTree = $tree['data'] ?? [];

$editId = isset($_GET['edit_cat']) ? (int) $_GET['edit_cat'] : null;
$editCategory = null;
if ($editId) {
    $editRes = api_get("/categories/$editId");
    $editCategory = $editRes['data'] ?? null;
}
$isEdit = $editCategory !== null;
?>

<div class="section">
    <a href="/admin" class="btn btn-small" style="margin-bottom: 30px;">&larr; Back to Dashboard</a>
    
    <h1 style="font-family: var(--font-serif); font-size: 2.5rem; font-weight: 500; margin-bottom: 40px;">Manage Categories</h1>

    <div class="product-detail" style="gap: 60px; grid-template-columns: 1fr 1.2fr;">
        <!-- Category Form -->
        <div style="background: var(--color-gray-bg); padding: 40px; border-radius: var(--border-radius); border: 1px solid var(--color-gray-light); height: fit-content;">
            <h3 style="font-family: var(--font-serif); font-size: 1.4rem; font-weight: 500; margin-bottom: 20px; border-bottom: 1px solid var(--color-gray-light); padding-bottom: 12px;">
                <?= $isEdit ? 'Edit Category' : 'Add Category' ?>
            </h3>
            <form method="POST" action="/admin/categories<?= $isEdit ? '?edit_cat=' . $editId : '' ?>" class="admin-form" style="border: none; padding: 0; box-shadow: none; background: transparent; display: flex; flex-direction: column; gap: 20px;">
                <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
                    Name
                    <input type="text" name="name" value="<?= htmlspecialchars($editCategory['name'] ?? '') ?>" required style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
                </label>
                <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
                    Parent Category
                    <select name="parent_id" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; background: #fff;">
                        <option value="">None (top level)</option>
                        <?php foreach ($categories as $cat): ?>
                            <?php 
                            // Skip the category itself to prevent parenting cycles
                            if ($isEdit && (int)$cat['id'] === $editId) continue;
                            
                            $selected = '';
                            if ($isEdit && (int)($editCategory['parent_id'] ?? 0) === (int)$cat['id']) {
                                $selected = 'selected';
                            }
                            ?>
                            <option value="<?= $cat['id'] ?>" <?= $selected ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;"><?= $isEdit ? 'Update Category' : 'Create Category' ?></button>
                    <?php if ($isEdit): ?>
                        <a href="/admin/categories" class="btn" style="flex: 1; justify-content: center; background: transparent; border: 1px solid var(--color-gray-light); color: var(--color-gray-dark);">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Current Category Tree -->
        <div style="padding: 0;">
            <h3 style="font-family: var(--font-serif); font-size: 1.4rem; font-weight: 500; margin-bottom: 20px; border-bottom: 1px solid var(--color-gray-light); padding-bottom: 12px;">Structure Tree</h3>
            <?php if (empty($categoryTree)): ?>
                <p style="color: var(--color-gray);">No categories created yet.</p>
            <?php else: ?>
                <ul class="category-tree" style="list-style: none; background: #fff; padding: 24px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); box-shadow: var(--shadow-soft);">
                    <?php foreach ($categoryTree as $cat): ?>
                        <li style="padding: 8px 0; font-weight: 600; font-size: 15px; color: var(--color-black); display: flex; flex-direction: column;">
                            <span style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f9f9f9; padding-bottom: 4px;">
                                <strong><?= htmlspecialchars($cat['name']) ?></strong>
                                <span style="font-weight: normal; font-size: 11px;">
                                    <a href="/admin/categories?edit_cat=<?= $cat['id'] ?>" style="color: var(--color-gray-dark); text-decoration: underline; margin-right: 8px;">Edit</a>
                                    <a href="/admin/categories?delete_cat=<?= $cat['id'] ?>" onclick="return confirm('Delete category (and nested subcategories) &quot;<?= htmlspecialchars($cat['name']) ?>&quot;?')" style="color: var(--color-error); text-decoration: underline;">Delete</a>
                                </span>
                            </span>
                            <?php if (!empty($cat['children'])): ?>
                                <ul style="list-style: none; padding-left: 20px; margin-top: 8px;">
                                    <?php foreach ($cat['children'] as $child): ?>
                                        <li style="padding: 6px 0; font-weight: 500; font-size: 14px; color: var(--color-gray-dark); display: flex; justify-content: space-between; align-items: center;">
                                            <span><?= htmlspecialchars($child['name']) ?></span>
                                            <span style="font-weight: normal; font-size: 11px;">
                                                <a href="/admin/categories?edit_cat=<?= $child['id'] ?>" style="color: var(--color-gray-dark); text-decoration: underline; margin-right: 8px;">Edit</a>
                                                <a href="/admin/categories?delete_cat=<?= $child['id'] ?>" onclick="return confirm('Delete category &quot;<?= htmlspecialchars($child['name']) ?>&quot;?')" style="color: var(--color-error); text-decoration: underline;">Delete</a>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

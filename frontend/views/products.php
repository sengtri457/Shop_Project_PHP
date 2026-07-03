<?php
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 12;
$sortBy = $_GET['sort_by'] ?? 'created_at';
$sortOrder = $_GET['sort_order'] ?? 'desc';
$categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;
$search = $_GET['search'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';

$query = http_build_query(array_filter([
    'page' => $page,
    'limit' => $limit,
    'sort_by' => $sortBy,
    'sort_order' => $sortOrder,
    'category_id' => $categoryId,
    'search' => $search ?: null,
    'min_price' => $minPrice ?: null,
    'max_price' => $maxPrice ?: null,
]));

$result = api_get("/products?$query");
$products = $result['data']['data'] ?? [];
$meta = $result['data']['meta'] ?? [];
$cats = api_get('/categories');
$categories = $cats['data'] ?? [];
?>

<div class="section">
    <h1>Products</h1>

    <form class="filters" method="GET">
        <input type="search" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">

        <select name="category_id">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $categoryId === $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="number" name="min_price" placeholder="Min price" step="0.01" value="<?= htmlspecialchars($minPrice) ?>">
        <input type="number" name="max_price" placeholder="Max price" step="0.01" value="<?= htmlspecialchars($maxPrice) ?>">

        <select name="sort_by">
            <option value="created_at" <?= $sortBy === 'created_at' ? 'selected' : '' ?>>Newest</option>
            <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>Name</option>
            <option value="base_price" <?= $sortBy === 'base_price' ? 'selected' : '' ?>>Price</option>
        </select>

        <select name="sort_order">
            <option value="desc" <?= $sortOrder === 'desc' ? 'selected' : '' ?>>Desc</option>
            <option value="asc" <?= $sortOrder === 'asc' ? 'selected' : '' ?>>Asc</option>
        </select>

        <button type="submit" class="btn">Filter</button>
    </form>

    <?php if (empty($products)): ?>
        <p>No products found.</p>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <a href="/products/<?= $product['id'] ?>" class="product-card">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="price">$<?= number_format($product['base_price'], 2) ?></p>
                    <?php if (!empty($product['brand'])): ?>
                        <p class="brand"><?= htmlspecialchars($product['brand']) ?></p>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (($meta['pages'] ?? 0) > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $meta['pages']; $i++): ?>
                    <a href="/products?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="page-link <?= $i === $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

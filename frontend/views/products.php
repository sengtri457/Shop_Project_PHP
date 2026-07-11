<?php
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 12;
$sortBy = $_GET['sort_by'] ?? 'created_at';
$sortOrder = $_GET['sort_order'] ?? 'desc';
$categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;
$search = $_GET['search'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';

$gender = $_GET['gender'] ?? '';

$query = http_build_query(array_filter([
    'page' => $page,
    'limit' => $limit,
    'sort_by' => $sortBy,
    'sort_order' => $sortOrder,
    'category_id' => $categoryId,
    'search' => $search ?: null,
    'min_price' => $minPrice ?: null,
    'max_price' => $maxPrice ?: null,
    'gender' => $gender ?: null,
]));

$result = api_get("/products?$query");
$products = $result['data']['data'] ?? [];
$meta = $result['data']['meta'] ?? [];
$cats = api_get('/categories');
$categories = $cats['data'] ?? [];
?>

<div class="section">
    <h1 style="margin-bottom: 30px; font-family: var(--font-serif); font-weight: 500;">All Products</h1>

    <form class="filters" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; background: #fafafa; border: 1px solid var(--color-gray-light); padding: 16px; border-radius: var(--border-radius); margin-bottom: 40px; align-items: center;">
        <input type="search" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>" style="flex: 1; min-width: 200px; padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">

        <select name="gender" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; background: #fff; color: var(--color-dark);">
            <option value="">All Sections</option>
            <option value="unisex" <?= $gender === 'unisex' ? 'selected' : '' ?>>Unisex</option>
            <option value="men" <?= $gender === 'men' ? 'selected' : '' ?>>Men</option>
            <option value="women" <?= $gender === 'women' ? 'selected' : '' ?>>Women</option>
            <option value="kids" <?= $gender === 'kids' ? 'selected' : '' ?>>Kids</option>
        </select>

        <select name="category_id" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; background: #fff; color: var(--color-dark);">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $categoryId === $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="number" name="min_price" placeholder="Min price" step="0.01" value="<?= htmlspecialchars($minPrice) ?>" style="width: 100px; padding: 10px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
        <input type="number" name="max_price" placeholder="Max price" step="0.01" value="<?= htmlspecialchars($maxPrice) ?>" style="width: 100px; padding: 10px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">

        <select name="sort_by" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; background: #fff;">
            <option value="created_at" <?= $sortBy === 'created_at' ? 'selected' : '' ?>>Newest</option>
            <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>Name</option>
            <option value="base_price" <?= $sortBy === 'base_price' ? 'selected' : '' ?>>Price</option>
        </select>

        <select name="sort_order" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; background: #fff;">
            <option value="desc" <?= $sortOrder === 'desc' ? 'selected' : '' ?>>Desc</option>
            <option value="asc" <?= $sortOrder === 'asc' ? 'selected' : '' ?>>Asc</option>
        </select>

        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Filter</button>
    </form>

    <?php if (empty($products)): ?>
        <p style="color: var(--color-gray); text-align: center; margin: 40px 0;">No products found matching your search.</p>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $product):
                $prodImages = [];
                if (!empty($product['images'])) {
                    $prodImages = array_filter(array_map('trim', explode(',', $product['images'])));
                }
                if (empty($prodImages) && !empty($product['variants'])) {
                    foreach ($product['variants'] as $v) {
                        if (!empty($v['image_url'])) {
                            $prodImages[] = $v['image_url'];
                            break;
                        }
                    }
                }
                $mainImg = !empty($prodImages) ? $prodImages[0] : '/assets/images/hero_banner.png';
                $discountPercent = (int) ($product['discount_percent'] ?? 0);
            ?>
                <a href="/products/<?= $product['id'] ?>" class="product-card">
                    <img src="<?= htmlspecialchars($mainImg) ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>" 
                         style="width: 100%; height: 240px; object-fit: cover; border-radius: var(--border-radius); margin-bottom: 15px;">
                    <?php if (!empty($product['brand'])): ?>
                        <span class="brand" style="margin-bottom: 4px; font-size: 11px; text-transform: uppercase; color: var(--color-gray);"><?= htmlspecialchars($product['brand']) ?></span>
                    <?php endif; ?>
                    <h3 style="font-size: 1.1rem; margin-bottom: 8px;"><?= htmlspecialchars($product['name']) ?></h3>
                    <?php if ($discountPercent > 0): ?>
                        <p class="price" style="display: flex; gap: 8px; align-items: center; margin-top: auto;">
                            <span style="color: var(--color-error); font-weight: bold;">
                                $<?= number_format($product['base_price'] * (1 - $discountPercent / 100), 2) ?>
                            </span>
                            <span style="font-size: 11px; font-weight: 600; color: var(--color-error); background: #fee8e6; padding: 1px 4px; border-radius: 3px;">
                                -<?= $discountPercent ?>%
                            </span>
                            <span style="color: var(--color-gray); text-decoration: line-through; font-size: 12px; font-weight: normal;">
                                $<?= number_format($product['base_price'], 2) ?>
                            </span>
                        </p>
                    <?php else: ?>
                        <p class="price" style="margin-top: auto;">$<?= number_format($product['base_price'], 2) ?></p>
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

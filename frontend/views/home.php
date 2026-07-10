<?php
function getCategoryStyle(int $id): string {
    $colors = [
        1 => 'background-color: #e5e0db;', // Warm beige linen
        2 => 'background-color: #d2d7df;', // Pale slate cotton
        3 => 'background-color: #dedcd7;', // Off-white silk
        4 => 'background-color: #cbd5e1;', // Cool slate
        5 => 'background-color: #e2e8f0;', // Cloud gray
    ];
    return $colors[$id] ?? 'background-color: #f1f5f9;';
}
?>

<div class="hero">
    <div class="hero-content">
        <h1>
            <?php if (is_logged_in()): ?>
                Curated Style for <?= htmlspecialchars($_SESSION['customer']['name'] ?? '') ?>
            <?php else: ?>
                Minimalism in Everyday Wear
            <?php endif; ?>
        </h1>
        <p>A collection of premium, high-quality linen and cotton essentials designed for modern comfort and timeless style.</p>
        <a href="/products" class="btn btn-primary btn-large">Shop the Collection</a>
    </div>
</div>

<div class="section">
    <h2>Shop by Category</h2>
    <?php
    $result = api_get('/categories');
    $categories = $result['data'] ?? [];
    ?>
    <?php if (empty($categories)): ?>
        <p style="color: var(--color-gray);">No categories yet.</p>
    <?php else: ?>
        <div class="category-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="/products?category_id=<?= $cat['id'] ?>" class="category-card" style="<?= getCategoryStyle($cat['id']) ?>">
                    <h3><?= htmlspecialchars($cat['name']) ?></h3>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="section">
    <h2>New Arrivals</h2>
    <?php
    $result = api_get('/products?limit=8&sort_by=created_at&sort_order=desc');
    $products = $result['data']['data'] ?? [];
    ?>
    <?php if (empty($products)): ?>
        <p style="color: var(--color-gray);">No products yet.</p>
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
    <?php endif; ?>
</div>

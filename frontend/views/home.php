<div class="hero">
    <h1>
        <?php if (is_logged_in()): ?>
            Welcome back, <?= htmlspecialchars($_SESSION['customer']['name'] ?? '') ?>!
        <?php else: ?>
            Welcome to the Shop
        <?php endif; ?>
    </h1>
    <p>Browse our collection of products</p>
    <a href="/products" class="btn btn-primary">Shop Now</a>
</div>

<div class="section">
    <h2>Categories</h2>
    <?php
    $result = api_get('/categories');
    $categories = $result['data'] ?? [];
    ?>
    <?php if (empty($categories)): ?>
        <p>No categories yet.</p>
    <?php else: ?>
        <div class="category-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="/products?category_id=<?= $cat['id'] ?>" class="category-card">
                    <h3><?= htmlspecialchars($cat['name']) ?></h3>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="section">
    <h2>Latest Products</h2>
    <?php
    $result = api_get('/products?limit=8&sort_by=created_at&sort_order=desc');
    $products = $result['data']['data'] ?? [];
    ?>
    <?php if (empty($products)): ?>
        <p>No products yet.</p>
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
    <?php endif; ?>
</div>

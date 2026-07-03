<?php
$result = api_get("/products/$productId");
$product = $result['data'] ?? [];

if (!$product):
    http_response_code(404);
?>
<div class="section">
    <h1>Product Not Found</h1>
    <a href="/products" class="btn">Back to Products</a>
</div>
<?php else: ?>
<div class="section">
    <a href="/products" class="btn btn-small">&larr; Back</a>

    <div class="product-detail">
        <div class="product-info">
            <h1><?= htmlspecialchars($product['name']) ?></h1>

            <?php if (!empty($product['brand'])): ?>
                <p class="brand"><?= htmlspecialchars($product['brand']) ?></p>
            <?php endif; ?>

            <p class="price">$<?= number_format($product['base_price'], 2) ?></p>

            <?php if (!empty($product['description'])): ?>
                <p class="description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <?php endif; ?>

            <?php if (!empty($product['categories'])): ?>
                <div class="tags">
                    <?php foreach ($product['categories'] as $cat): ?>
                        <a href="/products?category_id=<?= $cat['id'] ?>" class="tag">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($product['tags'])): ?>
                <div class="tags">
                    <?php foreach ($product['tags'] as $tag): ?>
                        <span class="tag tag-outline"><?= htmlspecialchars($tag['name']) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="product-variants">
            <h3>Choose Variant</h3>
            <?php $variants = $product['variants'] ?? []; ?>
            <?php if (empty($variants)): ?>
                <p>No variants available.</p>
            <?php else: ?>
                <form action="/cart" method="POST" class="variant-form">
                    <?php foreach ($variants as $variant): ?>
                        <label class="variant-option">
                            <input type="radio" name="variant_id" value="<?= $variant['id'] ?>" required>
                            <span class="variant-detail">
                                <?php if (!empty($variant['attributes'])): ?>
                                    <?php $attrs = json_decode($variant['attributes'], true); ?>
                                    <?php if ($attrs): ?>
                                        <?= htmlspecialchars(implode(' / ', $attrs)) ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <strong>$<?= number_format($variant['price'], 2) ?></strong>
                                <?php if ($variant['stock_qty'] > 0): ?>
                                    <span class="in-stock">In Stock</span>
                                <?php else: ?>
                                    <span class="out-of-stock">Out of Stock</span>
                                <?php endif; ?>
                            </span>
                        </label>
                    <?php endforeach; ?>

                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <label>
                        Quantity:
                        <input type="number" name="quantity" value="1" min="1" max="99">
                    </label>

                    <button type="submit" class="btn btn-primary btn-large">Add to Cart</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

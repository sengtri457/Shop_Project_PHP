<?php
$result = api_get('/cart?session_id=' . cart_session_id());
$cart = $result['data'] ?? [];
$items = $cart['items'] ?? [];
?>

<div class="section">
    <h1>Shopping Cart</h1>

    <?php if (empty($items)): ?>
        <p>Your cart is empty.</p>
        <a href="/products" class="btn">Browse Products</a>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($items as $item): ?>
                <div class="cart-item">
                    <div class="cart-item-info">
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <?php if (!empty($item['attributes'])): ?>
                            <?php $attrs = json_decode($item['attributes'], true); ?>
                            <?php if ($attrs): ?>
                                <p><?= htmlspecialchars(implode(' / ', $attrs)) ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <p class="price">$<?= number_format($item['price'], 2) ?></p>
                    </div>
                    <div class="cart-item-qty">
                        <form action="/cart" method="POST" style="display:none">
                            <input type="hidden" name="variant_id" value="<?= $item['variant_id'] ?>">
                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="0" max="99">
                            <button type="submit" class="btn btn-small">Update</button>
                        </form>
                        <span>Qty: <?= $item['quantity'] ?></span>
                    </div>
                    <div class="cart-item-total">
                        <strong>$<?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
        $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
        ?>
        <div class="cart-total">
            <h3>Total: $<?= number_format($total, 2) ?></h3>
        </div>

        <div class="cart-actions">
            <a href="/checkout" class="btn btn-primary btn-large">Proceed to Checkout</a>
            <a href="/products" class="btn">Continue Shopping</a>
        </div>
    <?php endif; ?>
</div>

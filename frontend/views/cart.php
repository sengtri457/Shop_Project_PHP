<?php
$result = api_get('/cart?session_id=' . cart_session_id());
$cart = $result['data'] ?? [];
$items = $cart['items'] ?? [];
?>

<div class="section" style="max-width: 800px; margin: 0 auto;">
    <h1 style="font-family: var(--font-serif); font-size: 2.5rem; font-weight: 500; margin-bottom: 40px; text-align: center; border-bottom: 1px solid var(--color-gray-light); padding-bottom: 20px;">Shopping Bag</h1>

    <?php if (empty($items)): ?>
        <div style="text-align: center; padding: 40px 0;">
            <p style="color: var(--color-gray); font-size: 1.1rem; margin-bottom: 24px;">Your bag is currently empty.</p>
            <a href="/products" class="btn btn-primary">Browse Essentials</a>
        </div>
    <?php else: ?>
        <div class="cart-items" style="display: flex; flex-direction: column; gap: 0; margin-bottom: 40px;">
            <?php foreach ($items as $item): ?>
                <div class="cart-item" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-gray-light); padding: 24px 0; gap: 20px;">
                    <div class="cart-item-info" style="flex: 2;">
                        <h3 style="font-family: var(--font-serif); font-size: 1.25rem; font-weight: 500; margin-bottom: 6px;"><?= htmlspecialchars($item['name']) ?></h3>
                        <?php if (!empty($item['attributes'])): ?>
                            <?php $attrs = json_decode($item['attributes'], true); ?>
                            <?php if ($attrs): ?>
                                <p style="color: var(--color-gray-dark); font-size: 13px; margin-bottom: 6px;"><?= htmlspecialchars(implode(' / ', $attrs)) ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <p class="price" style="font-size: 14px; font-weight: 500; color: var(--color-black);">$<?= number_format($item['price'], 2) ?></p>
                    </div>
                    
                    <div class="cart-item-qty" style="flex: 1; display: flex; justify-content: center;">
                        <form action="/cart/update" method="POST" style="display: flex; align-items: center; gap: 8px;">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="99" style="width: 55px; padding: 8px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); text-align: center; outline: none; font-size: 13px;">
                            <button type="submit" class="btn btn-small" style="padding: 8px 12px; font-size: 10px;">Update</button>
                        </form>
                    </div>

                    <div class="cart-item-total" style="flex: 1; text-align: right;">
                        <strong style="font-family: var(--font-sans); font-size: 15px; color: var(--color-black);">$<?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
                    </div>

                    <div class="cart-item-remove" style="display: flex; align-items: center;">
                        <form action="/cart/remove" method="POST" onsubmit="return confirm('Remove item from cart?')">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <button type="submit" class="btn btn-small btn-danger" style="background: none; border: none; color: var(--color-error); text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; font-weight: 600; cursor: pointer; padding: 0;">Remove</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
        $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
        ?>
        <div class="cart-total" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-gray-light); padding-bottom: 24px; margin-bottom: 30px;">
            <span style="font-size: 16px; font-weight: 600; text-transform: uppercase; color: var(--color-gray);">Subtotal</span>
            <h3 style="font-family: var(--font-sans); font-size: 1.8rem; font-weight: 600;">$<?= number_format($total, 2) ?></h3>
        </div>

        <div class="cart-actions" style="display: flex; flex-direction: column; gap: 12px; align-items: stretch;">
            <a href="/checkout" class="btn btn-primary btn-large" style="justify-content: center;">Proceed to Checkout</a>
            <a href="/products" class="btn btn-large" style="justify-content: center; background: transparent; border: 1px solid var(--color-gray-light); color: var(--color-gray-dark);">Continue Shopping</a>
        </div>
    <?php endif; ?>
</div>

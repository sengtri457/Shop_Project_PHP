<?php
$order = $_SESSION['last_order'] ?? null;
unset($_SESSION['last_order']);

if (!$order):
?>
<div class="section">
    <h1>Order Not Found</h1>
    <p>No recent order to display.</p>
    <a href="/" class="btn">Go Home</a>
</div>
<?php else: ?>
<div class="section">
    <h1>Order Confirmed</h1>
    <p>Thank you! Your order has been placed.</p>

    <div class="order-detail">
        <p><strong>Order ID:</strong> #<?= $order['id'] ?></p>
        <p><strong>Status:</strong> <?= $order['status'] ?></p>
        <p><strong>Total:</strong> $<?= number_format($order['total'], 2) ?></p>

        <?php if (!empty($order['items'])): ?>
            <h3>Items</h3>
            <ul>
                <?php foreach ($order['items'] as $item): ?>
                    <li>
                        <?= htmlspecialchars($item['product_name']) ?> x <?= $item['quantity'] ?>
                        — $<?= number_format($item['price_at_purchase'] * $item['quantity'], 2) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <a href="/" class="btn">Continue Shopping</a>
</div>
<?php endif; ?>

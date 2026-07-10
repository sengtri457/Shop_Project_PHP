<?php
if (!is_logged_in()) {
    redirect('/login');
    return;
}

$orderId = $orderId ?? 0;
$res = api_get("/orders/$orderId");
$order = $res['data'] ?? null;

if (!$order) {
    http_response_code(404);
    echo "<div class='section'><h1>Order not found</h1><a href='/' class='btn'>Go Home</a></div>";
    return;
}

// Security: Check if owner or admin
if (!is_admin() && (int) $order['customer_id'] !== (int) ($_SESSION['customer']['id'] ?? 0)) {
    http_response_code(403);
    echo "<div class='section'><h1>403 Forbidden</h1><p>You are not authorized to view this order.</p></div>";
    return;
}

$items = $order['items'] ?? [];
$discounts = $order['discounts'] ?? [];
?>

<div class="section">
    <?php if (is_admin()): ?>
        <a href="/admin/orders" class="btn btn-small">&larr; Back to Admin Orders</a>
    <?php else: ?>
        <a href="/" class="btn btn-small">&larr; Back to Shop</a>
    <?php endif; ?>

    <h1 style="margin-top: 20px;">Order Details #<?= $order['id'] ?></h1>

    <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-top: 20px;">
        <div style="display: flex; justify-content: space-between; flex-wrap: wrap; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
            <div>
                <p><strong>Order Status:</strong> <span class="badge badge-<?= htmlspecialchars($order['status']) ?>"><?= htmlspecialchars(ucfirst($order['status'])) ?></span></p>
                <p><strong>Date Placed:</strong> <?= htmlspecialchars($order['created_at']) ?></p>

                <?php if (is_admin()): ?>
                    <form action="/orders/<?= $order['id'] ?>/status" method="POST" style="margin-top: 15px; display: flex; align-items: center; gap: 10px;">
                        <label style="margin: 0; font-weight: normal; display: flex; align-items: center; gap: 5px;">
                            Change Status:
                            <select name="status" style="padding: 5px; border-radius: 4px; border: 1px solid #ddd;">
                                <?php foreach (['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $st): ?>
                                    <option value="<?= $st ?>" <?= $order['status'] === $st ? 'selected' : '' ?>>
                                        <?= ucfirst($st) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <button type="submit" class="btn btn-small btn-primary">Update</button>
                    </form>
                <?php endif; ?>
            </div>
            <div>
                <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?> (#<?= htmlspecialchars($order['customer_id']) ?>)</p>
                <p><strong>Total:</strong> <strong style="color: #007bff; font-size: 1.2rem;">$<?= number_format($order['total'], 2) ?></strong></p>
            </div>
        </div>

        <div style="margin-bottom: 25px; background: #fafafa; padding: 15px; border-radius: 6px; border: 1px solid #eee;">
            <h3 style="margin-bottom: 8px;">Shipping Address Snapshot</h3>
            <p>
                <?= htmlspecialchars($order['shipping_line1'] ?? '') ?>
                <?php if (!empty($order['shipping_line2'])): ?>
                    , <?= htmlspecialchars($order['shipping_line2']) ?>
                <?php endif; ?>
                <br>
                <?= htmlspecialchars($order['shipping_city'] ?? '') ?>
                <?php if (!empty($order['shipping_postal_code'])): ?>
                    , <?= htmlspecialchars($order['shipping_postal_code']) ?>
                <?php endif; ?>
                <br>
                <?= htmlspecialchars($order['shipping_country'] ?? '') ?>
                <?php if (!empty($order['shipping_phone'])): ?>
                    <br><strong>Phone:</strong> <?= htmlspecialchars($order['shipping_phone']) ?>
                <?php endif; ?>
            </p>
        </div>

        <h3>Order Items</h3>
        <?php if (empty($items)): ?>
            <p>No items in this order.</p>
        <?php else: ?>
            <table class="admin-table" style="margin-top: 10px; margin-bottom: 25px;">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Attributes</th>
                        <th>Price at Purchase</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($item['sku']) ?></td>
                            <td>
                                <?php if (!empty($item['attributes'])): ?>
                                    <?php $attrs = json_decode($item['attributes'], true); ?>
                                    <?php if ($attrs): ?>
                                        <?= htmlspecialchars(implode(' / ', $attrs)) ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>$<?= number_format($item['price_at_purchase'], 2) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>$<?= number_format($item['price_at_purchase'] * $item['quantity'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (!empty($discounts)): ?>
            <h3>Discounts Applied</h3>
            <div style="background: #e8f0fe; padding: 15px; border-radius: 6px; border: 1px solid #1967d2; margin-top: 10px;">
                <?php foreach ($discounts as $disc): ?>
                    <p>
                        Code: <strong><?= htmlspecialchars($disc['code']) ?></strong> 
                        (<?= htmlspecialchars(ucfirst($disc['type'])) ?> Discount) 
                        — Saved: <span style="color: #1967d2; font-weight: bold;">$<?= number_format($disc['amount_saved'], 2) ?></span>
                    </p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

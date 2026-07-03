<?php
$result = api_get('/orders');
$orders = $result['data'] ?? [];
?>

<div class="section">
    <h1>Orders</h1>

    <?php if (empty($orders)): ?>
        <p>No orders yet.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>#<?= $o['id'] ?></td>
                        <td><?= htmlspecialchars($o['customer_id'] ?? '') ?></td>
                        <td>$<?= number_format($o['total'], 2) ?></td>
                        <td><span class="badge badge-<?= $o['status'] ?>"><?= $o['status'] ?></span></td>
                        <td><?= $o['created_at'] ?></td>
                        <td>
                            <a href="/orders/<?= $o['id'] ?>" class="btn btn-small">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

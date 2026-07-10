<?php
$result = api_get('/orders');
$orders = $result['data'] ?? [];
?>

<div class="section">
    <a href="/admin" class="btn btn-small" style="margin-bottom: 30px;">&larr; Back to Dashboard</a>

    <h1 style="font-family: var(--font-serif); font-size: 2.5rem; font-weight: 500; margin-bottom: 40px;">Client Orders</h1>

    <?php if (empty($orders)): ?>
        <p style="color: var(--color-gray); text-align: center; margin-top: 40px;">No client orders found.</p>
    <?php else: ?>
        <table class="admin-table" style="box-shadow: var(--shadow-soft); border-radius: var(--border-radius); border: 1px solid var(--color-gray-light); overflow: hidden; width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: var(--color-gray-bg); border-bottom: 1px solid var(--color-gray-light);">
                    <th style="padding: 16px 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-gray-dark); text-align: left; font-weight: 600;">ID</th>
                    <th style="padding: 16px 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-gray-dark); text-align: left; font-weight: 600;">Customer</th>
                    <th style="padding: 16px 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-gray-dark); text-align: left; font-weight: 600;">Total</th>
                    <th style="padding: 16px 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-gray-dark); text-align: left; font-weight: 600;">Status</th>
                    <th style="padding: 16px 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-gray-dark); text-align: left; font-weight: 600;">Date</th>
                    <th style="padding: 16px 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-gray-dark); text-align: left; font-weight: 600; width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr style="border-bottom: 1px solid var(--color-gray-light);">
                        <td style="padding: 14px 20px; font-size: 14px; color: var(--color-gray-dark); font-weight: 500;">#<?= $o['id'] ?></td>
                        <td style="padding: 14px 20px; font-size: 14px; color: var(--color-black); font-weight: 600;"><?= htmlspecialchars($o['customer_name'] ?? 'Guest') ?></td>
                        <td style="padding: 14px 20px; font-size: 14px; color: var(--color-dark); font-weight: 600;">$<?= number_format($o['total'], 2) ?></td>
                        <td style="padding: 14px 20px;">
                            <span class="badge badge-<?= htmlspecialchars($o['status']) ?>">
                                <?= htmlspecialchars(ucfirst($o['status'])) ?>
                            </span>
                        </td>
                        <td style="padding: 14px 20px; font-size: 14px; color: var(--color-gray-dark);"><?= htmlspecialchars($o['created_at']) ?></td>
                        <td style="padding: 14px 20px;">
                            <a href="/orders/<?= $o['id'] ?>" class="btn btn-small" style="padding: 6px 12px; font-size: 11px;">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
$result = api_get('/products?limit=100');
$products = $result['data']['data'] ?? [];
?>

<div class="section">
    <a href="/admin" class="btn btn-small" style="margin-bottom: 30px;">&larr; Back to Dashboard</a>

    <div class="admin-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
        <h1 style="font-family: var(--font-serif); font-size: 2.5rem; font-weight: 500; margin: 0;">Products</h1>
        <a href="/admin/products/new" class="btn btn-primary">Add Product</a>
    </div>

    <?php if (empty($products)): ?>
        <p style="color: var(--color-gray); text-align: center; margin-top: 40px;">No products found.</p>
    <?php else: ?>
        <table class="admin-table" style="box-shadow: var(--shadow-soft); border-radius: var(--border-radius); border: 1px solid var(--color-gray-light); overflow: hidden; width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: var(--color-gray-bg); border-bottom: 1px solid var(--color-gray-light);">
                    <th style="padding: 16px 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-gray-dark); text-align: left; font-weight: 600;">ID</th>
                    <th style="padding: 16px 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-gray-dark); text-align: left; font-weight: 600;">Name</th>
                    <th style="padding: 16px 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-gray-dark); text-align: left; font-weight: 600;">Price</th>
                    <th style="padding: 16px 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-gray-dark); text-align: left; font-weight: 600;">Brand</th>
                    <th style="padding: 16px 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-gray-dark); text-align: left; font-weight: 600; width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr style="border-bottom: 1px solid var(--color-gray-light);">
                        <td style="padding: 14px 20px; font-size: 14px; color: var(--color-gray-dark); font-weight: 500;">#<?= $p['id'] ?></td>
                        <td style="padding: 14px 20px; font-size: 14px; color: var(--color-black); font-weight: 600;"><?= htmlspecialchars($p['name']) ?></td>
                        <td style="padding: 14px 20px; font-size: 14px; color: var(--color-dark);">$<?= number_format($p['base_price'], 2) ?></td>
                        <td style="padding: 14px 20px; font-size: 14px; color: var(--color-gray-dark);"><?= htmlspecialchars($p['brand'] ?? '—') ?></td>
                        <td style="padding: 14px 20px; display: flex; gap: 8px;">
                            <a href="/admin/products/<?= $p['id'] ?>/edit" class="btn btn-small" style="padding: 6px 12px; font-size: 11px;">Edit</a>
                            <a href="/admin/products?delete=<?= $p['id'] ?>" class="btn btn-small btn-danger"
                               onclick="return confirm('Delete this product?')" style="padding: 6px 12px; font-size: 11px;">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
$result = api_get('/products?limit=100');
$products = $result['data']['data'] ?? [];
?>

<div class="section">
    <div class="admin-header">
        <h1>Products</h1>
        <a href="/admin/products/new" class="btn btn-primary">Add Product</a>
    </div>

    <?php if (empty($products)): ?>
        <p>No products yet.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Brand</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td>$<?= number_format($p['base_price'], 2) ?></td>
                        <td><?= htmlspecialchars($p['brand'] ?? '') ?></td>
                        <td>
                            <a href="/admin/products/<?= $p['id'] ?>/edit" class="btn btn-small">Edit</a>
                            <a href="/admin/products?delete=<?= $p['id'] ?>" class="btn btn-small btn-danger"
                               onclick="return confirm('Delete this product?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

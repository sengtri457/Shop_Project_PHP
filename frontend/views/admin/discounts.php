<?php
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
$discount = null;

if ($editId) {
    $res = api_get("/discounts/$editId");
    $discount = $res['data'] ?? null;
}

$allRes = api_get('/discounts');
$discounts = $allRes['data'] ?? [];

$isEdit = $discount !== null;

// Format dates for datetime-local inputs
$startsAtVal = '';
if (!empty($discount['starts_at'])) {
    $startsAtVal = date('Y-m-d\TH:i', strtotime($discount['starts_at']));
}
$expiresAtVal = '';
if (!empty($discount['expires_at'])) {
    $expiresAtVal = date('Y-m-d\TH:i', strtotime($discount['expires_at']));
}
?>

<div class="section">
    <a href="/admin" class="btn btn-small" style="margin-bottom: 30px;">&larr; Back to Dashboard</a>

    <h1 style="font-family: var(--font-serif); font-size: 2.5rem; font-weight: 500; margin-bottom: 40px;">Manage Discounts</h1>

    <div class="product-detail" style="gap: 50px; grid-template-columns: 1fr 1.5fr;">
        <!-- Left Side: Form -->
        <div style="background: var(--color-gray-bg); padding: 30px; border-radius: var(--border-radius); border: 1px solid var(--color-gray-light); height: fit-content;">
            <h3 style="font-family: var(--font-serif); font-size: 1.4rem; font-weight: 500; margin-bottom: 20px; border-bottom: 1px solid var(--color-gray-light); padding-bottom: 12px;">
                <?= $isEdit ? 'Edit Discount Code' : 'Add Discount Code' ?>
            </h3>

            <form method="POST" action="/admin/discounts<?= $isEdit ? '?edit=' . $editId : '' ?>" class="admin-form" style="border: none; padding: 0; box-shadow: none; background: transparent; display: flex; flex-direction: column; gap: 16px;">
                <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
                    Coupon Code*
                    <input type="text" name="code" value="<?= htmlspecialchars($discount['code'] ?? '') ?>" required style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;" placeholder="e.g. SUMMER20">
                </label>

                <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
                    Discount Type*
                    <select name="type" required style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; background: #fff;">
                        <option value="fixed" <?= ($discount['type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Fixed Amount ($)</option>
                        <option value="percentage" <?= ($discount['type'] ?? '') === 'percentage' ? 'selected' : '' ?>>Percentage (%)</option>
                    </select>
                </label>

                <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
                    Discount Value*
                    <input type="number" name="value" step="0.01" value="<?= htmlspecialchars($discount['value'] ?? '') ?>" required style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;" placeholder="e.g. 10.00 or 15">
                </label>

                <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
                    Min Order Amount ($)
                    <input type="number" name="min_order_amount" step="0.01" value="<?= htmlspecialchars($discount['min_order_amount'] ?? '') ?>" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;" placeholder="Optional">
                </label>

                <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
                    Usage Limit (Times)
                    <input type="number" name="usage_limit" value="<?= htmlspecialchars($discount['usage_limit'] ?? '') ?>" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;" placeholder="Optional (e.g. 100)">
                </label>

                <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
                    Starts At
                    <input type="datetime-local" name="starts_at" value="<?= $startsAtVal ?>" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
                </label>

                <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
                    Expires At
                    <input type="datetime-local" name="expires_at" value="<?= $expiresAtVal ?>" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
                </label>

                <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 600; font-size: 13px;">
                    Status
                    <select name="is_active" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; background: #fff;">
                        <option value="1" <?= isset($discount['is_active']) && $discount['is_active'] ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= isset($discount['is_active']) && !$discount['is_active'] ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </label>

                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;"><?= $isEdit ? 'Update' : 'Create' ?></button>
                    <?php if ($isEdit): ?>
                        <a href="/admin/discounts" class="btn" style="flex: 1; justify-content: center; background: transparent; border: 1px solid var(--color-gray-light); color: var(--color-gray-dark);">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Right Side: List Table -->
        <div>
            <h3 style="font-family: var(--font-serif); font-size: 1.4rem; font-weight: 500; margin-bottom: 20px; border-bottom: 1px solid var(--color-gray-light); padding-bottom: 12px;">Existing Discounts</h3>
            
            <?php if (empty($discounts)): ?>
                <p style="color: var(--color-gray);">No discount codes defined yet.</p>
            <?php else: ?>
                <table class="admin-table" style="box-shadow: var(--shadow-soft); border-radius: var(--border-radius); border: 1px solid var(--color-gray-light); overflow: hidden; width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--color-gray-bg); border-bottom: 1px solid var(--color-gray-light);">
                            <th style="padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: var(--color-gray-dark); text-align: left; font-weight: 600;">Code</th>
                            <th style="padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: var(--color-gray-dark); text-align: left; font-weight: 600;">Type</th>
                            <th style="padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: var(--color-gray-dark); text-align: left; font-weight: 600;">Value</th>
                            <th style="padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: var(--color-gray-dark); text-align: left; font-weight: 600;">Used / Limit</th>
                            <th style="padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: var(--color-gray-dark); text-align: left; font-weight: 600;">Active</th>
                            <th style="padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: var(--color-gray-dark); text-align: left; font-weight: 600; width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($discounts as $d): ?>
                            <tr style="border-bottom: 1px solid var(--color-gray-light);">
                                <td style="padding: 12px 16px; font-size: 14px; font-weight: 600; color: var(--color-black);">
                                    <?= htmlspecialchars($d['code']) ?>
                                </td>
                                <td style="padding: 12px 16px; font-size: 14px; color: var(--color-gray-dark);">
                                    <?= htmlspecialchars(ucfirst($d['type'])) ?>
                                </td>
                                <td style="padding: 12px 16px; font-size: 14px; font-weight: 600;">
                                    <?= $d['type'] === 'fixed' ? '$' . number_format($d['value'], 2) : number_format($d['value'], 0) . '%' ?>
                                </td>
                                <td style="padding: 12px 16px; font-size: 14px; color: var(--color-gray-dark);">
                                    <?= $d['times_used'] ?> / <?= $d['usage_limit'] ?? '∞' ?>
                                </td>
                                <td style="padding: 12px 16px; font-size: 14px;">
                                    <?php if ($d['is_active']): ?>
                                        <span class="badge badge-delivered" style="background: #e6f4ea; color: #137333;">Yes</span>
                                    <?php else: ?>
                                        <span class="badge badge-cancelled" style="background: #fce8e6; color: #c5221f;">No</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px 16px; display: flex; gap: 8px;">
                                    <a href="/admin/discounts?edit=<?= $d['id'] ?>" class="btn btn-small" style="padding: 4px 8px; font-size: 11px;">Edit</a>
                                    <a href="/admin/discounts?delete=<?= $d['id'] ?>" class="btn btn-small btn-danger" onclick="return confirm('Delete discount code <?= htmlspecialchars($d['code']) ?>?')" style="padding: 4px 8px; font-size: 11px;">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

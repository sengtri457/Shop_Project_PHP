<?php
if (!is_logged_in()) {
    redirect('/login');
    return;
}

$addressesRes = api_get('/addresses');
$userAddresses = $addressesRes['data'] ?? [];
?>

<div class="section">
    <a href="/checkout" class="btn btn-small" style="margin-bottom: 30px;">&larr; Back to Checkout</a>
    <h1 style="font-family: var(--font-serif); font-size: 2.5rem; font-weight: 500; margin-bottom: 40px;">Shipping Addresses</h1>

    <div class="product-detail" style="gap: 60px;">
        <!-- Address List -->
        <div class="addresses-list" style="padding: 0;">
            <h3 style="font-family: var(--font-serif); font-size: 1.4rem; font-weight: 500; margin-bottom: 20px; border-bottom: 1px solid var(--color-gray-light); padding-bottom: 12px;">Saved Addresses</h3>
            <?php if (empty($userAddresses)): ?>
                <p style="color: var(--color-gray); margin-top: 15px;">No addresses saved yet. Add one using the form on the right.</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <?php foreach ($userAddresses as $addr): ?>
                        <div style="background: #fff; padding: 24px; border-radius: var(--border-radius); border: 1px solid var(--color-gray-light); position: relative; box-shadow: var(--shadow-soft);">
                            <?php if ($addr['is_default']): ?>
                                <span class="badge badge-delivered" style="position: absolute; top: 24px; right: 24px;">Default</span>
                            <?php endif; ?>
                            <p style="margin-bottom: 6px;"><strong style="font-size: 12px; text-transform: uppercase; color: var(--color-gray);">Street:</strong> <span style="font-size: 14px; color: var(--color-dark);"><?= htmlspecialchars($addr['line1']) ?></span></p>
                            <?php if ($addr['line2']): ?>
                                <p style="margin-bottom: 6px;"><strong style="font-size: 12px; text-transform: uppercase; color: var(--color-gray);">Line 2:</strong> <span style="font-size: 14px; color: var(--color-dark);"><?= htmlspecialchars($addr['line2']) ?></span></p>
                            <?php endif; ?>
                            <p style="margin-bottom: 6px;"><strong style="font-size: 12px; text-transform: uppercase; color: var(--color-gray);">City & Zip:</strong> <span style="font-size: 14px; color: var(--color-dark);"><?= htmlspecialchars($addr['city']) ?><?= $addr['postal_code'] ? ', ' . htmlspecialchars($addr['postal_code']) : '' ?></span></p>
                            <p style="margin-bottom: 6px;"><strong style="font-size: 12px; text-transform: uppercase; color: var(--color-gray);">Country:</strong> <span style="font-size: 14px; color: var(--color-dark);"><?= htmlspecialchars($addr['country']) ?></span></p>
                            <?php if ($addr['phone']): ?>
                                <p style="margin-bottom: 6px;"><strong style="font-size: 12px; text-transform: uppercase; color: var(--color-gray);">Phone:</strong> <span style="font-size: 14px; color: var(--color-dark);"><?= htmlspecialchars($addr['phone']) ?></span></p>
                            <?php endif; ?>
                            
                            <div style="margin-top: 20px; border-top: 1px solid var(--color-gray-light); padding-top: 15px;">
                                <a href="/customer/addresses?delete=<?= $addr['id'] ?>" 
                                   class="btn btn-small btn-danger"
                                   onclick="return confirm('Delete this address?')" style="padding: 6px 12px; font-size: 10px;">Delete Address</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Add Address Form -->
        <div class="address-add-form" style="background: var(--color-gray-bg); padding: 40px; border-radius: var(--border-radius); border: 1px solid var(--color-gray-light);">
            <h3 style="font-family: var(--font-serif); font-size: 1.4rem; font-weight: 500; margin-bottom: 20px; border-bottom: 1px solid var(--color-gray-light); padding-bottom: 12px;">Add Address</h3>
            <form action="/customer/addresses" method="POST" class="auth-form" style="margin-top: 15px;">
                <label>
                    Street Address (Line 1)*
                    <input type="text" name="line1" required placeholder="e.g. 123 Linen St">
                </label>
                <label>
                    Apartment, Suite, Unit (Line 2)
                    <input type="text" name="line2" placeholder="e.g. Apt 3A">
                </label>
                <label>
                    City*
                    <input type="text" name="city" required placeholder="e.g. San Francisco">
                </label>
                <label>
                    Postal Code
                    <input type="text" name="postal_code" placeholder="e.g. 94103">
                </label>
                <label>
                    Country*
                    <input type="text" name="country" required placeholder="e.g. United States">
                </label>
                <label>
                    Phone
                    <input type="text" name="phone" placeholder="e.g. 415-555-0123">
                </label>
                <label style="flex-direction: row; align-items: center; gap: 8px; cursor: pointer; margin-top: 10px;">
                    <input type="checkbox" name="is_default" value="1" checked style="accent-color: var(--color-black);">
                    Set as default shipping address
                </label>
                
                <button type="submit" class="btn btn-primary" style="margin-top: 15px; width: 100%; justify-content: center;">Save Address</button>
            </form>
        </div>
    </div>
</div>

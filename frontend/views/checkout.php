<?php
if (!is_logged_in()) {
    $_SESSION['_flash']['error'] = 'Please login to checkout';
    redirect('/login');
    return;
}

$customer = $_SESSION['customer'] ?? [];
$addresses = api_get('/addresses');
$userAddresses = $addresses['data'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartResult = api_get('/cart?session_id=' . cart_session_id());
    $cartItems = $cartResult['data']['items'] ?? [];

    if (empty($cartItems)) {
        $_SESSION['_flash']['error'] = 'Cart is empty';
        redirect('/checkout');
    }

    $addressId = (int) ($_POST['address_id'] ?? 0);

    if (!$addressId) {
        $_SESSION['_errors']['_global'] = 'Please select a shipping address';
        redirect('/checkout');
    }

    $items = array_map(fn($i) => [
        'variant_id' => $i['variant_id'],
        'quantity' => $i['quantity'],
    ], $cartItems);

    $orderData = [
        'customer_id' => $customer['id'],
        'items' => $items,
        'address_id' => $addressId,
    ];

    if (!empty($_POST['discount_code'])) {
        $orderData['discount'] = ['code' => $_POST['discount_code']];
    }

    $result = api_post('/orders', $orderData);

    if ($result['code'] === 201) {
        $_SESSION['_flash']['success'] = 'Order placed successfully!';
        $_SESSION['last_order'] = $result['data'];
        redirect('/order');
    }

    $_SESSION['_errors']['_global'] = $result['data']['error'] ?? 'Checkout failed';
}
?>

<div class="section" style="max-width: 600px; margin: 0 auto;">
    <h1 style="font-family: var(--font-serif); font-size: 2.5rem; font-weight: 500; text-align: center; margin-bottom: 40px;">Checkout</h1>

    <div class="checkout-layout" style="border: none; padding: 0; box-shadow: none;">
        <div class="checkout-form">
            <?php if (has_errors()): ?>
                <div class="alert alert-error" style="margin-bottom: 20px;"><?= error() ?></div>
            <?php endif; ?>

            <form method="POST">
                <h3 style="font-family: var(--font-serif); font-size: 1.4rem; font-weight: 500; margin-bottom: 12px;">Shipping Address</h3>
                
                <?php if (!empty($userAddresses)): ?>
                    <select name="address_id" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; font-size: 14px; background: #fff; margin-bottom: 24px;">
                        <option value="">Select a shipping address...</option>
                        <?php foreach ($userAddresses as $addr): ?>
                            <option value="<?= $addr['id'] ?>" <?= $addr['is_default'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($addr['line1']) ?>, <?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['country']) ?> (<?= htmlspecialchars($addr['phone'] ?? '') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <p style="color: var(--color-gray-dark); font-size: 14px; margin-bottom: 24px;">You have no saved addresses. <a href="/customer/addresses" style="color: var(--color-black); font-weight: 600; text-decoration: underline;">Add an address</a> before checking out.</p>
                <?php endif; ?>

                <h3 style="font-family: var(--font-serif); font-size: 1.4rem; font-weight: 500; margin-bottom: 12px; margin-top: 10px;">Promo Discount Code</h3>
                <input type="text" name="discount_code" placeholder="Enter coupon code (e.g. SUMMER10)" style="width: 100%; padding: 12px 16px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; font-size: 14px; margin-bottom: 30px;">

                <?php
                $cartRes = api_get('/cart?session_id=' . cart_session_id());
                $cartItems = $cartRes['data']['items'] ?? [];
                ?>
                <h3 style="font-family: var(--font-serif); font-size: 1.4rem; font-weight: 500; margin-bottom: 12px;">Order Summary</h3>
                <?php if (empty($cartItems)): ?>
                    <p style="color: var(--color-gray); margin-bottom: 24px;">Your cart is empty.</p>
                <?php else: ?>
                    <ul class="order-summary" style="list-style: none; padding: 20px; background: var(--color-gray-bg); border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); display: flex; flex-direction: column; gap: 10px; margin-bottom: 30px;">
                        <?php foreach ($cartItems as $item): ?>
                            <li style="display: flex; justify-content: space-between; font-size: 14px; color: var(--color-gray-dark);">
                                <span>
                                    <?= htmlspecialchars($item['name']) ?> 
                                    <span style="font-size: 12px; color: var(--color-gray);">
                                        <?php if (!empty($item['attributes'])): ?>
                                            (<?= htmlspecialchars(implode(' / ', json_decode($item['attributes'], true))) ?>)
                                        <?php endif; ?>
                                        x <?= $item['quantity'] ?>
                                    </span>
                                </span>
                                <span style="font-weight: 600; color: var(--color-black);">$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                            </li>
                        <?php endforeach; ?>
                        
                        <li style="display: flex; justify-content: space-between; font-size: 16px; color: var(--color-black); border-top: 1px solid var(--color-gray-light); padding-top: 12px; margin-top: 6px; font-weight: 600;">
                            <span>Total</span>
                            <span>$<?= number_format(array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cartItems)), 2) ?></span>
                        </li>
                    </ul>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary btn-large" style="width: 100%; justify-content: center;" <?= empty($cartItems) || empty($userAddresses) ? 'disabled' : '' ?>>
                    Place Order
                </button>
            </form>
        </div>
    </div>
</div>

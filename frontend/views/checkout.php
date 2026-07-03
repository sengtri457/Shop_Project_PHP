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

    $items = array_map(fn($i) => [
        'variant_id' => $i['variant_id'],
        'quantity' => $i['quantity'],
    ], $cartItems);

    $orderData = [
        'customer_id' => $customer['id'],
        'items' => $items,
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

<div class="section">
    <h1>Checkout</h1>

    <div class="checkout-layout">
        <div class="checkout-form">
            <?php if (has_errors()): ?>
                <div class="alert alert-error"><?= error() ?></div>
            <?php endif; ?>

            <form method="POST">
                <h3>Shipping Address</h3>
                <?php if (!empty($userAddresses)): ?>
                    <select name="address_id">
                        <?php foreach ($userAddresses as $addr): ?>
                            <option value="<?= $addr['id'] ?>">
                                <?= htmlspecialchars($addr['line1']) ?>, <?= htmlspecialchars($addr['city']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <p><a href="/customer/addresses">Add an address</a> before checking out.</p>
                <?php endif; ?>

                <h3>Discount Code</h3>
                <input type="text" name="discount_code" placeholder="Enter code">

                <?php
                $cartRes = api_get('/cart?session_id=' . cart_session_id());
                $cartItems = $cartRes['data']['items'] ?? [];
                ?>
                <h3>Order Summary</h3>
                <?php if (empty($cartItems)): ?>
                    <p>Your cart is empty.</p>
                <?php else: ?>
                    <ul class="order-summary">
                        <?php foreach ($cartItems as $item): ?>
                            <li>
                                <?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?>
                                — $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="total">
                        <strong>
                            Total: $<?= number_format(array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cartItems)), 2) ?>
                        </strong>
                    </p>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary btn-large" <?= empty($cartItems) ? 'disabled' : '' ?>>
                    Place Order
                </button>
            </form>
        </div>
    </div>
</div>

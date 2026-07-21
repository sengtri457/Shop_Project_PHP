<?php
if (!is_logged_in()) {
    $_SESSION['_flash']['error'] = 'Please login to checkout';
    redirect('/login');
    return;
}

$customer = $_SESSION['customer'] ?? [];
$addresses = api_get('/addresses');
$userAddresses = $addresses['data'] ?? [];

$cartRes = api_get('/cart?session_id=' . cart_session_id());
$cartItems = $cartRes['data']['items'] ?? [];
$subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cartItems));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($cartItems)) {
        $_SESSION['_flash']['error'] = 'Cart is empty';
        redirect('/checkout');
    }

    $addressId = (int) ($_POST['address_id'] ?? 0);
    $paymentMethod = trim($_POST['payment_method'] ?? 'cod');

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
        'payment_method' => $paymentMethod,
    ];

    if (!empty($_POST['discount_code'])) {
        $orderData['discount'] = ['code' => $_POST['discount_code']];
    }

    $result = api_post('/orders', $orderData);

    if ($result['code'] === 201) {
        api_delete('/cart?session_id=' . cart_session_id());
        $_SESSION['last_order'] = $result['data'];
        
        // If Bakong KHQR, handle in frontend modal
        if ($paymentMethod === 'bakong') {
            $_SESSION['pending_bakong_order_id'] = $result['data']['id'];
        } else {
            $_SESSION['_flash']['success'] = 'Order placed successfully!';
            redirect('/order');
        }
    } else {
        $_SESSION['_errors']['_global'] = $result['data']['error'] ?? 'Checkout failed';
    }
}

$pendingBakongOrderId = $_SESSION['pending_bakong_order_id'] ?? null;
if ($pendingBakongOrderId) {
    unset($_SESSION['pending_bakong_order_id']);
}
?>

<div class="max-w-[1000px] mx-auto px-6 py-12">
    <div class="mb-10 text-center">
        <h1 class="font-serif text-3xl md:text-4xl font-semibold text-brand-text mb-2">Checkout</h1>
        <p class="text-xs text-brand-muted">Complete your shipping address and payment details below.</p>
    </div>

    <?php if (has_errors()): ?>
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-lg">
            <?= error() ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left: Form Details -->
        <div class="lg:col-span-7 space-y-6">
            <form method="POST" id="checkout-form" class="space-y-6">
                <!-- Shipping Address Section -->
                <div class="bg-white border border-brand-border rounded-lg p-6 shadow-sm">
                    <h3 class="font-serif text-lg font-semibold text-brand-text mb-4 pb-2 border-b border-brand-border flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-brand-accent text-sm"></i> Shipping Address
                    </h3>
                    
                    <?php if (!empty($userAddresses)): ?>
                        <select name="address_id" required class="w-full bg-brand-bg border border-brand-border rounded px-4 py-3 text-xs text-brand-text focus:outline-none focus:border-brand-text">
                            <option value="">Select a shipping address...</option>
                            <?php foreach ($userAddresses as $addr): ?>
                                <option value="<?= $addr['id'] ?>" <?= $addr['is_default'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($addr['line1']) ?>, <?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['country']) ?> (<?= htmlspecialchars($addr['phone'] ?? '') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <p class="text-xs text-brand-muted mb-4">
                            You have no saved addresses. 
                            <a href="/customer/addresses" class="text-brand-text font-bold underline">Add an address</a> before checking out.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Payment Method Section -->
                <div class="bg-white border border-brand-border rounded-lg p-6 shadow-sm">
                    <h3 class="font-serif text-lg font-semibold text-brand-text mb-4 pb-2 border-b border-brand-border flex items-center gap-2">
                        <i class="fa-solid fa-credit-card text-brand-accent text-sm"></i> Payment Method
                    </h3>

                    <div class="space-y-3">
                        <!-- Option 1: Bakong KHQR -->
                        <label class="flex items-center justify-between p-4 rounded-lg border-2 border-brand-border hover:border-brand-text cursor-pointer transition-all has-[:checked]:border-brand-text has-[:checked]:bg-brand-bg/50">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="payment_method" value="bakong" checked class="w-4 h-4 text-brand-text focus:ring-0">
                                <div>
                                    <span class="font-sans text-xs font-bold text-brand-text block">Bakong KHQR (Scan to Pay)</span>
                                    <span class="text-[11px] text-brand-muted">Instant payment via ABA, ACLEDA, Wing, or Bakong App</span>
                                </div>
                            </div>
                            <span class="bg-[#e62020] text-white text-[10px] font-extrabold px-2.5 py-1 rounded tracking-wider uppercase">KHQR</span>
                        </label>

                        <!-- Option 2: Cash on Delivery -->
                        <label class="flex items-center justify-between p-4 rounded-lg border-2 border-brand-border hover:border-brand-text cursor-pointer transition-all has-[:checked]:border-brand-text has-[:checked]:bg-brand-bg/50">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="payment_method" value="cod" class="w-4 h-4 text-brand-text focus:ring-0">
                                <div>
                                    <span class="font-sans text-xs font-bold text-brand-text block">Cash on Delivery (COD)</span>
                                    <span class="text-[11px] text-brand-muted">Pay with cash upon package delivery</span>
                                </div>
                            </div>
                            <i class="fa-solid fa-truck text-brand-muted"></i>
                        </label>
                    </div>
                </div>

                <!-- Promo Code Section -->
                <div class="bg-white border border-brand-border rounded-lg p-6 shadow-sm">
                    <h3 class="font-serif text-lg font-semibold text-brand-text mb-4 pb-2 border-b border-brand-border flex items-center gap-2">
                        <i class="fa-solid fa-tag text-brand-accent text-sm"></i> Promo Code
                    </h3>
                    
                    <div class="flex gap-2">
                        <input type="text" id="coupon-code-input" name="discount_code" placeholder="Enter coupon code (e.g. SUMMER10)" class="flex-1 bg-brand-bg border border-brand-border rounded px-4 py-2.5 text-xs text-brand-text focus:outline-none focus:border-brand-text">
                        <button type="button" onclick="applyCouponCode()" id="coupon-apply-btn" class="px-5 py-2.5 bg-brand-darker border border-brand-border text-brand-text text-xs font-bold uppercase tracking-wider rounded hover:bg-brand-text hover:text-white transition-all">
                            Apply
                        </button>
                    </div>
                    <div id="coupon-message" class="text-xs font-semibold mt-2 hidden"></div>
                </div>

                <button type="submit" class="w-full py-4 bg-brand-text text-white text-xs font-bold uppercase tracking-widest rounded shadow-md hover:bg-brand-text/90 transition-all disabled:opacity-50" <?= empty($cartItems) || empty($userAddresses) ? 'disabled' : '' ?>>
                    Complete Order &rarr;
                </button>
            </form>
        </div>

        <!-- Right: Order Summary -->
        <div class="lg:col-span-5">
            <div class="bg-white border border-brand-border rounded-lg p-6 shadow-sm sticky top-24">
                <h3 class="font-serif text-lg font-semibold text-brand-text mb-4 pb-2 border-b border-brand-border">Order Summary</h3>

                <?php if (empty($cartItems)): ?>
                    <p class="text-xs text-brand-muted">Your cart is empty.</p>
                <?php else: ?>
                    <div class="divide-y divide-brand-border mb-6">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="py-3 first:pt-0 last:pb-0 flex justify-between items-center text-xs">
                                <div>
                                    <h4 class="font-semibold text-brand-text"><?= htmlspecialchars($item['name']) ?></h4>
                                    <p class="text-[11px] text-brand-muted mt-0.5">
                                        <?php if (!empty($item['attributes'])): ?>
                                            <?= htmlspecialchars(implode(' / ', json_decode($item['attributes'], true))) ?> &bull; 
                                        <?php endif; ?>
                                        Qty: <?= $item['quantity'] ?>
                                    </p>
                                </div>
                                <span class="font-bold text-brand-text">$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="space-y-2.5 pt-4 border-t border-brand-border text-xs">
                        <div class="flex justify-between text-brand-muted">
                            <span>Subtotal</span>
                            <span class="font-semibold text-brand-text">$<span id="summary-subtotal"><?= number_format($subtotal, 2) ?></span></span>
                        </div>
                        <div id="summary-discount-row" class="hidden justify-between text-emerald-600 font-semibold">
                            <span>Discount (<span id="applied-coupon-code"></span>)</span>
                            <span>-$<span id="summary-discount-amount">0.00</span></span>
                        </div>
                        <div class="flex justify-between text-sm font-bold text-brand-text pt-2 border-t border-dashed border-brand-border">
                            <span>Total</span>
                            <span>$<span id="summary-total"><?= number_format($subtotal, 2) ?></span></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bakong KHQR Payment Modal Overlay -->
<div id="bakong-modal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 <?= $pendingBakongOrderId ? '' : 'hidden' ?>">
    <div class="bg-white rounded-xl max-w-sm w-full p-6 text-center shadow-2xl relative border border-brand-border">
        <!-- Top Bakong Badge Header -->
        <div class="bg-[#e62020] text-white py-2.5 px-4 rounded-t-xl -mx-6 -mt-6 mb-5 flex items-center justify-between">
            <span class="text-[11px] font-extrabold tracking-widest uppercase flex items-center gap-1.5">
                <i class="fa-solid fa-qrcode"></i> KHQR Payment
            </span>
            <span class="text-[10px] font-bold opacity-90">Bakong NBC</span>
        </div>

        <h3 class="font-serif text-xl font-bold text-brand-text mb-1">Scan to Pay</h3>
        <p class="text-[11px] text-brand-muted mb-4">Scan using ABA, ACLEDA, Wing, or Bakong mobile app</p>

        <!-- QR Image Box -->
        <div class="w-56 h-56 bg-brand-bg rounded-lg border-2 border-dashed border-brand-border p-3 mx-auto mb-4 flex items-center justify-center relative shadow-inner">
            <img id="bakong-qr-img" src="" class="w-full h-full object-contain rounded hidden" alt="Bakong KHQR Code">
            <div id="bakong-qr-loader" class="flex flex-col items-center gap-2 text-brand-muted">
                <i class="fa-solid fa-circle-notch fa-spin text-2xl text-brand-accent"></i>
                <span class="text-[11px] font-semibold">Generating KHQR...</span>
            </div>
        </div>

        <!-- Currency Selection Toggle -->
        <div class="flex items-center justify-center gap-2 mb-4">
            <button type="button" id="btn-khqr-usd" onclick="switchKhqrCurrency('USD')" class="px-3 py-1 text-xs font-bold rounded-lg border transition-all bg-brand-text text-white border-brand-text shadow-sm">
                <i class="fa-solid fa-dollar-sign"></i> USD ($)
            </button>
            <button type="button" id="btn-khqr-khr" onclick="switchKhqrCurrency('KHR')" class="px-3 py-1 text-xs font-bold rounded-lg border transition-all bg-brand-bg text-brand-muted border-brand-border hover:bg-slate-100">
                ៛ KHR (Riel)
            </button>
        </div>

        <!-- Payment Details -->
        <div class="bg-brand-bg p-3 rounded-lg text-xs space-y-1.5 mb-5 border border-brand-border">
            <div class="flex justify-between text-brand-muted">
                <span>Merchant Name:</span>
                <strong class="text-brand-text font-bold" id="bakong-merchant-name">SENGTREE bUN</strong>
            </div>
            <div class="flex justify-between text-brand-muted">
                <span>Account ID:</span>
                <strong class="text-brand-text font-medium" id="bakong-account-id">bun_sengtri@bkrt</strong>
            </div>
            <div class="flex justify-between text-brand-muted pt-1 border-t border-brand-border">
                <span>Total Amount:</span>
                <strong class="text-brand-accent font-extrabold text-sm"><span id="bakong-currency-symbol">$</span><span id="bakong-amount">0.00</span></strong>
            </div>
        </div>

        <!-- Live Status Spinner -->
        <div id="bakong-status-spinner" class="flex items-center justify-center gap-2 text-xs font-semibold text-brand-muted py-1">
            <i class="fa-solid fa-spinner fa-spin text-brand-accent"></i>
            <span>Waiting for payment scan...</span>
        </div>
    </div>
</div>

<script>
let bakongPollInterval = null;
let currentBakongOrderId = <?= (int)($pendingBakongOrderId ?? 0) ?>;

if (currentBakongOrderId > 0) {
    initBakongPayment(currentBakongOrderId);
}

function applyCouponCode() {
    const input = document.getElementById('coupon-code-input');
    const code = input.value.trim();
    const msgEl = document.getElementById('coupon-message');
    const btn = document.getElementById('coupon-apply-btn');
    
    if (!code) {
        msgEl.classList.remove('hidden', 'text-emerald-600');
        msgEl.classList.add('text-rose-600');
        msgEl.textContent = 'Please enter a coupon code';
        return;
    }
    
    btn.disabled = true;
    btn.textContent = 'Checking...';
    
    const subtotal = <?= (float)$subtotal ?>;
    const customerId = <?= (int)($customer['id'] ?? 0) ?>;
    
    fetch(API_BASE + '/discounts/validate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ code: code, total: subtotal, customer_id: customerId })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = 'Apply';
        msgEl.classList.remove('hidden');
        if (data.valid) {
            msgEl.classList.remove('text-rose-600');
            msgEl.classList.add('text-emerald-600');
            msgEl.textContent = `Coupon applied! Saved $${data.amount_saved.toFixed(2)}`;
            
            document.getElementById('summary-discount-row').classList.remove('hidden');
            document.getElementById('summary-discount-row').classList.add('flex');
            document.getElementById('applied-coupon-code').textContent = data.discount.code;
            document.getElementById('summary-discount-amount').textContent = data.amount_saved.toFixed(2);
            
            const newTotal = subtotal - data.amount_saved;
            document.getElementById('summary-total').textContent = newTotal.toFixed(2);
        } else {
            msgEl.classList.remove('text-emerald-600');
            msgEl.classList.add('text-rose-600');
            msgEl.textContent = data.error || 'Invalid coupon code';
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = 'Apply';
        msgEl.classList.remove('hidden', 'text-emerald-600');
        msgEl.classList.add('text-rose-600');
        msgEl.textContent = 'Error validating coupon code';
    });
}

let currentKhqrCurrency = 'USD';

function switchKhqrCurrency(curr) {
    currentKhqrCurrency = curr;
    const btnUsd = document.getElementById('btn-khqr-usd');
    const btnKhr = document.getElementById('btn-khqr-khr');
    
    if (curr === 'KHR') {
        btnKhr.className = 'px-3 py-1 text-xs font-bold rounded-lg border transition-all bg-brand-text text-white border-brand-text shadow-sm';
        btnUsd.className = 'px-3 py-1 text-xs font-bold rounded-lg border transition-all bg-brand-bg text-brand-muted border-brand-border hover:bg-slate-100';
    } else {
        btnUsd.className = 'px-3 py-1 text-xs font-bold rounded-lg border transition-all bg-brand-text text-white border-brand-text shadow-sm';
        btnKhr.className = 'px-3 py-1 text-xs font-bold rounded-lg border transition-all bg-brand-bg text-brand-muted border-brand-border hover:bg-slate-100';
    }
    
    if (currentBakongOrderId > 0) {
        document.getElementById('bakong-qr-img').classList.add('hidden');
        document.getElementById('bakong-qr-loader').classList.remove('hidden');
        initBakongPayment(currentBakongOrderId, curr);
    }
}

function initBakongPayment(orderId, currency = 'USD') {
    const modal = document.getElementById('bakong-modal');
    modal.classList.remove('hidden');
    
    fetch(API_BASE + '/payments/bakong/generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_id: orderId, currency: currency })
    })
    .then(res => res.json())
    .then(data => {
        if (data.qr_image) {
            document.getElementById('bakong-qr-img').src = data.qr_image;
            document.getElementById('bakong-qr-img').classList.remove('hidden');
            document.getElementById('bakong-qr-loader').classList.add('hidden');
            document.getElementById('bakong-merchant-name').textContent = data.merchant_name || 'SENGTREE bUN';
            document.getElementById('bakong-account-id').textContent = data.account_id || 'bun_sengtri@bkrt';
            
            if (data.currency === 'KHR') {
                document.getElementById('bakong-currency-symbol').textContent = '៛';
                document.getElementById('bakong-amount').textContent = Number(data.amount).toLocaleString('en-US');
            } else {
                document.getElementById('bakong-currency-symbol').textContent = '$';
                document.getElementById('bakong-amount').textContent = Number(data.amount).toFixed(2);
            }
            
            startBakongPolling(orderId, data.md5);
        } else {
            alert(data.error || 'Failed to generate Bakong KHQR code');
        }
    })
    .catch(err => console.error(err));
}

function startBakongPolling(orderId, md5) {
    if (bakongPollInterval) clearInterval(bakongPollInterval);
    
    bakongPollInterval = setInterval(() => {
        fetch(API_BASE + '/payments/bakong/check', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId, md5: md5 })
        })
        .then(res => res.json())
        .then(data => {
            if (data.paid) {
                clearInterval(bakongPollInterval);
                document.getElementById('bakong-status-spinner').innerHTML = `
                    <span class="text-emerald-600 font-bold flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-lg"></i> Payment Verified! Redirecting...
                    </span>
                `;
                setTimeout(() => {
                    window.location.href = '/order';
                }, 1500);
            }
        })
        .catch(err => console.error(err));
    }, 3000);
}
</script>

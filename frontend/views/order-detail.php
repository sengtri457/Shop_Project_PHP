<?php
if (!is_logged_in()) {
    redirect('/login');
    return;
}

$orderId = (int) ($orderId ?? 0);
$res = api_get("/orders/$orderId");
$order = $res['data'] ?? null;

if (!$order) {
    http_response_code(404);
    ?>
    <div class="py-16 px-6 max-w-xl mx-auto text-center">
        <h1 class="font-serif text-3xl font-semibold text-brand-text mb-3">Order Not Found</h1>
        <p class="text-xs text-brand-muted mb-6">The requested order #<?= $orderId ?> does not exist or has been removed.</p>
        <a href="/customer/orders" class="inline-block px-6 py-3 bg-brand-text text-white text-xs font-bold uppercase tracking-wider rounded">Back to Orders</a>
    </div>
    <?php
    return;
}

// Security: Check if owner or admin
if (!is_admin() && (int) $order['customer_id'] !== (int) ($_SESSION['customer']['id'] ?? 0)) {
    http_response_code(403);
    ?>
    <div class="py-16 px-6 max-w-xl mx-auto text-center">
        <h1 class="font-serif text-3xl font-semibold text-brand-error mb-3">403 Access Denied</h1>
        <p class="text-xs text-brand-muted mb-6">You do not have permission to view order #<?= $orderId ?>.</p>
        <a href="/" class="inline-block px-6 py-3 bg-brand-text text-white text-xs font-bold uppercase tracking-wider rounded">Go Home</a>
    </div>
    <?php
    return;
}

$items = $order['items'] ?? [];
$discounts = $order['discounts'] ?? [];
$orderDate = date('F d, Y · h:i A', strtotime($order['created_at']));
$status = strtolower($order['status'] ?? 'pending');

function getOrderStatusBadge(string $st): string {
    switch (strtolower($st)) {
        case 'pending': return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'confirmed':
        case 'processing': return 'bg-sky-50 text-sky-700 border-sky-200';
        case 'shipped': return 'bg-indigo-50 text-indigo-700 border-indigo-200';
        case 'delivered': return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'cancelled': return 'bg-rose-50 text-rose-700 border-rose-200';
        default: return 'bg-gray-50 text-gray-700 border-gray-200';
    }
}
?>

<div class="max-w-[1280px] mx-auto px-6 md:px-8 py-10">
    <!-- Top Back Link -->
    <div class="mb-6">
        <?php if (is_admin()): ?>
            <a href="/admin/orders" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-brand-muted hover:text-brand-text transition-colors">
                <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to Admin Orders
            </a>
        <?php else: ?>
            <a href="/customer/orders" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-brand-muted hover:text-brand-text transition-colors">
                <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to My Orders
            </a>
        <?php endif; ?>
    </div>

    <!-- Header Section -->
    <div class="flex flex-wrap items-center justify-between gap-4 pb-6 mb-8 border-b border-brand-border">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="font-serif text-3xl font-semibold text-brand-text">Order #<?= (int)$order['id'] ?></h1>
                <span class="px-3 py-1 rounded-full border text-[11px] font-bold uppercase tracking-wider <?= getOrderStatusBadge($status) ?>">
                    <?= htmlspecialchars(ucfirst($status)) ?>
                </span>
            </div>
            <p class="text-xs text-brand-muted mt-1">Placed on <?= $orderDate ?></p>
        </div>

        <div class="text-right">
            <span class="text-[10px] font-bold uppercase tracking-wider text-brand-muted block">Total Amount</span>
            <span class="text-2xl font-bold text-brand-text">$<?= number_format((float)($order['total'] ?? 0), 2) ?></span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left Column: Order Items & Pricing Breakdown -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Items Table / List Card -->
            <div class="bg-white border border-brand-border rounded-lg overflow-hidden shadow-sm">
                <div class="px-6 py-4 bg-brand-bg border-b border-brand-border">
                    <h3 class="font-serif text-lg font-semibold text-brand-text">Order Items (<?= count($items) ?>)</h3>
                </div>

                <div class="divide-y divide-brand-border">
                    <?php foreach ($items as $item): 
                        $variantImg = !empty($item['variant_image']) ? $item['variant_image'] : (!empty($item['image_url']) ? $item['image_url'] : null);
                        if ($variantImg) {
                            $itemImg = asset_url($variantImg);
                        } else {
                            $prodImages = split_image_urls($item['product_images'] ?? $item['images'] ?? '');
                            $itemImg = !empty($prodImages) ? asset_url($prodImages[0]) : '/assets/images/hero_banner.png';
                        }
                        $attrs = !empty($item['attributes']) ? json_decode($item['attributes'], true) : null;
                    ?>
                        <div class="p-6 flex items-center gap-5">
                            <div class="w-16 h-20 bg-brand-darker border border-brand-border rounded overflow-hidden flex-shrink-0">
                                <img src="<?= htmlspecialchars((string)$itemImg) ?>" class="w-full h-full object-cover" alt="<?= htmlspecialchars((string)($item['product_name'] ?? 'Product')) ?>">
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h4 class="font-sans text-sm font-semibold text-brand-text truncate">
                                    <?= htmlspecialchars((string)($item['product_name'] ?? 'Item')) ?>
                                </h4>
                                <div class="text-[11px] text-brand-muted mt-1 space-y-0.5">
                                    <?php if (!empty($item['sku'])): ?>
                                        <p>SKU: <span class="font-medium text-brand-text"><?= htmlspecialchars((string)$item['sku']) ?></span></p>
                                    <?php endif; ?>
                                    <?php if ($attrs && is_array($attrs)): ?>
                                        <p>Variant: <span class="font-medium text-brand-text"><?= htmlspecialchars(implode(' / ', $attrs)) ?></span></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-xs font-bold text-brand-text">
                                    $<?= number_format((float)(($item['price_at_purchase'] ?? 0) * ($item['quantity'] ?? 1)), 2) ?>
                                </div>
                                <div class="text-[11px] text-brand-muted mt-0.5">
                                    <?= (int)($item['quantity'] ?? 1) ?> &times; $<?= number_format((float)($item['price_at_purchase'] ?? 0), 2) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Discounts Applied Card (if any) -->
            <?php if (!empty($discounts)): ?>
                <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-lg p-5">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-800 mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-tag"></i> Applied Coupon Code
                    </h4>
                    <?php foreach ($discounts as $disc): ?>
                        <div class="flex items-center justify-between text-xs text-emerald-900 font-medium">
                            <span>Code <strong><?= htmlspecialchars((string)$disc['code']) ?></strong> (<?= htmlspecialchars(ucfirst($disc['type'] ?? 'discount')) ?>)</span>
                            <span class="font-bold text-emerald-700">-$<?= number_format((float)($disc['amount_saved'] ?? 0), 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Shipping Details & Admin Actions -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Shipping Address Card -->
            <div class="bg-white border border-brand-border rounded-lg p-6 shadow-sm">
                <h3 class="font-serif text-base font-semibold text-brand-text pb-3 mb-4 border-b border-brand-border flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-brand-accent text-sm"></i> Shipping Details
                </h3>

                <div class="text-xs text-brand-muted space-y-2 leading-relaxed">
                    <p class="font-bold text-brand-text text-sm mb-1">
                        <?= htmlspecialchars((string)($order['customer_name'] ?? $_SESSION['customer']['name'] ?? 'Customer')) ?>
                    </p>
                    <p><?= htmlspecialchars((string)($order['shipping_line1'] ?? '')) ?></p>
                    <?php if (!empty($order['shipping_line2'])): ?>
                        <p><?= htmlspecialchars((string)$order['shipping_line2']) ?></p>
                    <?php endif; ?>
                    <p>
                        <?= htmlspecialchars((string)($order['shipping_city'] ?? '')) ?>
                        <?php if (!empty($order['shipping_postal_code'])): ?>
                            , <?= htmlspecialchars((string)$order['shipping_postal_code']) ?>
                        <?php endif; ?>
                    </p>
                    <p class="font-semibold text-brand-text"><?= htmlspecialchars((string)($order['shipping_country'] ?? '')) ?></p>
                    <?php if (!empty($order['shipping_phone'])): ?>
                        <p class="pt-2 border-t border-brand-border text-brand-text">
                            <strong>Phone:</strong> <?= htmlspecialchars((string)$order['shipping_phone']) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Admin Status Action Card -->
            <?php if (is_admin()): ?>
                <div class="bg-white border border-brand-border rounded-lg p-6 shadow-sm">
                    <h3 class="font-serif text-base font-semibold text-brand-text pb-3 mb-4 border-b border-brand-border flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-brand-accent text-sm"></i> Admin Order Management
                    </h3>

                    <form action="/orders/<?= (int)$order['id'] ?>/status" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-2">
                                Change Order Status
                            </label>
                            <select name="status" class="w-full bg-brand-bg border border-brand-border rounded px-3 py-2.5 text-xs text-brand-text focus:outline-none focus:border-brand-text">
                                <?php foreach (['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $st): ?>
                                    <option value="<?= $st ?>" <?= strtolower($order['status']) === $st ? 'selected' : '' ?>>
                                        <?= ucfirst($st) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-brand-text hover:bg-brand-text/90 text-white text-xs font-bold uppercase tracking-wider rounded transition-all">
                            Update Order Status
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


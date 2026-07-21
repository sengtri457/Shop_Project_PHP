<?php
if (!is_logged_in()) {
    $_SESSION['_flash']['error'] = 'Please login to view your orders';
    redirect('/login');
    return;
}

$customerId = (int) $_SESSION['customer']['id'];
$ordersResult = api_get('/orders?customer_id=' . $customerId);
$orders = $ordersResult['data'] ?? [];

function getOrderStatusBadgeClass(string $status): string
{
    switch (strtolower($status)) {
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

<div class="max-w-[1280px] mx-auto px-6 md:px-8 py-12">
    <div class="mb-10 text-center">
        <h1 class="font-serif text-3xl md:text-4xl font-semibold text-brand-text mb-2">My Orders</h1>
        <p class="text-xs text-brand-muted">Track order status, view items, and manage order history.</p>
    </div>

    <?php if (empty($orders)): ?>
        <div class="text-center py-20 bg-white rounded-lg border border-brand-border flex flex-col items-center justify-center p-8 max-w-lg mx-auto shadow-sm">
            <div class="w-14 h-14 rounded-full bg-brand-darker text-brand-muted flex items-center justify-center mb-4 text-xl">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <h3 class="font-serif text-lg font-semibold text-brand-text mb-1">No Orders Yet</h3>
            <p class="text-xs text-brand-muted mb-6 leading-relaxed max-w-xs">
                You haven't placed any orders yet. Discover our latest collections and start shopping!
            </p>
            <a href="/products" class="inline-block bg-brand-text text-white text-xs font-bold uppercase tracking-widest py-3 px-8 rounded hover:bg-brand-text/90 transition-all shadow-sm">
                Shop Collection
            </a>
        </div>
    <?php else: ?>
        <div class="space-y-6 max-w-4xl mx-auto">
            <?php foreach ($orders as $order): 
                $orderId = (int) $order['id'];
                
                // Fetch full order details
                $detailResult = api_get("/orders/$orderId");
                $orderDetails = $detailResult['data'] ?? $order;
                $items = $orderDetails['items'] ?? [];
                $discounts = $orderDetails['discounts'] ?? [];
                $orderDate = date('F d, Y', strtotime($order['created_at']));
                $status = strtolower($order['status'] ?? 'pending');
            ?>
                <div class="bg-white border border-brand-border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <!-- Top Bar Header -->
                    <div class="bg-brand-bg px-6 py-4 border-b border-brand-border flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-6 flex-wrap text-xs">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-muted block">Order Placed</span>
                                <span class="font-medium text-brand-text"><?= $orderDate ?></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-muted block">Total Amount</span>
                                <span class="font-bold text-brand-text">$<?= number_format((float)($order['total'] ?? 0), 2) ?></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-muted block">Ship To</span>
                                <span class="font-medium text-brand-text" title="<?= htmlspecialchars(($order['shipping_line1'] ?? '') . ', ' . ($order['shipping_city'] ?? '')) ?>">
                                    <?= htmlspecialchars((string)($order['shipping_city'] ?? '')) ?>, <?= htmlspecialchars((string)($order['shipping_country'] ?? '')) ?>
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="px-3 py-0.5 rounded-full border text-[10px] font-bold uppercase tracking-wider <?= getOrderStatusBadgeClass($status) ?>">
                                <?= htmlspecialchars(ucfirst($status)) ?>
                            </span>
                            <span class="text-xs font-bold text-brand-text">#<?= $orderId ?></span>
                        </div>
                    </div>

                    <!-- Items Body -->
                    <div class="p-6 divide-y divide-brand-border">
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
                            <div class="py-4 first:pt-0 last:pb-0 flex items-center gap-4">
                                <div class="w-16 h-20 bg-brand-darker border border-brand-border rounded overflow-hidden flex-shrink-0">
                                    <img src="<?= htmlspecialchars((string)$itemImg) ?>" class="w-full h-full object-cover" alt="<?= htmlspecialchars((string)($item['product_name'] ?? 'Product')) ?>">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-sans text-xs font-semibold text-brand-text truncate">
                                        <?= htmlspecialchars((string)($item['product_name'] ?? 'Item')) ?>
                                    </h4>
                                    <div class="text-[11px] text-brand-muted mt-0.5 space-y-0.5">
                                        <?php if (!empty($item['sku'])): ?>
                                            <p>SKU: <span class="font-medium text-brand-text"><?= htmlspecialchars((string)$item['sku']) ?></span></p>
                                        <?php endif; ?>
                                        <?php if ($attrs && is_array($attrs)): ?>
                                            <p>Variant: <span class="font-medium text-brand-text"><?= htmlspecialchars(implode(' / ', $attrs)) ?></span></p>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-[11px] text-brand-muted mt-1">
                                        Quantity: <strong class="text-brand-text"><?= (int)($item['quantity'] ?? 1) ?></strong> &times; $<?= number_format((float)($item['price_at_purchase'] ?? 0), 2) ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-bold text-brand-text">
                                        $<?= number_format((float)(($item['price_at_purchase'] ?? 0) * ($item['quantity'] ?? 1)), 2) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Discounts applied -->
                        <?php if (!empty($discounts)): ?>
                            <div class="pt-3 mt-3 text-xs text-emerald-700 flex items-center justify-between font-medium">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-tag text-emerald-600"></i> Coupon Applied
                                </span>
                                <span>-$<?= number_format((float)($discounts[0]['amount_saved'] ?? 0), 2) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Footer Details Link -->
                    <div class="px-6 py-3 bg-brand-bg/50 border-t border-brand-border flex justify-end">
                        <a href="/orders/<?= $orderId ?>" class="text-[11px] font-bold uppercase tracking-wider text-brand-text hover:text-brand-accent transition-colors flex items-center gap-1">
                            View Order Details <i class="fa-solid fa-chevron-right text-[9px]"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>


<?php
$pageTitle = 'Order Confirmed';
$order = $_SESSION['last_order'] ?? null;
unset($_SESSION['last_order']);

if (!$order):
?>
<div class="py-16 px-6 max-w-xl mx-auto text-center">
    <div class="w-16 h-16 rounded-full bg-brand-darker border border-brand-border flex items-center justify-center mx-auto mb-6 text-brand-muted text-xl">
        <i class="fa-solid fa-receipt"></i>
    </div>
    <h1 class="font-serif text-3xl font-semibold text-brand-text mb-3">No Recent Order Found</h1>
    <p class="text-xs text-brand-muted leading-relaxed mb-8">
        We couldn't locate any recent checkout confirmation for your current session. Browse our collection to place a new order.
    </p>
    <a href="/products" class="inline-flex items-center gap-2 px-8 py-3.5 bg-brand-text text-white text-xs font-bold uppercase tracking-widest rounded hover:bg-brand-text/90 transition-all shadow-md">
        <span>Explore Products</span>
        <i class="fa-solid fa-arrow-right text-[10px]"></i>
    </a>
</div>
<?php else:
    $items = $order['items'] ?? [];
    $orderDate = date('F d, Y · h:i A');
?>
<div class="py-12 md:py-16 max-w-3xl mx-auto px-6">
    <!-- Success Banner -->
    <div class="text-center mb-10">
        <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center mx-auto mb-4 text-2xl shadow-sm">
            <i class="fa-solid fa-check"></i>
        </div>
        <span class="text-[11px] font-bold uppercase tracking-widest text-emerald-600 block mb-1">Payment Successful</span>
        <h1 class="font-serif text-3xl md:text-4xl font-semibold text-brand-text tracking-tight mb-2">Thank You For Your Order!</h1>
        <p class="text-xs text-brand-muted max-w-md mx-auto leading-relaxed">
            Order <strong class="text-brand-text font-bold">#<?= (int)$order['id'] ?></strong> has been placed successfully. We are getting your items ready for shipment.
        </p>
    </div>

    <!-- Order Summary Card -->
    <div class="bg-white border border-brand-border rounded-lg shadow-sm overflow-hidden mb-8">
        <!-- Top Info Header -->
        <div class="bg-brand-bg px-6 py-4 border-b border-brand-border flex flex-wrap items-center justify-between gap-4 text-xs">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-muted block">Order ID</span>
                <span class="font-bold text-brand-text text-sm">#<?= (int)$order['id'] ?></span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-muted block">Date</span>
                <span class="font-medium text-brand-text"><?= $orderDate ?></span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-muted block">Status</span>
                <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                    <?= htmlspecialchars(ucfirst($order['status'] ?? 'Pending')) ?>
                </span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-muted block">Total Paid</span>
                <span class="font-bold text-brand-text text-sm">$<?= number_format((float)($order['total'] ?? 0), 2) ?></span>
            </div>
        </div>

        <!-- Items List -->
        <?php if (!empty($items)): ?>
            <div class="p-6 divide-y divide-brand-border">
                <?php foreach ($items as $item): 
                    $variantImg = !empty($item['variant_image']) ? $item['variant_image'] : (!empty($item['image_url']) ? $item['image_url'] : null);
                    if ($variantImg) {
                        $itemImg = asset_url($variantImg);
                    } else {
                        $prodImages = split_image_urls($item['product_images'] ?? $item['images'] ?? '');
                        $itemImg = !empty($prodImages) ? asset_url($prodImages[0]) : '/assets/images/hero_banner.png';
                    }
                ?>
                    <div class="py-4 first:pt-0 last:pb-0 flex items-center gap-4">
                        <div class="w-16 h-20 bg-brand-darker border border-brand-border rounded overflow-hidden flex-shrink-0">
                            <img src="<?= htmlspecialchars((string)$itemImg) ?>" class="w-full h-full object-cover" alt="<?= htmlspecialchars((string)($item['product_name'] ?? 'Product')) ?>">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-sans text-xs font-semibold text-brand-text truncate"><?= htmlspecialchars((string)($item['product_name'] ?? 'Item')) ?></h4>
                            <p class="text-[11px] text-brand-muted mt-0.5">
                                Quantity: <strong class="text-brand-text"><?= (int)($item['quantity'] ?? 1) ?></strong> 
                                &times; $<?= number_format((float)($item['price_at_purchase'] ?? 0), 2) ?>
                            </p>
                        </div>
                        <div class="text-right font-bold text-xs text-brand-text">
                            $<?= number_format((float)(($item['price_at_purchase'] ?? 0) * ($item['quantity'] ?? 1)), 2) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="/customer/orders" class="px-8 py-3.5 bg-brand-text text-white text-xs font-bold uppercase tracking-widest rounded text-center hover:bg-brand-text/90 transition-all shadow-sm">
            View All Orders
        </a>
        <a href="/products" class="px-8 py-3.5 bg-white text-brand-text border border-brand-border text-xs font-bold uppercase tracking-widest rounded text-center hover:bg-brand-bg transition-all">
            Continue Shopping
        </a>
    </div>
</div>
<?php endif; ?>


<?php
if (!is_logged_in()) {
    $_SESSION['_flash']['error'] = 'Please login to view your orders';
    redirect('/login');
    return;
}

$customerId = $_SESSION['customer']['id'];
$ordersResult = api_get('/orders?customer_id=' . $customerId);
$orders = $ordersResult['data'] ?? [];

// Helper to get HSL colors for order statuses
function getStatusBadgeStyle(string $status): string
{
    $status = strtolower($status);
    switch ($status) {
        case 'pending':
            return 'background: #fef3c7; color: #d97706;'; // Amber/Gold
        case 'confirmed':
        case 'processing':
            return 'background: #e0f2fe; color: #0284c7;'; // Sky Blue
        case 'shipped':
            return 'background: #e0e7ff; color: #4f46e5;'; // Indigo
        case 'delivered':
            return 'background: #dcfce7; color: #16a34a;'; // Emerald Green
        case 'cancelled':
            return 'background: #ffeeeb; color: #b91c1c;'; // Terracotta Red
        default:
            return 'background: #f3f4f6; color: #4b5563;'; // Grey
    }
}
?>

<div class="max-w-[1280px] mx-auto px-8 pt-12 pb-24">
    <div class="mb-10 text-center">
        <h1 class="font-serif text-[2.3rem] font-medium text-brand-text mb-2">My Orders</h1>
        <p class="text-[13px] text-brand-muted">Track status, view invoices, and browse order history.</p>
    </div>

    <?php if (empty($orders)): ?>
        <div class="text-center py-20 bg-brand-darker rounded-brand border border-brand-border flex flex-col items-center justify-center p-6">
            <div class="w-12 h-12 rounded-full bg-brand-accentLight text-brand-accent flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <h3 class="font-sans text-[15px] font-semibold text-brand-text mb-1">No orders yet</h3>
            <p class="text-[12px] text-brand-muted mb-6 max-w-xs leading-relaxed">You haven't placed any orders yet. Explore our latest arrivals to get started!</p>
            <a href="/products" class="inline-block bg-brand-text text-brand-bg text-[11px] font-bold uppercase tracking-widest py-3 px-6 rounded hover:bg-brand-text/95 transition-all">
                Shop Our Collection
            </a>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 30px;">
            <?php foreach ($orders as $order): 
                $orderId = $order['id'];
                
                // Fetch full order details including items
                $detailResult = api_get("/orders/$orderId");
                $orderDetails = $detailResult['data'] ?? $order;
                $items = $orderDetails['items'] ?? [];
                $discounts = $orderDetails['discounts'] ?? [];
                
                $orderDate = date('F d, Y', strtotime($order['created_at']));
            ?>
                <div style="background: #fff; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); overflow: hidden; box-shadow: var(--shadow-soft);">
                    <!-- Header of the Card -->
                    <div style="background: var(--color-gray-bg); padding: 20px 24px; border-b: 1px solid var(--color-gray-light); display: flex; flex-wrap: wrap; justify-content: space-between; gap: 16px; align-items: center;">
                        <div style="display: flex; gap: 32px; flex-wrap: wrap;">
                            <div>
                                <p style="font-size: 10px; font-weight: 600; text-transform: uppercase; color: var(--color-gray); margin-bottom: 4px;">Order Placed</p>
                                <p style="font-size: 13.5px; font-weight: 500; color: var(--color-black);"><?= $orderDate ?></p>
                            </div>
                            <div>
                                <p style="font-size: 10px; font-weight: 600; text-transform: uppercase; color: var(--color-gray); margin-bottom: 4px;">Total Amount</p>
                                <p style="font-size: 13.5px; font-weight: 600; color: var(--color-black);">$<?= number_format($order['total'], 2) ?></p>
                            </div>
                            <div>
                                <p style="font-size: 10px; font-weight: 600; text-transform: uppercase; color: var(--color-gray); margin-bottom: 4px;">Ship To</p>
                                <p style="font-size: 13.5px; font-weight: 500; color: var(--color-black);" title="<?= htmlspecialchars($order['shipping_line1'] . ', ' . $order['shipping_city']) ?>">
                                    <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_country']) ?>
                                </p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span style="font-size: 12px; font-weight: 600; padding: 6px 14px; border-radius: 9999px; text-transform: uppercase; tracking-wider: 1px; <?= getStatusBadgeStyle($order['status']) ?>">
                                <?= htmlspecialchars($order['status']) ?>
                            </span>
                            <span style="font-size: 13.5px; font-weight: 500; color: var(--color-gray-dark);">Order #<?= $orderId ?></span>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div style="padding: 24px; display: flex; flex-direction: column; gap: 20px;">
                        <?php foreach ($items as $item): 
                            $prodImages = split_image_urls($item['product_images'] ?? '');
                            $itemImg = !empty($prodImages) ? asset_url($prodImages[0]) : '/assets/images/hero_banner.png';
                        ?>
                            <div style="display: flex; gap: 20px; align-items: center;">
                                <div style="width: 70px; height: 90px; border-radius: var(--border-radius); border: 1px solid var(--color-gray-light); overflow: hidden; flex-shrink: 0; background: var(--color-gray-bg);">
                                    <img src="<?= htmlspecialchars($itemImg) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div style="flex: 1;">
                                    <h4 style="font-size: 14.5px; font-weight: 600; color: var(--color-black); margin-bottom: 4px;"><?= htmlspecialchars($item['product_name']) ?></h4>
                                    <p style="font-size: 12px; color: var(--color-gray-dark); margin-bottom: 2px;">
                                        SKU: <span style="font-weight: 500;"><?= htmlspecialchars($item['sku'] ?? 'N/A') ?></span>
                                        <?php if (!empty($item['attributes'])): ?>
                                            | Variant: <span style="font-weight: 500;"><?= htmlspecialchars(implode(' / ', json_decode($item['attributes'], true))) ?></span>
                                        <?php endif; ?>
                                    </p>
                                    <p style="font-size: 13.5px; color: var(--color-gray-dark);">Qty: <span style="font-weight: 600; color: var(--color-black);"><?= $item['quantity'] ?></span> &times; $<?= number_format($item['price_at_purchase'], 2) ?></p>
                                </div>
                                <div style="text-align: right; font-weight: 600; font-size: 14.5px; color: var(--color-black);">
                                    $<?= number_format($item['price_at_purchase'] * $item['quantity'], 2) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <!-- Applied Discounts snapshot -->
                        <?php if (!empty($discounts)): ?>
                            <div style="border-top: 1px solid var(--color-gray-light); padding-top: 16px; margin-top: 8px; display: flex; flex-direction: column; gap: 8px;">
                                <?php foreach ($discounts as $d): ?>
                                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: var(--color-error);">
                                        <span style="display: flex; align-items: center; gap: 6px;">
                                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a1.125 1.125 0 001.591 0l4.318-4.318a1.125 1.125 0 000-1.591l-9.581-9.581A1.125 1.125 0 009.568 3z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"></path>
                                            </svg>
                                            Coupon applied: <strong><?= htmlspecialchars($d['code']) ?></strong>
                                        </span>
                                        <span>-$<?= number_format($d['amount_saved'], 2) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

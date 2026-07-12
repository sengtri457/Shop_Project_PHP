<?php
$poId = $poId ?? 0;
$poResult = api_get("/purchase-orders/$poId");
$po = $poResult['code'] === 200 ? $poResult['data'] : null;

if (!$po) {
    echo '<div class="max-w-[1000px] mx-auto px-4 py-8 text-center text-brand-error font-semibold">Purchase Order not found.</div>';
    return;
}

$statusColor = 'bg-brand-muted/10 text-brand-muted';
if ($po['status'] === 'ordered') {
    $statusColor = 'bg-brand-accentLight text-brand-accent';
} elseif ($po['status'] === 'received') {
    $statusColor = 'bg-brand-successBg text-brand-success';
} elseif ($po['status'] === 'cancelled') {
    $statusColor = 'bg-brand-errorBg text-brand-error';
}
?>

<div class="max-w-[1000px] mx-auto px-4 lg:px-8 py-8">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <a href="/admin/purchase-orders" class="text-[11px] font-bold uppercase tracking-wider text-brand-muted hover:text-brand-text mb-3 inline-block">&larr; Back to ledger</a>
            <h1 class="font-serif text-[1.75rem] font-bold text-brand-text leading-tight mt-1">Purchase Order Detail</h1>
            <p class="text-[12.5px] text-brand-muted mt-1">Audit and update restocking order transitions.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-[11px] text-brand-muted uppercase font-bold tracking-wider">Status:</span>
            <span class="inline-block px-3 py-1 text-[11px] font-bold uppercase tracking-wider rounded <?= $statusColor ?>">
                <?= htmlspecialchars($po['status']) ?>
            </span>
        </div>
    </div>

    <!-- Error/Success Flash Messages -->
    <?php if (isset($_SESSION['_flash']['success'])): ?>
        <div class="mb-6 p-4 bg-brand-successBg border border-brand-success text-brand-success text-[12.5px] rounded font-semibold">
            <?= htmlspecialchars($_SESSION['_flash']['success']) ?>
            <?php unset($_SESSION['_flash']['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['_flash']['error'])): ?>
        <div class="mb-6 p-4 bg-brand-errorBg border border-brand-error text-brand-error text-[12.5px] rounded font-semibold">
            <?= htmlspecialchars($_SESSION['_flash']['error']) ?>
            <?php unset($_SESSION['_flash']['error']); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- PO Information Card -->
        <div class="bg-brand-bg border border-brand-border rounded-brand p-6 lg:col-span-2">
            <h3 class="font-serif text-[1.2rem] font-semibold text-brand-text mb-5">Order Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-[12.5px]">
                <div>
                    <span class="block text-[10px] uppercase font-bold tracking-wider text-brand-muted">PO Identification</span>
                    <span class="font-semibold text-brand-text">PO-#<?= $po['id'] ?></span>
                </div>
                <div>
                    <span class="block text-[10px] uppercase font-bold tracking-wider text-brand-muted">Total Costs</span>
                    <span class="font-bold text-brand-text">$<?= number_format($po['total_cost'], 2) ?></span>
                </div>
                <div>
                    <span class="block text-[10px] uppercase font-bold tracking-wider text-brand-muted">Created Timestamp</span>
                    <span class="text-brand-muted"><?= date('M d, Y H:i', strtotime($po['created_at'])) ?></span>
                </div>
                <div>
                    <span class="block text-[10px] uppercase font-bold tracking-wider text-brand-muted">Restocked Timestamp</span>
                    <span class="text-brand-muted">
                        <?= $po['received_at'] ? date('M d, Y H:i', strtotime($po['received_at'])) : 'Pending Supplier Arrival' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Supplier Contacts -->
        <div class="bg-brand-bg border border-brand-border rounded-brand p-6 lg:col-span-1">
            <h3 class="font-serif text-[1.2rem] font-semibold text-brand-text mb-5">Supplier Contacts</h3>
            
            <div class="flex flex-col gap-4 text-[12.5px]">
                <div>
                    <span class="block text-[10px] uppercase font-bold tracking-wider text-brand-muted">Supplier Name</span>
                    <span class="font-semibold text-brand-text"><?= htmlspecialchars($po['supplier_name']) ?></span>
                </div>
                <div>
                    <span class="block text-[10px] uppercase font-bold tracking-wider text-brand-muted">Contact Person</span>
                    <span class="text-brand-text"><?= htmlspecialchars($po['supplier_email'] ?? '—') ?></span>
                </div>
                <div>
                    <span class="block text-[10px] uppercase font-bold tracking-wider text-brand-muted">Phone Number</span>
                    <span class="text-brand-text"><?= htmlspecialchars($po['supplier_phone'] ?? '—') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- PO Items List -->
    <div class="bg-brand-bg border border-brand-border rounded-brand p-6 mb-8">
        <h3 class="font-serif text-[1.2rem] font-semibold text-brand-text mb-5">Procured Items</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-brand-border text-brand-muted text-[10px] uppercase tracking-wider">
                        <th class="py-3 px-3 font-semibold">Product Detail</th>
                        <th class="py-3 px-3 font-semibold w-32">SKU</th>
                        <th class="py-3 px-3 font-semibold w-24 text-center">Quantity</th>
                        <th class="py-3 px-3 font-semibold w-32 text-right">Unit Cost</th>
                        <th class="py-3 px-3 font-semibold w-32 text-right">Line Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    <?php foreach ($po['items'] as $item): 
                        $attrs = json_decode($item['attributes'] ?? '{}', true);
                        $attrStr = !empty($attrs) ? implode(' / ', $attrs) : '';
                    ?>
                        <tr class="text-[12.5px] text-brand-text hover:bg-brand-darker/40 transition-colors">
                            <td class="py-4 px-3 font-semibold">
                                <?= htmlspecialchars($item['product_name']) ?>
                                <?php if ($attrStr): ?>
                                    <span class="block text-[10.5px] font-normal text-brand-muted mt-0.5"><?= htmlspecialchars($attrStr) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-3 text-brand-muted font-mono"><?= htmlspecialchars($item['sku']) ?></td>
                            <td class="py-4 px-3 text-center font-bold text-brand-accent"><?= $item['quantity'] ?></td>
                            <td class="py-4 px-3 text-right text-brand-muted">$<?= number_format($item['unit_cost'], 2) ?></td>
                            <td class="py-4 px-3 text-right font-semibold">$<?= number_format($item['quantity'] * $item['unit_cost'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Actions Panel -->
    <div class="bg-brand-bg border border-brand-border rounded-brand p-6">
        <h3 class="font-serif text-[1.2rem] font-semibold text-brand-text mb-4">Workflow Actions</h3>
        
        <?php if ($po['status'] === 'draft'): ?>
            <p class="text-[12.5px] text-brand-muted mb-5">This restocking order is in draft mode. Click to mark it as officially ordered from the supplier.</p>
            <form action="/admin/purchase-orders/<?= $po['id'] ?>" method="POST" class="inline">
                <input type="hidden" name="status" value="ordered">
                <button type="submit" class="bg-brand-text text-brand-bg text-[12px] font-bold uppercase tracking-wider py-3 px-6 rounded hover:bg-brand-text/95 transition-all">
                    Send Order &rarr;
                </button>
            </form>
        <?php elseif ($po['status'] === 'ordered'): ?>
            <div class="p-4 bg-brand-accentLight border-l-[3px] border-brand-accent text-[12px] text-brand-accent rounded mb-5 leading-relaxed font-semibold">
                ⚠️ IMPORTANT: Confirming receipt of this order will automatically increment inventory levels in your products store for each variant item listed above. This action is auditable and irreversible.
            </div>
            
            <div class="flex gap-4">
                <form action="/admin/purchase-orders/<?= $po['id'] ?>" method="POST" class="inline" onsubmit="return confirm('Confirm receipt of these products? This will update variant stock levels.');">
                    <input type="hidden" name="status" value="received">
                    <button type="submit" class="bg-brand-text text-brand-bg text-[12px] font-bold uppercase tracking-wider py-3 px-6 rounded hover:bg-brand-text/95 transition-all">
                        ✓ Mark as Received (Restock Store)
                    </button>
                </form>

                <form action="/admin/purchase-orders/<?= $po['id'] ?>" method="POST" class="inline" onsubmit="return confirm('Cancel this purchase order?');">
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="bg-brand-darker text-brand-error border border-brand-border text-[12px] font-bold uppercase tracking-wider py-3 px-6 rounded hover:bg-brand-border/40 transition-all">
                        ✕ Cancel Purchase Order
                    </button>
                </form>
            </div>
        <?php elseif ($po['status'] === 'received'): ?>
            <div class="flex items-center gap-3 text-brand-success bg-brand-successBg border border-brand-success p-4 rounded text-[13px] font-semibold">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Stocking Completed. Item counts successfully added to variant inventory levels on <?= date('M d, Y H:i', strtotime($po['received_at'])) ?>.</span>
            </div>
        <?php elseif ($po['status'] === 'cancelled'): ?>
            <div class="flex items-center gap-3 text-brand-error bg-brand-errorBg border border-brand-error p-4 rounded text-[13px] font-semibold">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>This purchase order has been cancelled and will not replenish shop stock.</span>
            </div>
        <?php endif; ?>
    </div>
</div>

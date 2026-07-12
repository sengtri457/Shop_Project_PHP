<?php
$poResult = api_get('/purchase-orders');
$orders = $poResult['code'] === 200 ? $poResult['data'] : [];
?>

<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="font-serif text-[1.75rem] font-bold text-brand-text leading-tight">Purchase Orders (Restocking)</h1>
            <p class="text-[12.5px] text-brand-muted mt-1">Manage restocking requests and verify supplier arrivals to automatically update store inventory levels.</p>
        </div>
        <div>
            <a href="/admin/purchase-orders/new" class="inline-block bg-brand-text text-brand-bg text-[11px] font-bold uppercase tracking-widest py-3 px-5 rounded hover:bg-brand-text/95 transition-all text-center">
                + Create Purchase Order
            </a>
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

    <div class="bg-brand-bg border border-brand-border rounded-brand p-6">
        <h3 class="font-serif text-[1.2rem] font-semibold text-brand-text mb-5">Purchase Order Ledger</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-brand-border text-brand-muted text-[10px] uppercase tracking-wider">
                        <th class="py-3 px-3 font-semibold w-24">Order ID</th>
                        <th class="py-3 px-3 font-semibold">Supplier</th>
                        <th class="py-3 px-3 font-semibold">Total Cost</th>
                        <th class="py-3 px-3 font-semibold">Status</th>
                        <th class="py-3 px-3 font-semibold">Order Date</th>
                        <th class="py-3 px-3 font-semibold">Received Date</th>
                        <th class="py-3 px-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" class="py-8 text-center text-brand-muted text-[12.5px]">No purchase orders created yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): 
                            $statusColor = 'bg-brand-muted/10 text-brand-muted';
                            if ($o['status'] === 'ordered') {
                                $statusColor = 'bg-brand-accentLight text-brand-accent';
                            } elseif ($o['status'] === 'received') {
                                $statusColor = 'bg-brand-successBg text-brand-success';
                            } elseif ($o['status'] === 'cancelled') {
                                $statusColor = 'bg-brand-errorBg text-brand-error';
                            }
                        ?>
                            <tr class="text-[12.5px] text-brand-text hover:bg-brand-darker/40 transition-colors">
                                <td class="py-4 px-3 font-semibold">PO-#<?= $o['id'] ?></td>
                                <td class="py-4 px-3 font-semibold"><?= htmlspecialchars($o['supplier_name']) ?></td>
                                <td class="py-4 px-3 font-semibold">$<?= number_format($o['total_cost'], 2) ?></td>
                                <td class="py-4 px-3">
                                    <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded <?= $statusColor ?>">
                                        <?= htmlspecialchars($o['status']) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-3 text-brand-muted"><?= date('M d, Y H:i', strtotime($o['created_at'])) ?></td>
                                <td class="py-4 px-3 text-brand-muted">
                                    <?= $o['received_at'] ? date('M d, Y H:i', strtotime($o['received_at'])) : '<span class="text-brand-muted/50">—</span>' ?>
                                </td>
                                <td class="py-4 px-3 text-right">
                                    <div class="flex justify-end gap-3.5 items-center">
                                        <a href="/admin/purchase-orders/<?= $o['id'] ?>" class="text-[11px] font-bold uppercase tracking-wider text-brand-text hover:text-brand-accent transition-colors">Manage</a>
                                        
                                        <?php if ($o['status'] === 'draft' || $o['status'] === 'cancelled'): ?>
                                            <form action="/admin/purchase-orders?delete=<?= $o['id'] ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this purchase order?');" class="inline">
                                                <button type="submit" class="text-[11px] font-bold uppercase tracking-wider text-brand-error hover:underline bg-transparent border-0 p-0 cursor-pointer">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

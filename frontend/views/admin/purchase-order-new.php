<?php
$suppliersResult = api_get('/suppliers');
$suppliers = $suppliersResult['code'] === 200 ? $suppliersResult['data'] : [];

$productsResult = api_get('/products?limit=100');
$products = $productsResult['code'] === 200 ? ($productsResult['data']['data'] ?? []) : [];

// Gather all variants into a flat array for easy select options
$variantsList = [];
foreach ($products as $p) {
    foreach ($p['variants'] ?? [] as $v) {
        $attrs = json_decode($v['attributes'] ?? '{}', true);
        $attrStr = !empty($attrs) ? implode(' / ', $attrs) : '';
        $variantsList[] = [
            'id' => $v['id'],
            'sku' => $v['sku'],
            'name' => $p['name'] . ($attrStr ? " ($attrStr)" : ""),
            'price' => $p['base_price']
        ];
    }
}
?>

<div class="max-w-[1000px] mx-auto px-4 lg:px-8 py-8">
    <div class="mb-8">
        <a href="/admin/purchase-orders" class="text-[11px] font-bold uppercase tracking-wider text-brand-muted hover:text-brand-text mb-3 inline-block">&larr; Back to ledger</a>
        <h1 class="font-serif text-[1.75rem] font-bold text-brand-text leading-tight mt-1">Create Purchase Order</h1>
        <p class="text-[12.5px] text-brand-muted mt-1">Draft a restocking purchase request to be sent to a registered supplier.</p>
    </div>

    <!-- Error Flash Message -->
    <?php if (isset($_SESSION['_flash']['error'])): ?>
        <div class="mb-6 p-4 bg-brand-errorBg border border-brand-error text-brand-error text-[12.5px] rounded font-semibold">
            <?= htmlspecialchars($_SESSION['_flash']['error']) ?>
            <?php unset($_SESSION['_flash']['error']); ?>
        </div>
    <?php endif; ?>

    <form action="/admin/purchase-orders/new" method="POST" class="flex flex-col gap-6" onsubmit="return validateForm()">
        <div class="bg-brand-bg border border-brand-border rounded-brand p-6">
            <h3 class="font-serif text-[1.2rem] font-semibold text-brand-text mb-5">Supplier Details</h3>
            
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-1.5">Select Supplier *</label>
                <select name="supplier_id" required class="w-full bg-brand-darker border border-brand-border rounded px-3 py-2.5 text-[12.5px] text-brand-text focus:outline-none focus:border-brand-accent focus:bg-white transition-all">
                    <option value="">-- Choose Supplier --</option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['contact_name'] ?? 'No Contact') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="bg-brand-bg border border-brand-border rounded-brand p-6">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-serif text-[1.2rem] font-semibold text-brand-text">Order Items</h3>
                <button type="button" onclick="addRow()" class="bg-brand-darker text-brand-text border border-brand-border text-[10.5px] font-bold uppercase tracking-wider py-1.5 px-3 rounded hover:bg-brand-border/40 transition-all">
                    + Add Item Row
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="itemsTable">
                    <thead>
                        <tr class="border-b border-brand-border text-brand-muted text-[10px] uppercase tracking-wider">
                            <th class="py-3 px-2 font-semibold">Product Variant *</th>
                            <th class="py-3 px-2 font-semibold w-24">Quantity *</th>
                            <th class="py-3 px-2 font-semibold w-32">Unit Cost (USD) *</th>
                            <th class="py-3 px-2 text-right font-semibold w-16"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-border" id="itemsContainer">
                        <!-- Rows injected here via JS -->
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="py-8 text-center text-brand-muted text-[12.5px]">
                No items added yet. Click "Add Item Row" to list products.
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-brand-text text-brand-bg text-[12px] font-bold uppercase tracking-wider py-3 px-6 rounded hover:bg-brand-text/95 transition-all">
                Save & Create Draft
            </button>
            <a href="/admin/purchase-orders" class="bg-brand-darker text-brand-text border border-brand-border text-[12px] font-bold uppercase tracking-wider py-3 px-6 rounded hover:bg-brand-border/40 transition-all text-center">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
var variants = <?= json_encode($variantsList) ?>;

function addRow() {
    var container = document.getElementById('itemsContainer');
    var emptyState = document.getElementById('emptyState');
    emptyState.style.display = 'none';

    var tr = document.createElement('tr');
    tr.className = 'text-[12.5px] text-brand-text';

    var selectOptions = '<option value="">-- Select Variant --</option>';
    variants.forEach(function(v) {
        selectOptions += '<option value="' + v.id + '">' + escapeHtml(v.name) + ' [SKU: ' + escapeHtml(v.sku) + ']</option>';
    });

    tr.innerHTML = ' \
        <td class="py-3.5 px-2"> \
            <select name="variants[]" required class="w-full bg-brand-darker border border-brand-border rounded px-2 py-1.5 text-[12px] text-brand-text focus:outline-none focus:border-brand-accent focus:bg-white transition-all"> \
                ' + selectOptions + ' \
            </select> \
        </td> \
        <td class="py-3.5 px-2"> \
            <input type="number" name="quantities[]" min="1" required placeholder="Qty" class="w-full bg-brand-darker border border-brand-border rounded px-2 py-1.5 text-[12px] text-brand-text focus:outline-none focus:border-brand-accent focus:bg-white transition-all"> \
        </td> \
        <td class="py-3.5 px-2"> \
            <input type="number" name="costs[]" step="0.01" min="0" required placeholder="Cost" class="w-full bg-brand-darker border border-brand-border rounded px-2 py-1.5 text-[12px] text-brand-text focus:outline-none focus:border-brand-accent focus:bg-white transition-all"> \
        </td> \
        <td class="py-3.5 px-2 text-right"> \
            <button type="button" onclick="removeRow(this)" class="text-brand-error hover:underline text-[11px] font-bold uppercase tracking-wider bg-transparent border-0 cursor-pointer">Remove</button> \
        </td> \
    ';

    container.appendChild(tr);
}

function removeRow(btn) {
    var tr = btn.closest('tr');
    tr.remove();

    var container = document.getElementById('itemsContainer');
    if (container.children.length === 0) {
        document.getElementById('emptyState').style.display = 'block';
    }
}

function validateForm() {
    var container = document.getElementById('itemsContainer');
    if (container.children.length === 0) {
        alert('Please add at least one item row to the purchase order.');
        return false;
    }
    return true;
}

function escapeHtml(text) {
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Start with 1 row automatically
addRow();
</script>

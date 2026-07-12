<?php
$suppliersResult = api_get('/suppliers');
$suppliers = $suppliersResult['code'] === 200 ? $suppliersResult['data'] : [];

$editingSupplier = null;
if (isset($_GET['edit_id'])) {
    $editId = (int) $_GET['edit_id'];
    foreach ($suppliers as $s) {
        if ((int)$s['id'] === $editId) {
            $editingSupplier = $s;
            break;
        }
    }
}
?>

<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="font-serif text-[1.75rem] font-bold text-brand-text leading-tight">Supplier Management</h1>
            <p class="text-[12.5px] text-brand-muted mt-1">Register and maintain suppliers for product stock procurement.</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add/Edit Supplier Form -->
        <div class="lg:col-span-1">
            <div class="bg-brand-bg border border-brand-border rounded-brand p-6 sticky top-24">
                <h3 class="font-serif text-[1.2rem] font-semibold text-brand-text mb-5">
                    <?= $editingSupplier ? 'Edit Supplier' : 'Register New Supplier' ?>
                </h3>
                
                <form action="/admin/suppliers<?= $editingSupplier ? '?edit=' . $editingSupplier['id'] : '' ?>" method="POST" class="flex flex-col gap-4">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-1.5">Supplier Name *</label>
                        <input type="text" name="name" required 
                               value="<?= htmlspecialchars($editingSupplier['name'] ?? '') ?>" 
                               placeholder="e.g. Nike Distribution Center"
                               class="w-full bg-brand-darker border border-brand-border rounded px-3 py-2 text-[12.5px] text-brand-text focus:outline-none focus:border-brand-accent focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-1.5">Contact Person</label>
                        <input type="text" name="contact_name" 
                               value="<?= htmlspecialchars($editingSupplier['contact_name'] ?? '') ?>" 
                               placeholder="e.g. Michael Jordan"
                               class="w-full bg-brand-darker border border-brand-border rounded px-3 py-2 text-[12.5px] text-brand-text focus:outline-none focus:border-brand-accent focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-1.5">Email Address</label>
                        <input type="email" name="email" 
                               value="<?= htmlspecialchars($editingSupplier['email'] ?? '') ?>" 
                               placeholder="e.g. orders@supplier.com"
                               class="w-full bg-brand-darker border border-brand-border rounded px-3 py-2 text-[12.5px] text-brand-text focus:outline-none focus:border-brand-accent focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-1.5">Phone Number</label>
                        <input type="text" name="phone" 
                               value="<?= htmlspecialchars($editingSupplier['phone'] ?? '') ?>" 
                               placeholder="e.g. +1 (555) 019-9023"
                               class="w-full bg-brand-darker border border-brand-border rounded px-3 py-2 text-[12.5px] text-brand-text focus:outline-none focus:border-brand-accent focus:bg-white transition-all">
                    </div>

                    <div class="mt-2 flex gap-3">
                        <button type="submit" class="flex-1 bg-brand-text text-brand-bg text-[12px] font-bold uppercase tracking-wider py-2.5 px-4 rounded hover:bg-brand-text/95 transition-all text-center">
                            <?= $editingSupplier ? 'Save Changes' : 'Register Supplier' ?>
                        </button>
                        <?php if ($editingSupplier): ?>
                            <a href="/admin/suppliers" class="bg-brand-darker text-brand-text border border-brand-border text-[12px] font-bold uppercase tracking-wider py-2.5 px-4 rounded hover:bg-brand-border/40 transition-all text-center">
                                Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Suppliers List -->
        <div class="lg:col-span-2">
            <div class="bg-brand-bg border border-brand-border rounded-brand p-6">
                <h3 class="font-serif text-[1.2rem] font-semibold text-brand-text mb-5">All Registered Suppliers</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-brand-border text-brand-muted text-[10px] uppercase tracking-wider">
                                <th class="py-3 px-3 font-semibold w-12">ID</th>
                                <th class="py-3 px-3 font-semibold">Name / Contact</th>
                                <th class="py-3 px-3 font-semibold">Email</th>
                                <th class="py-3 px-3 font-semibold">Phone</th>
                                <th class="py-3 px-3 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-border">
                            <?php if (empty($suppliers)): ?>
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-brand-muted text-[12.5px]">No suppliers registered yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($suppliers as $s): ?>
                                    <tr class="text-[12.5px] text-brand-text hover:bg-brand-darker/40 transition-colors">
                                        <td class="py-4 px-3 font-semibold">#<?= $s['id'] ?></td>
                                        <td class="py-4 px-3">
                                            <div class="font-semibold text-brand-text"><?= htmlspecialchars($s['name']) ?></div>
                                            <div class="text-[10.5px] text-brand-muted mt-0.5"><?= htmlspecialchars($s['contact_name'] ?? 'No contact name') ?></div>
                                        </td>
                                        <td class="py-4 px-3 text-brand-muted"><?= htmlspecialchars($s['email'] ?? '—') ?></td>
                                        <td class="py-4 px-3 text-brand-muted"><?= htmlspecialchars($s['phone'] ?? '—') ?></td>
                                        <td class="py-4 px-3 text-right">
                                            <div class="flex justify-end gap-3.5">
                                                <a href="/admin/suppliers?edit_id=<?= $s['id'] ?>" class="text-[11px] font-bold uppercase tracking-wider text-brand-text hover:text-brand-accent transition-colors">Edit</a>
                                                
                                                <form action="/admin/suppliers?delete=<?= $s['id'] ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this supplier?');" class="inline">
                                                    <button type="submit" class="text-[11px] font-bold uppercase tracking-wider text-brand-error hover:underline bg-transparent border-0 p-0 cursor-pointer">Delete</button>
                                                </form>
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
    </div>
</div>

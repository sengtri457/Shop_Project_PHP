<?php
$favIds = isset($_GET['ids']) ? array_filter(array_map('intval', explode(',', $_GET['ids']))) : [];

$products = [];
if (!empty($favIds)) {
    $result = api_get('/products?limit=100');
    $allProducts = $result['data']['data'] ?? [];
    
    $products = array_filter($allProducts, function($p) use ($favIds) {
        return in_array((int)$p['id'], $favIds);
    });
}
?>

<div class="max-w-[1280px] mx-auto px-8 pt-12 pb-24">
    <div class="mb-10 text-center">
        <h1 class="font-serif text-[2rem] font-medium text-brand-text mb-2">My Favorites</h1>
        <p class="text-[12.5px] text-brand-muted">Your curated list of pieces you love.</p>
    </div>

    <?php if (empty($products)): ?>
        <div class="text-center py-20 bg-brand-darker rounded-brand border border-brand-border flex flex-col items-center justify-center p-6">
            <div class="w-12 h-12 rounded-full bg-brand-accentLight text-brand-accent flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </div>
            <h3 class="font-sans text-[15px] font-semibold text-brand-text mb-1">Your wishlist is empty</h3>
            <p class="text-[12px] text-brand-muted mb-6 max-w-xs leading-relaxed">Save items you like here to keep track of them and easily add to your bag later.</p>
            <a href="/products" class="inline-block bg-brand-text text-brand-bg text-[11px] font-bold uppercase tracking-widest py-3 px-6 rounded hover:bg-brand-text/95 transition-all">
                Shop All Products
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-7">
            <?php foreach ($products as $product):
                $prodImages = split_image_urls($product['images'] ?? '');
                if (empty($prodImages) && !empty($product['variants'])) {
                    foreach ($product['variants'] as $v) {
                        if (!empty($v['image_url'])) {
                            $prodImages[] = $v['image_url'];
                            break;
                        }
                    }
                }
                $mainImg = !empty($prodImages) ? asset_url($prodImages[0]) : '/assets/images/hero_banner.png';
                $discountPercent = (int) ($product['discount_percent'] ?? 0);
            ?>
                <div class="flex flex-col bg-transparent rounded-brand group transition-all duration-300 relative">
                    <!-- Favorite Toggle Button Overlay -->
                    <button onclick="toggleFav(<?= $product['id'] ?>, event)" class="fav-btn absolute top-3.5 right-3.5 z-20 w-8 h-8 rounded-full bg-white/95 border border-brand-border flex items-center justify-center hover:scale-105 transition-transform shadow-[0_2px_8px_rgba(0,0,0,0.04)] focus:outline-none" data-fav-id="<?= $product['id'] ?>">
                        <svg class="w-4 h-4 text-brand-error fill-current" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                        </svg>
                    </button>

                    <a href="/products/<?= $product['id'] ?>" class="flex flex-col h-full">
                        <div class="w-full aspect-[3/4] overflow-hidden rounded-brand bg-brand-darker mb-3 relative border border-brand-border">
                            <img src="<?= htmlspecialchars($mainImg) ?>" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-102" alt="<?= htmlspecialchars($product['name']) ?>">
                        </div>
                        <?php if (!empty($product['brand'])): ?>
                            <span class="font-sans text-[10px] font-semibold uppercase tracking-wider text-brand-muted mb-1"><?= htmlspecialchars($product['brand']) ?></span>
                        <?php endif; ?>
                        <h3 class="font-sans text-[14px] font-medium leading-relaxed mb-1.5 text-left text-brand-text"><?= htmlspecialchars($product['name']) ?></h3>
                        <?php if ($discountPercent > 0): ?>
                            <p class="font-sans text-[14px] font-semibold text-brand-accent text-left flex gap-2 items-center mt-auto">
                                <span class="text-brand-error">
                                    $<?= number_format($product['base_price'] * (1 - $discountPercent / 100), 2) ?>
                                </span>
                                <span class="text-[11px] font-semibold text-brand-error bg-[#fee8e6] px-1 py-0.5 rounded-[3px]">
                                    -<?= $discountPercent ?>%
                                </span>
                                <span class="text-brand-muted line-through text-[12px] font-normal">
                                    $<?= number_format($product['base_price'], 2) ?>
                                </span>
                            </p>
                        <?php else: ?>
                            <p class="font-sans text-[14px] font-semibold text-brand-accent text-left mt-auto">$<?= number_format($product['base_price'], 2) ?></p>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    // Redirect logic to pass LocalStorage favorites to server-side PHP
    (function() {
        const favs = JSON.parse(localStorage.getItem('fav_products') || '[]');
        const idsParam = favs.join(',');
        const urlParams = new URLSearchParams(window.location.search);
        const currentIds = urlParams.get('ids') || '';
        
        if (currentIds !== idsParam) {
            const targetUrl = '/favorites' + (favs.length > 0 ? '?ids=' + idsParam : '');
            if (window.navigateTo) {
                window.navigateTo(targetUrl);
            } else {
                window.location.href = targetUrl;
            }
        }
    })();
</script>

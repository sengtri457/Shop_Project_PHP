<?php
function getCategoryImage(string $name): string {
    $name = strtolower(trim($name));
    $map = [
        'shoes'       => 'https://static.nike.com/a/images/f_auto/dpr_1.2,cs_srgb/h_1198,c_limit/f5100f5d-1df6-4558-b407-701bbbfc571f/nike-just-do-it.jpg',
        'T-Shirt'     => 'https://static.nike.com/a/images/f_auto/dpr_1.2,cs_srgb/w_1600,c_limit/e5fc5c02-1940-4003-985e-603f98881351/nike-just-do-it.jpg',
    ];
    
    foreach ($map as $key => $url) {
        if (strpos($name, $key) !== false) {
            return $url;
        }
    }
    return 'https://static.nike.com/a/images/f_auto/dpr_1.2,cs_srgb/w_1600,c_limit/e5fc5c02-1940-4003-985e-603f98881351/nike-just-do-it.jpg';
}

function getColorHex(string $colorName): string {
    $c = strtolower(trim($colorName));
    $map = [
        'black'  => '#1A1A1A',
        'white'  => '#FFFFFF',
        'red'    => '#E53E3E',
        'blue'   => '#3182CE',
        'navy'   => '#1A202C',
        'green'  => '#38A169',
        'pink'   => '#ED64A6',
        'beige'  => '#E6D5C3',
        'cream'  => '#F5F5DC',
        'brown'  => '#8B4513',
        'grey'   => '#A0AEC0',
        'gray'   => '#A0AEC0',
        'yellow' => '#D69E2E',
    ];
    return $map[$c] ?? '#CBD5E0';
}
?>

<style>
@keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.animate-marquee {
    display: flex;
    width: max-content;
    animation: marquee 25s linear infinite;
}
.animate-marquee:hover {
    animation-play-state: paused;
}
.scrollbar-none::-webkit-scrollbar {
    display: none;
}
.scrollbar-none {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

<div class="flex items-center justify-start py-12 px-6 sm:px-12 bg-brand-darker bg-no-repeat bg-cover bg-center rounded-brand mb-10 mx-4 sm:mx-6 min-h-[380px] border border-brand-border" style="background-image: linear-gradient(to right, rgba(250,250,248,0.92) 35%, rgba(7, 196, 189, 0.1) 100%), url('https://static.nike.com/a/images/f_auto/dpr_1.2,cs_srgb/w_1600,c_limit/ec45aa51-06a0-40ef-9e5b-acefe2b618bf/nike-just-do-it.jpg');">
    <div class="max-w-[500px] flex flex-col items-start">
        <h1 class="font-serif text-[2.2rem] sm:text-[2.6rem] font-medium leading-[1.15] mb-3 text-brand-text">
            <?php if (is_logged_in()): ?>
                Curated Style for <?= htmlspecialchars($_SESSION['customer']['name'] ?? '') ?>
            <?php else: ?>
                Minimalism in Everyday Wear
            <?php endif; ?>
        </h1>
        <p class="text-[0.95rem] text-brand-muted mb-5 leading-relaxed">A collection of premium, high-quality linen and cotton essentials designed for modern comfort and timeless style.</p>
        <a href="/products" class="inline-flex items-center justify-center px-7 py-3 bg-brand-text text-brand-bg hover:bg-brand-accent border border-brand-text rounded-brand font-sans text-[11px] font-semibold tracking-widest uppercase transition-all duration-300 transform hover:-translate-y-px">Shop the Collection</a>
    </div>
</div>

<!-- Brand Logo Slider -->
<div class="w-full overflow-hidden mb-6 py-4 bg-brand-bg relative flex items-center">
    <!-- Fade overlays on left/right for smooth transition -->
    <div class="absolute left-0 top-0 bottom-0 w-16 bg-gradient-to-r from-brand-bg to-transparent z-10 pointer-events-none"></div>
    <div class="absolute right-0 top-0 bottom-0 w-16 bg-gradient-to-l from-brand-bg to-transparent z-10 pointer-events-none"></div>

    <div class="animate-marquee flex gap-20 items-center">
        <!-- Set 1 -->
        <div class="flex gap-20 items-center shrink-0">
            <span class="font-sans font-black italic tracking-tighter text-[17px] text-brand-text/35 hover:text-brand-accent transition-colors">NIKE</span>
            <span class="font-sans font-bold text-[16px] lowercase tracking-tight text-brand-text/35 hover:text-brand-accent transition-colors">adidas</span>
            <span class="font-sans font-black tracking-widest text-[16px] uppercase text-brand-text/35 hover:text-brand-accent transition-colors">PUMA</span>
            <span class="font-serif font-semibold italic text-[16px] text-brand-text/35 hover:text-brand-accent transition-colors">Reebok</span>
            <span class="font-sans font-extrabold tracking-wide text-[15px] uppercase text-brand-text/35 hover:text-brand-accent transition-colors">CONVERSE</span>
            <span class="font-sans font-black tracking-tight text-[17px] uppercase text-brand-text/35 hover:text-brand-accent transition-colors">VANS</span>
            <span class="font-sans font-extrabold tracking-widest text-[16px] uppercase text-brand-text/35 hover:text-brand-accent transition-colors">FILA</span>
        </div>
        <!-- Set 2 (Duplicate for Seamless Loop) -->
        <div class="flex gap-20 items-center shrink-0">
            <span class="font-sans font-black italic tracking-tighter text-[17px] text-brand-text/35 hover:text-brand-accent transition-colors">NIKE</span>
            <span class="font-sans font-bold text-[16px] lowercase tracking-tight text-brand-text/35 hover:text-brand-accent transition-colors">adidas</span>
            <span class="font-sans font-black tracking-widest text-[16px] uppercase text-brand-text/35 hover:text-brand-accent transition-colors">PUMA</span>
            <span class="font-serif font-semibold italic text-[16px] text-brand-text/35 hover:text-brand-accent transition-colors">Reebok</span>
            <span class="font-sans font-extrabold tracking-wide text-[15px] uppercase text-brand-text/35 hover:text-brand-accent transition-colors">CONVERSE</span>
            <span class="font-sans font-black tracking-tight text-[17px] uppercase text-brand-text/35 hover:text-brand-accent transition-colors">VANS</span>
            <span class="font-sans font-extrabold tracking-widest text-[16px] uppercase text-brand-text/35 hover:text-brand-accent transition-colors">FILA</span>
        </div>
    </div>
</div>


<div class="mb-10 px-4 sm:px-6">
    <h2 class="font-serif text-[1.6rem] font-medium mb-4 text-left relative pb-2 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-10 after:h-[2px] after:bg-brand-accent">Shop by Section</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <a href="/products?gender=men" class="flex flex-col bg-transparent rounded-brand overflow-hidden group transition-all duration-300">
            <div class="w-full aspect-[3/4] overflow-hidden rounded-brand bg-brand-darker mb-3 relative border border-brand-border">
                <img src="https://images.unsplash.com/photo-1488161628813-04466f872be2?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" alt="Men">
            </div>
            <h3 class="font-sans text-[13px] font-semibold uppercase tracking-wider mt-1 text-left text-brand-text">Men</h3>
        </a>
        <a href="/products?gender=women" class="flex flex-col bg-transparent rounded-brand overflow-hidden group transition-all duration-300">
            <div class="w-full aspect-[3/4] overflow-hidden rounded-brand bg-brand-darker mb-3 relative border border-brand-border">
                <img src="https://images.unsplash.com/photo-1509631179647-0177331693ae?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" alt="Women">
            </div>
            <h3 class="font-sans text-[13px] font-semibold uppercase tracking-wider mt-1 text-left text-brand-text">Women</h3>
        </a>
        <a href="/products?gender=kids" class="flex flex-col bg-transparent rounded-brand overflow-hidden group transition-all duration-300">
            <div class="w-full aspect-[3/4] overflow-hidden rounded-brand bg-brand-darker mb-3 relative border border-brand-border">
                <img src="https://images.unsplash.com/photo-1519457431-44ccd64a579b?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" alt="Kids">
            </div>
            <h3 class="font-sans text-[13px] font-semibold uppercase tracking-wider mt-1 text-left text-brand-text">Kids</h3>
        </a>
    </div>
</div>

<div class="mb-10 px-4 sm:px-6">
    <h2 class="font-serif text-[1.6rem] font-medium mb-4 text-left relative pb-2 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-10 after:h-[2px] after:bg-brand-accent">Shop by Category</h2>
    <?php
    $result = api_get('/categories');
    $categories = array_slice($result['data'] ?? [], 0, 5);
    ?>
    <?php if (empty($categories)): ?>
        <p class="text-brand-muted">No categories yet.</p>
    <?php else: ?>
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 lg:grid-rows-2 lg:gap-5 lg:h-[460px]">
            <?php foreach ($categories as $index => $cat): 
                $gridClass = '';
                if ($index === 0) {
                    // Leftmost Tall Column (Col 1, Rows 1-2)
                    $gridClass = 'col-span-1 row-span-2 h-[340px] lg:h-full lg:col-start-1 lg:row-start-1';
                } elseif ($index === 1) {
                    // Middle Top Wide Card (Cols 2-3, Row 1)
                    $gridClass = 'col-span-2 row-span-1 h-[160px] lg:h-full lg:col-start-2 lg:row-start-1';
                } elseif ($index === 2) {
                    // Middle Bottom Left Card (Col 2, Row 2)
                    $gridClass = 'col-span-1 row-span-1 h-[160px] lg:h-full lg:col-start-2 lg:row-start-2';
                } elseif ($index === 3) {
                    // Middle Bottom Right Card (Col 3, Row 2)
                    $gridClass = 'col-span-1 row-span-1 h-[160px] lg:h-full lg:col-start-3 lg:row-start-2';
                } elseif ($index === 4) {
                    // Rightmost Tall Column (Col 4, Rows 1-2)
                    $gridClass = 'col-span-2 lg:col-span-1 row-span-1 lg:row-span-2 h-[180px] lg:h-full lg:col-start-4 lg:row-start-1';
                }
            ?>
                <a href="/products?category_id=<?= $cat['id'] ?>" class="<?= $gridClass ?> relative overflow-hidden rounded-brand border border-brand-border group transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/45 via-transparent to-transparent z-10"></div>
                    <img src="<?= getCategoryImage($cat['name']) ?>" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" alt="<?= htmlspecialchars($cat['name']) ?>">
                    <div class="absolute bottom-4 left-4 z-20 backdrop-blur-md bg-white/80 border border-white/30 text-brand-text text-[11px] font-bold uppercase tracking-widest px-4.5 py-2.5 rounded-full shadow-[0_8px_16px_rgba(0,0,0,0.06)] group-hover:bg-brand-text group-hover:text-brand-bg group-hover:border-transparent transition-all duration-300">
                        <?= htmlspecialchars($cat['name']) ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Most Seller Products Section (Clean & Dynamic UI) -->
<?php
$bestSellersRes = api_get('/products/best-sellers?limit=10');
$bestSellers = [];
if (isset($bestSellersRes['data']['data']) && is_array($bestSellersRes['data']['data'])) {
    $bestSellers = $bestSellersRes['data']['data'];
} elseif (isset($bestSellersRes['data']) && is_array($bestSellersRes['data'])) {
    $bestSellers = $bestSellersRes['data'];
}
?>
<?php if (!empty($bestSellers)): ?>
<div class="mb-10 relative group/carousel px-4 sm:px-6">
    <!-- Section Header -->
    <div class="flex items-center justify-between mb-4 pb-2 relative after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-10 after:h-[2px] after:bg-brand-accent">
        <h2 class="font-serif text-[1.6rem] font-medium text-brand-text">
            Most Selling Products
        </h2>
        <a href="/products" class="text-[11px] font-bold uppercase tracking-widest text-brand-text hover:text-brand-accent transition-colors flex items-center gap-1.5">
            SEE MORE <i class="fa-solid fa-chevron-right text-[10px]"></i>
        </a>
    </div>

    <!-- Scroll Controls -->
    <button onclick="scrollBestSellers(-1)" aria-label="Previous Products" class="hidden md:flex absolute left-6 top-[55%] -translate-y-1/2 z-30 w-10 h-10 rounded-full bg-white/95 border border-brand-border text-brand-text shadow-lg items-center justify-center hover:bg-brand-text hover:text-white transition-all opacity-0 group-hover/carousel:opacity-100 focus:outline-none">
        <i class="fa-solid fa-chevron-left text-xs"></i>
    </button>
    <button onclick="scrollBestSellers(1)" aria-label="Next Products" class="hidden md:flex absolute right-6 top-[55%] -translate-y-1/2 z-30 w-10 h-10 rounded-full bg-white/95 border border-brand-border text-brand-text shadow-lg items-center justify-center hover:bg-brand-text hover:text-white transition-all opacity-0 group-hover/carousel:opacity-100 focus:outline-none">
        <i class="fa-solid fa-chevron-right text-xs"></i>
    </button>

    <!-- Horizontal Scrollable Cards Grid -->
    <div id="bestsellers-scroll" class="flex gap-5 overflow-x-auto scrollbar-none scroll-smooth pb-4 px-1 -mx-1">
        <?php 
        $rankCounter = 1;
        foreach ($bestSellers as $product):
            if (!is_array($product) || empty($product['id'])) continue;
            $rankNumber = $rankCounter++;
            $prodImages = split_image_urls($product['images'] ?? '');
            if (empty($prodImages) && !empty($product['variants']) && is_array($product['variants'])) {
                foreach ($product['variants'] as $v) {
                    if (!empty($v['image_url'])) {
                        $prodImages[] = $v['image_url'];
                        break;
                    }
                }
            }
            $mainImg = !empty($prodImages) ? asset_url($prodImages[0]) : '/assets/images/hero_banner.png';
            $discountPercent = (int) ($product['discount_percent'] ?? 0);
            $basePrice = (float) ($product['base_price'] ?? 0);
            $salePrice = $discountPercent > 0 ? $basePrice * (1 - $discountPercent / 100) : $basePrice;
            $soldQty = (int) ($product['total_sold'] ?? 0);
            
            // Extract colors from variants
            $colors = [];
            if (!empty($product['variants'])) {
                foreach ($product['variants'] as $v) {
                    if (!empty($v['color']) && !in_array($v['color'], $colors)) {
                        $colors[] = $v['color'];
                    }
                }
            }
        ?>
            <div class="min-w-[240px] sm:min-w-[260px] max-w-[260px] flex-shrink-0 flex flex-col bg-white rounded-lg border border-brand-border/70 overflow-hidden group hover:shadow-xl transition-all duration-300 relative">
                
                <!-- Badges Overlay (Top Left: Rank, Discount & Units Sold) -->
                <div class="absolute top-3 left-3 z-20 flex gap-1.5 items-center flex-wrap max-w-[90%] pointer-events-none">
                    <span class="bg-brand-text text-white text-[10px] font-extrabold px-2 py-0.5 rounded-[3px] shadow-sm">
                        #<?= $rankNumber ?>
                    </span>
                    <?php if ($discountPercent > 0): ?>
                        <span class="bg-[#e53e3e] text-white text-[11px] font-bold px-2 py-0.5 rounded-[3px] shadow-sm">
                            -<?= $discountPercent ?>%
                        </span>
                    <?php endif; ?>
                    <?php if ($soldQty > 0): ?>
                        <span class="bg-black/75 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded-[3px]">
                            🔥 <?= $soldQty ?> <?= $soldQty === 1 ? 'unit' : 'units' ?> sold
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Product Image Container -->
                <a href="/products/<?= (int)($product['id'] ?? 0) ?>" class="block w-full aspect-[4/5] bg-brand-darker relative overflow-hidden">
                    <img src="<?= htmlspecialchars((string)($mainImg ?? '')) ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="<?= htmlspecialchars((string)($product['name'] ?? '')) ?>">
                    
                    <!-- Quick View Overlay -->
                    <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <button type="button" onclick="openQuickView(<?= (int)($product['id'] ?? 0) ?>, event)" class="bg-white/95 text-brand-text hover:bg-brand-text hover:text-white text-[10px] font-bold uppercase tracking-widest py-2 px-4 rounded shadow-md transition-all">
                            Quick View
                        </button>
                    </div>
                </a>

                <!-- Card Details Body -->
                <div class="p-3.5 flex flex-col flex-1 justify-between">
                    <div>
                        <!-- Price & Heart Wishlist Row -->
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-baseline gap-1.5 flex-wrap">
                                <?php if ($discountPercent > 0): ?>
                                    <span class="font-sans text-[15px] font-bold text-[#e53e3e]">
                                        US $<?= number_format($salePrice, 2) ?>
                                    </span>
                                    <span class="font-sans text-[11px] text-brand-muted line-through font-normal">
                                        US $<?= number_format($basePrice, 2) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="font-sans text-[15px] font-bold text-brand-text">
                                        US $<?= number_format($basePrice, 2) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Heart Icon -->
                            <button onclick="toggleFav(<?= (int)($product['id'] ?? 0) ?>, event)" class="fav-btn text-brand-muted hover:text-brand-error transition-colors focus:outline-none p-1" data-fav-id="<?= (int)($product['id'] ?? 0) ?>" aria-label="Favorite">
                                <svg class="w-4 h-4" viewBox="0 0 24 24">
                                    <path fill="none" stroke="currentColor" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Product Title -->
                        <a href="/products/<?= (int)($product['id'] ?? 0) ?>" class="block">
                            <h3 class="font-sans text-[13px] font-medium text-brand-text truncate hover:text-brand-accent transition-colors">
                                <?= htmlspecialchars((string)($product['name'] ?? '')) ?>
                            </h3>
                        </a>
                    </div>

                    <!-- Color Swatches -->
                    <?php if (!empty($colors)): ?>
                        <div class="flex gap-1.5 items-center mt-2.5">
                            <?php foreach (array_slice($colors, 0, 5) as $c): ?>
                                <span class="w-3 h-3 rounded-full border border-black/15 shadow-inner inline-block" style="background-color: <?= getColorHex((string)$c) ?>;" title="<?= htmlspecialchars((string)$c) ?>"></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function scrollBestSellers(dir) {
    const container = document.getElementById('bestsellers-scroll');
    if (container) {
        container.scrollBy({ left: dir * 300, behavior: 'smooth' });
    }
}
</script>
<?php endif; ?>

<div class="mb-10 px-4 sm:px-6">
    <h2 class="font-serif text-[1.6rem] font-medium mb-4 text-left relative pb-2 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-10 after:h-[2px] after:bg-brand-accent">New Arrivals</h2>
    <?php
    $result = api_get('/products?limit=8&sort_by=created_at&sort_order=desc');
    $products = $result['data']['data'] ?? [];
    ?>
    <?php if (empty($products)): ?>
        <p class="text-brand-muted">No products yet.</p>
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
                $basePrice = (float) ($product['base_price'] ?? 0);
            ?>
                <div class="flex flex-col bg-transparent rounded-brand group transition-all duration-300 relative">
                    <!-- Favorite Toggle Button Overlay -->
                    <button onclick="toggleFav(<?= (int)($product['id'] ?? 0) ?>, event)" class="fav-btn absolute top-3.5 right-3.5 z-20 w-8 h-8 rounded-full bg-white/95 border border-brand-border flex items-center justify-center hover:scale-105 transition-transform shadow-[0_2px_8px_rgba(0,0,0,0.04)] focus:outline-none" data-fav-id="<?= (int)($product['id'] ?? 0) ?>">
                        <svg class="w-4 h-4 text-brand-muted hover:text-brand-error transition-colors" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                        </svg>
                    </button>

                    <a href="/products/<?= (int)($product['id'] ?? 0) ?>" class="flex flex-col h-full">
                        <div class="w-full aspect-[3/4] overflow-hidden rounded-brand bg-brand-darker mb-3 relative border border-brand-border">
                            <img src="<?= htmlspecialchars((string)($mainImg ?? '')) ?>" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105" alt="<?= htmlspecialchars((string)($product['name'] ?? '')) ?>">
                            
                            <!-- Quick View Overlay -->
                            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center pointer-events-none">
                                <button type="button" onclick="openQuickView(<?= (int)($product['id'] ?? 0) ?>, event)" class="pointer-events-auto bg-brand-bg/95 hover:bg-brand-bg text-brand-text text-[10px] font-bold uppercase tracking-widest py-2.5 px-5 rounded shadow-lg transition-all translate-y-3 group-hover:translate-y-0 duration-300">
                                    Quick View
                                </button>
                            </div>
                        </div>
                        <?php if (!empty($product['brand'])): ?>
                            <span class="font-sans text-[10px] font-semibold uppercase tracking-wider text-brand-muted mb-1"><?= htmlspecialchars((string)($product['brand'] ?? '')) ?></span>
                        <?php endif; ?>
                        <h3 class="font-sans text-[14px] font-medium leading-relaxed mb-1.5 text-left text-brand-text"><?= htmlspecialchars((string)($product['name'] ?? '')) ?></h3>
                        <?php if ($discountPercent > 0): ?>
                            <p class="font-sans text-[14px] font-semibold text-brand-accent text-left flex gap-2 items-center mt-auto">
                                <span class="text-brand-error">
                                    $<?= number_format($basePrice * (1 - $discountPercent / 100), 2) ?>
                                </span>
                                <span class="text-[11px] font-semibold text-brand-error bg-[#fee8e6] px-1 py-0.5 rounded-[3px]">
                                    -<?= $discountPercent ?>%
                                </span>
                                <span class="text-brand-muted line-through text-[12px] font-normal">
                                    $<?= number_format($basePrice, 2) ?>
                                </span>
                            </p>
                        <?php else: ?>
                            <p class="font-sans text-[14px] font-semibold text-brand-accent text-left mt-auto">$<?= number_format($basePrice, 2) ?></p>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Immersive Video Campaign Banner -->
<div class="px-4 sm:px-6 mb-10">
    <div class="w-full aspect-[21/9] md:aspect-[21/8] max-h-[380px] overflow-hidden rounded-brand relative border border-brand-border group">
    <video autoplay muted loop playsinline class="w-full h-full object-cover scale-[1.01] transition-transform duration-[1200ms] group-hover:scale-105">
        <source src="<?= asset_url('/assets/images/hm.webm') ?>" type="video/webm">
        Your browser does not support the video tag.
    </video>
    <!-- Dark overlay for contrast -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/15 to-transparent flex flex-col justify-end p-6 md:p-8">
        <div class="max-w-md page-fade">
            <span class="text-[10.5px] font-bold tracking-widest text-[#fee8e6] uppercase block mb-1.5">Exclusive Campaign</span>
            <h2 class="font-serif text-[1.5rem] md:text-[1.85rem] font-medium text-white leading-tight mb-2">Redefining Active Style</h2>
            <p class="text-[12px] text-white/80 leading-relaxed mb-4 max-w-sm">Experience maximum comfort and performance engineered for daily movement.</p>
            <a href="/products" class="inline-block bg-white text-black text-[11px] font-bold uppercase tracking-wider px-5 py-2.5 rounded hover:bg-black hover:text-white border border-white transition-all duration-300">
                Explore Collection &rarr;
            </a>
        </div>
    </div>
</div>
</div>

<!-- Latest In Clothing Category Bar -->
<div class="px-4 sm:px-6 mb-12">
    <h2 class="font-serif text-[1.6rem] font-medium mb-6 text-left relative pb-2 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-10 after:h-[2px] after:bg-brand-accent">Latest in Clothing</h2>

    <!-- Category Items Grid -->
    <div class="flex items-start justify-start md:justify-center gap-4 sm:gap-8 overflow-x-auto scrollbar-none py-2 px-1">
        <?php
        $latestClothingCategories = [
            [
                'name' => 'Fan Gear',
                'url' => '/products?search=fan+gear',
                'img' => 'https://static.nike.com/a/images/w_144,c_limit/7975a172-7036-46a9-857c-8985a1e36dcb/image.png'
            ],
            [
                'name' => 'All Clothing',
                'url' => '/products',
                'img' => 'https://static.nike.com/a/images/w_144,c_limit/6ac8e770-faf6-4cb7-bdee-88831514cfa5/image.png'
            ],
            [
                'name' => 'Hoodies & Sweatshirts',
                'url' => '/products?search=hoodie',
                'img' => 'https://static.nike.com/a/images/w_144,c_limit/32f81668-f255-45ff-8c28-da6a4a11bade/image.png'
            ],
            [
                'name' => 'Jackets & Vests',
                'url' => '/products?search=jacket',
                'img' => 'https://static.nike.com/a/images/w_144,c_limit/5aa267b2-409b-421d-bc5f-e0f035a1b074/image.png'
            ],
            [
                'name' => 'ACG',
                'url' => '/products?search=acg',
                'img' => 'https://static.nike.com/a/images/w_144,c_limit/4935136a-59df-4502-9e4f-887b5f5ce58b/image.png'
            ],
            [
                'name' => 'Pants',
                'url' => '/products?search=pants',
                'img' => 'https://static.nike.com/a/images/w_144,c_limit/007b0190-f6d7-4af3-8353-3d993c2f6559/image.png'
            ],
            [
                'name' => 'Shorts',
                'url' => '/products?search=shorts',
                'img' => 'https://static.nike.com/a/images/w_144,c_limit/ba9f9c2c-9a7b-417c-a302-a83966a880ca/image.png'
            ],
            [
                'name' => 'Tops & T-Shirts',
                'url' => '/products?category_id=2',
                'img' => 'https://static.nike.com/a/images/w_144,c_limit/7975a172-7036-46a9-857c-8985a1e36dcb/image.png'
            ],
        ];
        foreach ($latestClothingCategories as $catItem):
        ?>
            <a href="<?= $catItem['url'] ?>" class="flex flex-col items-center group text-center cursor-pointer min-w-[85px] max-w-[105px] shrink-0">
                <!-- Image Container -->
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-brand-darker border border-brand-border flex items-center justify-center mb-2.5 overflow-hidden group-hover:border-brand-accent group-hover:scale-105 group-hover:shadow-md transition-all duration-300">
                    <img src="<?= asset_url($catItem['img']) ?>" 
                         onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236A6A65\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\'/></svg>';" 
                         alt="<?= htmlspecialchars($catItem['name']) ?>" 
                         class="w-12 h-12 sm:w-14 sm:h-14 object-contain transition-transform duration-300 group-hover:scale-110">
                </div>
                <!-- Label -->
                <span class="font-sans text-[11px] font-bold text-brand-text group-hover:text-brand-accent transition-colors leading-tight">
                    <?= htmlspecialchars($catItem['name']) ?>
                </span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Sticky Editorial Campaign Section -->
<div class="px-4 sm:px-6 mb-16 mt-16 max-w-[1280px] mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- LEFT COLUMN: Guides & Styling (Col 1-4) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Card 1: Samba Size Guide -->
            <div class="bg-brand-bg border border-brand-border/60 rounded-brand overflow-hidden group hover:shadow-soft transition-all duration-300">
                <div class="w-full aspect-[3/2] overflow-hidden bg-brand-darker relative border-b border-brand-border/40">
                    <img src="https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=800&auto=format&fit=crop&q=80" alt="Samba Size Guide" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                </div>
                <div class="p-5">
                    <h3 class="font-serif text-base font-semibold text-brand-text mb-2">The adidas Samba Size Guide</h3>
                    <p class="text-xs text-brand-muted leading-relaxed font-light">Tired of asking are Sambas true to size? Check out our official adidas Samba size chart for all you need to find your perfect Samba style and fit today.</p>
                </div>
            </div>

            <!-- Card 2: How To Style A Soccer Jersey -->
            <div class="bg-brand-bg border border-brand-border/60 rounded-brand overflow-hidden group hover:shadow-soft transition-all duration-300">
                <div class="w-full aspect-[3/2] overflow-hidden bg-brand-darker relative border-b border-brand-border/40">
                    <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=800&auto=format&fit=crop&q=80" alt="Soccer Jersey Styling" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                </div>
                <div class="p-5">
                    <h3 class="font-serif text-base font-semibold text-brand-text mb-2">How To Style A Soccer Jersey</h3>
                    <p class="text-xs text-brand-muted leading-relaxed font-light">From sporty to flirty to polished, the soccer jersey is a versatile wardrobe hero. Get inspired on how to style a jersey in five unique and modern ways.</p>
                </div>
            </div>
        </div>

        <!-- MIDDLE COLUMN: Sticky Promo Campaign (Col 5-8) -->
        <div class="lg:col-span-4 lg:sticky lg:top-28 self-start bg-brand-darker/50 border border-brand-border/60 rounded-brand p-8 text-center flex flex-col items-center justify-center min-h-[320px] shadow-sm">
            <span class="text-[10px] font-bold tracking-widest text-brand-accent uppercase block mb-3">Limited Promotion</span>
            
            <div class="border-t border-b border-brand-border/60 py-6 w-full my-2 space-y-3">
                <h2 class="font-serif text-2xl font-bold tracking-tight text-brand-text leading-tight">SAVE 30% IN THE APP</h2>
                <h4 class="font-sans text-xs font-bold tracking-widest text-brand-text/80 uppercase">2 GRAPHIC TEES FOR $30</h4>
            </div>

            <p class="text-[11.5px] text-brand-muted leading-relaxed font-light mt-4 mb-5 max-w-sm">
                Limited time offer valid September 23, 2025 12:01am PST through September 28, 2025 11:59pm PST. adiClub members receive 30% off eligible full price and sale products* in the adidas app with promo code ADICLUB at checkout online. Offer is only valid in the adidas app. Members must be signed in to their account at the time of purchase for the discount to apply online. Offer not valid at adidas Retail stores.
            </p>

            <a href="/products" class="inline-block px-6 py-2.5 bg-brand-text hover:bg-brand-accent text-white text-[10px] font-bold uppercase tracking-widest rounded transition-colors shadow-sm">
                Shop the App Offer
            </a>
        </div>

        <!-- RIGHT COLUMN: Samba & Custom Blocks (Col 9-12) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Card 1: Samba Size Guide 2 -->
            <div class="bg-brand-bg border border-brand-border/60 rounded-brand overflow-hidden group hover:shadow-soft transition-all duration-300">
                <div class="w-full aspect-[3/2] overflow-hidden bg-brand-darker relative border-b border-brand-border/40">
                    <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80" alt="Samba Size Guide 2" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                </div>
                <div class="p-5">
                    <h3 class="font-serif text-base font-semibold text-brand-text mb-2">The adidas Samba Size Guide</h3>
                    <p class="text-xs text-brand-muted leading-relaxed font-light">Tired of asking are Sambas true to size? Check out our official adidas Samba size chart for all you need to find your perfect Samba style and fit today.</p>
                </div>
            </div>

            <!-- Card 2: Arsenal Jersey (Using Existing Local Image) -->
            <div class="bg-brand-bg border border-brand-border/60 rounded-brand overflow-hidden group hover:shadow-soft transition-all duration-300">
                <div class="w-full aspect-[3/2] overflow-hidden bg-brand-darker relative border-b border-brand-border/40">
                    <img src="<?= asset_url('/assets/images/nike-football.avif') ?>" alt="Arsenal Custom Block" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                </div>
                <div class="p-5">
                    <h3 class="font-serif text-base font-semibold text-brand-text mb-2">Custom Blocks & Badges</h3>
                    <p class="text-xs text-brand-muted leading-relaxed font-light">Elevate your store layout with interactive custom blocks, promotional badges, stickers, and seamless product quick views designed for modern e-commerce.</p>
                </div>
            </div>
        </div>
    </div>
</div>





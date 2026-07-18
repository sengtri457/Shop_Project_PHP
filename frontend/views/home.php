<?php
function getCategoryImage(string $name): string {
    $name = strtolower(trim($name));
    $map = [
        'shoes'       => 'https://static.nike.com/a/images/f_auto/dpr_1.2,cs_srgb/h_1198,c_limit/f5100f5d-1df6-4558-b407-701bbbfc571f/nike-just-do-it.jpg',
        'T-Shirt'     => 'https://static.nike.com/a/images/f_auto/dpr_1.2,cs_srgb/h_658,c_limit/e78ce8ec-bf9b-4b1c-acc4-a852f660aee8/nike-just-do-it.jpg',

    ];
    
    foreach ($map as $key => $url) {
        if (strpos($name, $key) !== false) {
            return $url;
        }
    }
    return 'https://static.nike.com/a/images/f_auto/dpr_1.2,cs_srgb/h_658,c_limit/e78ce8ec-bf9b-4b1c-acc4-a852f660aee8/nike-just-do-it.jpg';
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
</style>

<!-- Brand Logo Slider -->
<div class="w-full overflow-hidden  mb-10 bg-brand-bg relative flex items-center">
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

<div class="flex items-center justify-start py-[100px] px-8 sm:px-20 bg-brand-darker bg-no-repeat bg-cover bg-center rounded-brand mb-20 min-h-[520px] border border-brand-border" style="background-image: linear-gradient(to right, rgba(250,250,248,0.92) 35%, rgba(7, 196, 189, 0.1) 100%), url('https://static.nike.com/a/images/f_auto/dpr_1.2,cs_srgb/w_1600,c_limit/ec45aa51-06a0-40ef-9e5b-acefe2b618bf/nike-just-do-it.jpg');">
    <div class="max-w-[540px] flex flex-col items-start">
        <h1 class="font-serif text-[2.8rem] sm:text-[3.2rem] font-medium leading-[1.15] mb-5 text-brand-text">
            <?php if (is_logged_in()): ?>
                Curated Style for <?= htmlspecialchars($_SESSION['customer']['name'] ?? '') ?>
            <?php else: ?>
                Minimalism in Everyday Wear
            <?php endif; ?>
        </h1>
        <p class="text-[1.05rem] text-brand-muted mb-8 leading-relaxed">A collection of premium, high-quality linen and cotton essentials designed for modern comfort and timeless style.</p>
        <a href="/products" class="inline-flex items-center justify-center px-9 py-4 bg-brand-text text-brand-bg hover:bg-brand-accent border border-brand-text rounded-brand font-sans text-[12px] font-semibold tracking-widest uppercase transition-all duration-300 transform hover:-translate-y-px">Shop the Collection</a>
    </div>
</div>

<div class="mb-20">
    <h2 class="font-serif text-[1.8rem] font-medium mb-8 text-left relative pb-3 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-10 after:h-[2px] after:bg-brand-accent">Shop by Section</h2>
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

<div class="mb-20">
    <h2 class="font-serif text-[1.8rem] font-medium mb-8 text-left relative pb-3 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-10 after:h-[2px] after:bg-brand-accent">Shop by Category</h2>
    <?php
    $result = api_get('/categories');
    $categories = array_slice($result['data'] ?? [], 0, 5);
    ?>
    <?php if (empty($categories)): ?>
        <p class="text-brand-muted">No categories yet.</p>
    <?php else: ?>
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 lg:grid-rows-2 lg:gap-6 lg:h-[580px]">
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

<div class="mb-20">
    <h2 class="font-serif text-[1.8rem] font-medium mb-8 text-left relative pb-3 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-10 after:h-[2px] after:bg-brand-accent">New Arrivals</h2>
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
            ?>
                <div class="flex flex-col bg-transparent rounded-brand group transition-all duration-300 relative">
                    <!-- Favorite Toggle Button Overlay -->
                    <button onclick="toggleFav(<?= $product['id'] ?>, event)" class="fav-btn absolute top-3.5 right-3.5 z-20 w-8 h-8 rounded-full bg-white/95 border border-brand-border flex items-center justify-center hover:scale-105 transition-transform shadow-[0_2px_8px_rgba(0,0,0,0.04)] focus:outline-none" data-fav-id="<?= $product['id'] ?>">
                        <svg class="w-4 h-4 text-brand-muted hover:text-brand-error transition-colors" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                        </svg>
                    </button>

                    <a href="/products/<?= $product['id'] ?>" class="flex flex-col h-full">
                        <div class="w-full aspect-[3/4] overflow-hidden rounded-brand bg-brand-darker mb-3 relative border border-brand-border">
                            <img src="<?= htmlspecialchars($mainImg) ?>" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105" alt="<?= htmlspecialchars($product['name']) ?>">
                            
                            <!-- Quick View Overlay -->
                            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center pointer-events-none">
                                <button type="button" onclick="openQuickView(<?= (int)$product['id'] ?>, event)" class="pointer-events-auto bg-brand-bg/95 hover:bg-brand-bg text-brand-text text-[10px] font-bold uppercase tracking-widest py-2.5 px-5 rounded shadow-lg transition-all translate-y-3 group-hover:translate-y-0 duration-300">
                                    Quick View
                                </button>
                            </div>
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

<!-- Immersive Video Campaign Banner -->
<div class="w-full aspect-[21/9] md:aspect-[21/8] max-h-[550px] overflow-hidden rounded-brand relative mb-20 border border-brand-border group">
    <video autoplay muted loop playsinline class="w-full h-full object-cover scale-[1.01] transition-transform duration-[1200ms] group-hover:scale-105">
        <source src="<?= asset_url('/assets/images/hm.webm') ?>" type="video/webm">
        Your browser does not support the video tag.
    </video>
    <!-- Dark overlay for contrast -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/15 to-transparent flex flex-col justify-end p-8 md:p-14">
        <div class="max-w-md page-fade">
            <span class="text-[10.5px] font-bold tracking-widest text-[#fee8e6] uppercase block mb-2">Exclusive Campaign</span>
            <h2 class="font-serif text-[1.75rem] md:text-[2.25rem] font-medium text-white leading-tight mb-4">Redefining Active Style</h2>
            <p class="text-[12.5px] text-white/80 leading-relaxed mb-6 max-w-sm">Experience maximum comfort and performance engineered for daily movement.</p>
            <a href="/products" class="inline-block bg-white text-black text-[11px] font-bold uppercase tracking-wider px-6 py-3 rounded hover:bg-black hover:text-white border border-white transition-all duration-300">
                Explore Collection &rarr;
            </a>
        </div>
    </div>
</div>


<?php
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = max(1, (int) ($_GET['limit'] ?? 60));
$sortBy = $_GET['sort_by'] ?? 'created_at';
$sortOrder = $_GET['sort_order'] ?? 'desc';
$categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;
$search = $_GET['search'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';
$gender = $_GET['gender'] ?? '';

$query = http_build_query(array_filter([
    'page' => $page,
    'limit' => $limit,
    'sort_by' => $sortBy,
    'sort_order' => $sortOrder,
    'category_id' => $categoryId,
    'search' => $search ?: null,
    'min_price' => $minPrice ?: null,
    'max_price' => $maxPrice ?: null,
    'gender' => $gender ?: null,
]));

$result = api_get("/products?$query");
$products = $result['data']['data'] ?? [];
$meta = $result['data']['meta'] ?? [];
$totalProducts = $meta['total'] ?? count($products);

$cats = api_get('/categories');
$categories = $cats['data'] ?? [];

// Dynamic Banner Configuration
$bannerTitle = 'All Products';
$bannerSubtitle = 'Discover our curated selection of high-quality essentials.';
$bannerImage = '/assets/images/hero_banner.png';

if ($gender === 'men') {
    $bannerTitle = "Men's Collection";
    $bannerSubtitle = "Engineered for durability and modern style.";
    $bannerImage = '/assets/images/bannerMen.gif';
} elseif ($gender === 'women') {
    $bannerTitle = "Women's Collection";
    $bannerSubtitle = "Premium fabrics and clean, elegant silhouettes.";
    $bannerImage = '/assets/images/BannerWomen.avif';
} elseif ($gender === 'kids') {
    $bannerTitle = "Kids' Collection";
    $bannerSubtitle = "Comfortable, playful styles designed for movement.";
    $bannerImage = '/assets/images/hero_banner.png'; 
}

$activeFilterCount = count(array_filter([$categoryId, $search, $minPrice, $maxPrice, $gender]));
?>

<style>
.sidebar-transition {
    transition: width 0.4s cubic-bezier(0.25, 1, 0.5, 1), 
                opacity 0.25s ease-out, 
                padding 0.4s cubic-bezier(0.25, 1, 0.5, 1),
                margin 0.4s cubic-bezier(0.25, 1, 0.5, 1);
}
.sidebar-transition.sidebar-hidden {
    width: 0 !important;
    min-width: 0 !important;
    opacity: 0 !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
    margin-right: -32px !important;
    border-color: transparent !important;
    pointer-events: none !important;
    overflow: hidden !important;
}
</style>

<div class="section px-4 sm:px-6">
    <!-- Premium Section Top Banner -->
    <div class="w-full aspect-[21/6] md:aspect-[21/5] max-h-[280px] overflow-hidden rounded-brand relative mb-8 border border-brand-border">
        <!-- Dark gradient overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/30 to-transparent z-10"></div>
        <img src="<?= asset_url($bannerImage) ?>" class="w-full h-full object-cover" alt="<?= htmlspecialchars($bannerTitle) ?>">
        <div class="absolute inset-0 flex flex-col justify-center px-6 md:px-12 z-20">
            <div class="max-w-md page-fade">
                <span class="text-[10px] font-bold tracking-widest text-[#fee8e6] uppercase block mb-1">Store Catalog</span>
                <h1 class="font-serif text-[1.6rem] md:text-[2.2rem] font-medium text-white leading-tight mb-1.5"><?= htmlspecialchars($bannerTitle) ?></h1>
                <p class="text-[12px] text-white/80 leading-relaxed max-w-sm"><?= htmlspecialchars($bannerSubtitle) ?></p>
            </div>
        </div>
    </div>

    <!-- Main 2-Column E-Commerce Flex Layout -->
    <div class="flex gap-8 items-start mb-12 relative">
        
        <!-- DESKTOP SIDEBAR FILTERS -->
        <aside id="desktop-sidebar-panel" class="hidden lg:block w-[260px] shrink-0 sticky top-24 bg-brand-bg border border-brand-border rounded-brand p-5 space-y-6 shadow-sm overflow-hidden sidebar-transition">
            <div class="flex items-center justify-between border-b border-brand-border pb-3">
                <h3 class="font-serif text-base font-semibold text-brand-text flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-accent" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filters
                </h3>
                <?php if ($activeFilterCount > 0): ?>
                    <a href="/products" class="text-[11px] font-bold text-brand-accent hover:underline uppercase tracking-wider">Reset All</a>
                <?php endif; ?>
            </div>

            <form action="/products" method="GET" class="space-y-6">
                <!-- Preserve existing search/sort if any -->
                <?php if ($search): ?><input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"><?php endif; ?>
                <?php if ($sortBy): ?><input type="hidden" name="sort_by" value="<?= htmlspecialchars($sortBy) ?>"><?php endif; ?>
                <?php if ($sortOrder): ?><input type="hidden" name="sort_order" value="<?= htmlspecialchars($sortOrder) ?>"><?php endif; ?>

                <!-- Gender / Section Nav -->
                <div>
                    <h4 class="text-[11px] font-bold uppercase tracking-widest text-brand-muted mb-3">Section</h4>
                    <div class="flex flex-col gap-1 text-[13px]">
                        <a href="/products?<?= http_build_query(array_merge($_GET, ['gender' => '', 'page' => 1])) ?>" class="px-3 py-2 rounded transition-all <?= empty($gender) ? 'bg-brand-text text-brand-bg font-semibold' : 'text-brand-text hover:bg-brand-darker' ?>">
                            All Sections
                        </a>
                        <a href="/products?<?= http_build_query(array_merge($_GET, ['gender' => 'men', 'page' => 1])) ?>" class="px-3 py-2 rounded transition-all <?= $gender === 'men' ? 'bg-brand-text text-brand-bg font-semibold' : 'text-brand-text hover:bg-brand-darker' ?>">
                            Men's Collection
                        </a>
                        <a href="/products?<?= http_build_query(array_merge($_GET, ['gender' => 'women', 'page' => 1])) ?>" class="px-3 py-2 rounded transition-all <?= $gender === 'women' ? 'bg-brand-text text-brand-bg font-semibold' : 'text-brand-text hover:bg-brand-darker' ?>">
                            Women's Collection
                        </a>
                        <a href="/products?<?= http_build_query(array_merge($_GET, ['gender' => 'kids', 'page' => 1])) ?>" class="px-3 py-2 rounded transition-all <?= $gender === 'kids' ? 'bg-brand-text text-brand-bg font-semibold' : 'text-brand-text hover:bg-brand-darker' ?>">
                            Kids' Collection
                        </a>
                    </div>
                </div>

                <!-- Categories Nav -->
                <div class="border-t border-brand-border pt-4">
                    <h4 class="text-[11px] font-bold uppercase tracking-widest text-brand-muted mb-3">Category</h4>
                    <div class="flex flex-col gap-1 text-[13px]">
                        <a href="/products?<?= http_build_query(array_merge($_GET, ['category_id' => '', 'page' => 1])) ?>" class="px-3 py-1.5 rounded transition-all <?= empty($categoryId) ? 'text-brand-accent font-bold' : 'text-brand-muted hover:text-brand-text' ?>">
                            All Categories
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="/products?<?= http_build_query(array_merge($_GET, ['category_id' => $cat['id'], 'page' => 1])) ?>" class="px-3 py-1.5 rounded transition-all flex items-center justify-between <?= (int)$categoryId === (int)$cat['id'] ? 'text-brand-accent font-bold' : 'text-brand-muted hover:text-brand-text' ?>">
                                <span><?= htmlspecialchars($cat['name']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Price Range Filter -->
                <div class="border-t border-brand-border pt-4">
                    <h4 class="text-[11px] font-bold uppercase tracking-widest text-brand-muted mb-3">Price Range</h4>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <input type="number" name="min_price" placeholder="Min $" step="0.01" value="<?= htmlspecialchars($minPrice) ?>" class="w-full bg-brand-darker border border-brand-border rounded px-3 py-2 text-xs text-brand-text focus:outline-none focus:border-brand-accent">
                        <input type="number" name="max_price" placeholder="Max $" step="0.01" value="<?= htmlspecialchars($maxPrice) ?>" class="w-full bg-brand-darker border border-brand-border rounded px-3 py-2 text-xs text-brand-text focus:outline-none focus:border-brand-accent">
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-brand-text text-brand-bg hover:bg-brand-accent text-[11px] font-bold uppercase tracking-wider rounded transition-colors">
                        Apply Price
                    </button>
                </div>
            </form>
        </aside>

        <!-- PRODUCT LISTINGS & TOOLBAR -->
        <main id="products-main-panel" class="flex-1 min-w-0 flex flex-col gap-6">
            <!-- Toolbar & Controls -->
            <div class="flex flex-wrap items-center justify-between gap-4 bg-brand-bg border border-brand-border rounded-brand p-4 shadow-sm">
                <!-- Search Box -->
                <form action="/products" method="GET" class="flex-1 max-w-xs relative">
                    <?php if ($gender): ?><input type="hidden" name="gender" value="<?= htmlspecialchars($gender) ?>"><?php endif; ?>
                    <?php if ($categoryId): ?><input type="hidden" name="category_id" value="<?= htmlspecialchars($categoryId) ?>"><?php endif; ?>
                    <input type="search" name="search" placeholder="Search product name..." value="<?= htmlspecialchars($search) ?>" class="w-full bg-brand-darker text-xs text-brand-text placeholder-brand-muted pl-9 pr-3 py-2.5 rounded border border-brand-border focus:outline-none focus:border-brand-accent">
                    <svg class="w-4 h-4 text-brand-muted absolute left-3 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </form>

                <div class="flex items-center gap-3 ml-auto">
                    <!-- Desktop Hide/Show Filters Toggle -->
                    <button type="button" onclick="toggleDesktopSidebar()" class="hidden lg:flex items-center gap-2 px-3.5 py-2 bg-brand-darker border border-brand-border text-brand-text text-xs font-semibold rounded hover:bg-brand-border/40 focus:outline-none transition-all">
                        <svg class="w-4 h-4 text-brand-accent" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span id="desktop-sidebar-btn-text">Hide Filters</span>
                    </button>

                    <!-- Mobile Filter Drawer Trigger -->
                    <button onclick="toggleMobileFilters()" class="lg:hidden flex items-center gap-2 px-4 py-2 bg-brand-darker border border-brand-border text-brand-text text-xs font-semibold rounded hover:bg-brand-border/40">
                        <svg class="w-4 h-4 text-brand-accent" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filters <?= $activeFilterCount > 0 ? "($activeFilterCount)" : "" ?>
                    </button>

                    <!-- Sorting Dropdown -->
                    <form action="/products" method="GET" class="flex items-center gap-2">
                        <?php foreach ($_GET as $k => $v): if (in_array($k, ['sort_by', 'sort_order'])) continue; ?>
                            <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
                        <?php endforeach; ?>
                        
                        <label class="text-[11px] font-bold uppercase tracking-wider text-brand-muted hidden sm:inline">Sort By:</label>
                        <select name="sort_by" onchange="this.form.submit()" class="bg-brand-darker text-xs font-medium text-brand-text border border-brand-border rounded px-3 py-2 focus:outline-none">
                            <option value="created_at" <?= $sortBy === 'created_at' ? 'selected' : '' ?>>Newest</option>
                            <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>Name (A-Z)</option>
                            <option value="base_price" <?= $sortBy === 'base_price' ? 'selected' : '' ?>>Price</option>
                        </select>
                        <select name="sort_order" onchange="this.form.submit()" class="bg-brand-darker text-xs font-medium text-brand-text border border-brand-border rounded px-2.5 py-2 focus:outline-none">
                            <option value="desc" <?= $sortOrder === 'desc' ? 'selected' : '' ?>>High &rarr; Low</option>
                            <option value="asc" <?= $sortOrder === 'asc' ? 'selected' : '' ?>>Low &rarr; High</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Products Grid Section -->
            <?php if (empty($products)): ?>
                <div class="text-center py-16 bg-brand-darker rounded-brand border border-brand-border p-6">
                    <p class="text-[13px] text-brand-muted font-medium mb-3">No products match your selected filters.</p>
                    <a href="/products" class="inline-block px-5 py-2.5 bg-brand-text text-brand-bg text-[11px] font-bold uppercase tracking-widest rounded hover:bg-brand-accent transition-colors">Clear All Filters</a>
                </div>
            <?php else: ?>
                <div id="products-grid-view" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6 transition-all duration-300">
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
                        <div class="flex flex-col bg-transparent rounded-brand group transition-all duration-300 relative h-full">
                            <!-- Favorite Toggle Button Overlay -->
                            <button type="button" onclick="toggleFav(<?= (int)$product['id'] ?>, event)" class="fav-btn absolute top-3 right-3 z-20 w-8 h-8 rounded-full bg-white/95 border border-brand-border flex items-center justify-center hover:scale-105 transition-transform shadow-sm focus:outline-none" data-fav-id="<?= (int)$product['id'] ?>">
                                <svg class="w-4 h-4 text-brand-muted hover:text-brand-error transition-colors" viewBox="0 0 24 24">
                                    <path fill="none" stroke="currentColor" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                                </svg>
                            </button>

                            <a href="/products/<?= $product['id'] ?>" class="flex flex-col h-full">
                                <div class="w-full aspect-[3/4] overflow-hidden rounded-brand bg-brand-darker mb-3 relative border border-brand-border">
                                    <img src="<?= htmlspecialchars($mainImg) ?>" 
                                         alt="<?= htmlspecialchars($product['name']) ?>" 
                                         loading="lazy"
                                         decoding="async"
                                         class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                                    
                                    <!-- Quick View Overlay -->
                                    <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center pointer-events-none">
                                        <button type="button" onclick="openQuickView(<?= (int)$product['id'] ?>, event)" class="pointer-events-auto bg-brand-bg/95 hover:bg-brand-bg text-brand-text text-[10px] font-bold uppercase tracking-widest py-2 px-4 rounded shadow transition-all translate-y-2 group-hover:translate-y-0 duration-300">
                                            Quick View
                                        </button>
                                    </div>
                                </div>
                                <?php if (!empty($product['brand'])): ?>
                                    <span class="font-sans text-[10px] font-semibold uppercase tracking-wider text-brand-muted mb-1"><?= htmlspecialchars($product['brand']) ?></span>
                                <?php endif; ?>
                                <h3 class="font-sans text-[13.5px] font-medium leading-relaxed mb-1 text-left text-brand-text line-clamp-1"><?= htmlspecialchars($product['name']) ?></h3>
                                
                                <?php if ($discountPercent > 0): ?>
                                    <p class="font-sans text-[13.5px] font-semibold text-brand-accent text-left flex gap-2 items-center mt-auto">
                                        <span class="text-brand-error">
                                            $<?= number_format($basePrice * (1 - $discountPercent / 100), 2) ?>
                                        </span>
                                        <span class="text-[10px] font-semibold text-brand-error bg-[#fee8e6] px-1 py-0.5 rounded-[3px]">
                                            -<?= $discountPercent ?>%
                                        </span>
                                        <span class="text-brand-muted line-through text-[11.5px] font-normal">
                                            $<?= number_format($basePrice, 2) ?>
                                        </span>
                                    </p>
                                <?php else: ?>
                                    <p class="font-sans text-[13.5px] font-semibold text-brand-accent text-left mt-auto">$<?= number_format($basePrice, 2) ?></p>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Infinite Scroll Sentinel & Skeleton Loader -->
                <div id="infinite-scroll-sentinel" class="w-full py-6 flex flex-col items-center justify-center col-span-full">
                    <!-- Skeleton Loader Grid (Hidden by default, shown during fetch) -->
                    <div id="infinite-skeleton-container" class="hidden grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6 w-full mb-6">
                        <?php for ($s = 0; $s < 3; $s++): ?>
                            <div class="flex flex-col bg-brand-darker rounded-brand border border-brand-border overflow-hidden animate-pulse">
                                <div class="w-full aspect-[3/4] bg-brand-border/40 skeleton-shimmer"></div>
                                <div class="p-3 space-y-2">
                                    <div class="h-2.5 bg-brand-border/50 rounded w-1/3 skeleton-shimmer"></div>
                                    <div class="h-3.5 bg-brand-border/60 rounded w-3/4 skeleton-shimmer"></div>
                                    <div class="h-3.5 bg-brand-border/70 rounded w-1/2 skeleton-shimmer"></div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                    
                    <div id="infinite-status-msg" class="text-xs font-semibold text-brand-muted uppercase tracking-wider flex items-center gap-2"></div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<!-- Mobile Filter Drawer -->
<div id="mobile-filter-backdrop" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[999] opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden" onclick="toggleMobileFilters()"></div>
<div id="mobile-filter-drawer" class="fixed left-0 top-0 bottom-0 w-full max-w-[320px] bg-brand-bg border-r border-brand-border z-[1000] -translate-x-full transition-transform duration-300 flex flex-col p-6 overflow-y-auto lg:hidden">
    <div class="flex items-center justify-between border-b border-brand-border pb-4 mb-6">
        <h3 class="font-serif text-lg font-semibold text-brand-text">Filter Products</h3>
        <button onclick="toggleMobileFilters()" class="text-brand-muted hover:text-brand-text text-2xl focus:outline-none">&times;</button>
    </div>
    
    <form action="/products" method="GET" class="space-y-6">
        <div>
            <h4 class="text-[11px] font-bold uppercase tracking-widest text-brand-muted mb-3">Section</h4>
            <select name="gender" class="w-full bg-brand-darker border border-brand-border rounded px-3 py-2 text-xs">
                <option value="">All Sections</option>
                <option value="men" <?= $gender === 'men' ? 'selected' : '' ?>>Men</option>
                <option value="women" <?= $gender === 'women' ? 'selected' : '' ?>>Women</option>
                <option value="kids" <?= $gender === 'kids' ? 'selected' : '' ?>>Kids</option>
            </select>
        </div>

        <div>
            <h4 class="text-[11px] font-bold uppercase tracking-widest text-brand-muted mb-3">Category</h4>
            <select name="category_id" class="w-full bg-brand-darker border border-brand-border rounded px-3 py-2 text-xs">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (int)$categoryId === (int)$cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <h4 class="text-[11px] font-bold uppercase tracking-widest text-brand-muted mb-3">Price Range</h4>
            <div class="grid grid-cols-2 gap-2 mb-3">
                <input type="number" name="min_price" placeholder="Min $" step="0.01" value="<?= htmlspecialchars($minPrice) ?>" class="w-full bg-brand-darker border border-brand-border rounded px-3 py-2 text-xs">
                <input type="number" name="max_price" placeholder="Max $" step="0.01" value="<?= htmlspecialchars($maxPrice) ?>" class="w-full bg-brand-darker border border-brand-border rounded px-3 py-2 text-xs">
            </div>
        </div>

        <button type="submit" class="w-full py-3 bg-brand-text text-brand-bg text-[11px] font-bold uppercase tracking-widest rounded">Apply Filters</button>
        <?php if ($activeFilterCount > 0): ?>
            <a href="/products" class="block text-center text-[11px] font-bold text-brand-error uppercase tracking-wider mt-2">Clear All</a>
        <?php endif; ?>
    </form>
</div>

<!-- Back to Top Floating Button -->
<button id="back-to-top-btn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" aria-label="Back to top" class="fixed bottom-6 right-6 z-50 w-11 h-11 rounded-full bg-brand-text text-white shadow-xl flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300 hover:bg-brand-accent focus:outline-none">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
    </svg>
</button>

<script>
// Infinite Scroll & Intersection Observer State
let infiniteCurrentPage = <?= (int)$page ?>;
const infiniteTotalPages = <?= (int)($meta['pages'] ?? 1) ?>;
let infiniteIsLoading = false;
const currentQueryParams = new URLSearchParams(window.location.search);

function toggleMobileFilters() {
    const backdrop = document.getElementById('mobile-filter-backdrop');
    const drawer = document.getElementById('mobile-filter-drawer');
    if (backdrop && drawer) {
        const isClosed = drawer.classList.contains('-translate-x-full');
        if (isClosed) {
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            drawer.classList.remove('-translate-x-full');
        } else {
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            drawer.classList.add('-translate-x-full');
        }
    }
}

function toggleDesktopSidebar() {
    const sidebar = document.getElementById('desktop-sidebar-panel');
    const gridView = document.getElementById('products-grid-view');
    const btnText = document.getElementById('desktop-sidebar-btn-text');

    if (!sidebar) return;

    const isHidden = sidebar.classList.contains('sidebar-hidden');

    if (isHidden) {
        sidebar.classList.remove('sidebar-hidden');
        if (gridView) {
            gridView.classList.remove('lg:grid-cols-4');
            gridView.classList.add('lg:grid-cols-3');
        }
        if (btnText) btnText.textContent = 'Hide Filters';
        localStorage.setItem('sidebar_collapsed', 'false');
    } else {
        sidebar.classList.add('sidebar-hidden');
        if (gridView) {
            gridView.classList.remove('lg:grid-cols-3');
            gridView.classList.add('lg:grid-cols-4');
        }
        if (btnText) btnText.textContent = 'Show Filters';
        localStorage.setItem('sidebar_collapsed', 'true');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Check saved sidebar preference
    if (localStorage.getItem('sidebar_collapsed') === 'true' && window.innerWidth >= 1024) {
        const sidebar = document.getElementById('desktop-sidebar-panel');
        const gridView = document.getElementById('products-grid-view');
        const btnText = document.getElementById('desktop-sidebar-btn-text');
        if (sidebar) sidebar.classList.add('sidebar-hidden');
        if (gridView) {
            gridView.classList.remove('lg:grid-cols-3');
            gridView.classList.add('lg:grid-cols-4');
        }
        if (btnText) btnText.textContent = 'Show Filters';
    }

    // Floating Back to Top Button scroll listener
    const backToTopBtn = document.getElementById('back-to-top-btn');
    window.addEventListener('scroll', () => {
        if (backToTopBtn) {
            if (window.scrollY > 400) {
                backToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
            } else {
                backToTopBtn.classList.add('opacity-0', 'pointer-events-none');
            }
        }
    });

    // Intersection Observer setup for infinite scroll
    const sentinel = document.getElementById('infinite-scroll-sentinel');
    if (!sentinel || infiniteCurrentPage >= infiniteTotalPages) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !infiniteIsLoading && infiniteCurrentPage < infiniteTotalPages) {
                fetchNextProductBatch();
            }
        });
    }, {
        rootMargin: '250px' // Load next page 250px before sentinel enters viewport
    });

    observer.observe(sentinel);
});

function fetchNextProductBatch() {
    infiniteIsLoading = true;
    const nextPage = infiniteCurrentPage + 1;
    const skeletonContainer = document.getElementById('infinite-skeleton-container');
    const statusMsg = document.getElementById('infinite-status-msg');
    
    if (skeletonContainer) skeletonContainer.classList.remove('hidden');
    if (statusMsg) statusMsg.innerHTML = '<span class="animate-spin text-brand-accent">↻</span> Loading more products...';

    currentQueryParams.set('page', nextPage);

    fetch('http://localhost:8000/products?' + currentQueryParams.toString(), {
        headers: typeof getAuthHeaders === 'function' ? getAuthHeaders() : {}
    })
    .then(res => res.json())
    .then(result => {
        infiniteIsLoading = false;
        if (skeletonContainer) skeletonContainer.classList.add('hidden');

        const productsData = result.data?.data || [];
        if (productsData.length > 0) {
            infiniteCurrentPage = nextPage;
            appendProductsToGrid(productsData);
        }

        if (infiniteCurrentPage >= infiniteTotalPages) {
            if (statusMsg) statusMsg.innerHTML = '<span class="text-brand-muted">You\'ve reached the end of the collection</span>';
        } else {
            if (statusMsg) statusMsg.innerHTML = '';
        }
    })
    .catch(err => {
        console.error(err);
        infiniteIsLoading = false;
        if (skeletonContainer) skeletonContainer.classList.add('hidden');
        if (statusMsg) statusMsg.innerHTML = '<span class="text-brand-error">Failed to load items. Scroll to retry.</span>';
    });
}

function appendProductsToGrid(products) {
    const gridView = document.getElementById('products-grid-view');
    if (!gridView) return;

    products.forEach(p => {
        let images = [];
        if (p.images) {
            images = (typeof splitImageUrls === 'function') ? splitImageUrls(p.images) : (typeof p.images === 'string' ? p.images.split(',') : p.images);
        }
        if (images.length === 0 && p.variants && Array.isArray(p.variants)) {
            p.variants.forEach(v => {
                if (v.image_url) images.push(v.image_url);
            });
        }
        const mainImgUrl = images.length > 0 ? ((typeof getAssetUrl === 'function') ? getAssetUrl(images[0].trim()) : images[0].trim()) : '/assets/images/hero_banner.png';
        const discPercent = parseInt(p.discount_percent || 0);
        const basePrice = parseFloat(p.base_price || 0);
        const salePrice = discPercent > 0 ? basePrice * (1 - discPercent / 100) : basePrice;

        const cardHtml = `
            <div class="flex flex-col bg-transparent rounded-brand group transition-all duration-300 relative h-full">
                <button type="button" onclick="toggleFav(${p.id}, event)" class="fav-btn absolute top-3 right-3 z-20 w-8 h-8 rounded-full bg-white/95 border border-brand-border flex items-center justify-center hover:scale-105 transition-transform shadow-sm focus:outline-none" data-fav-id="${p.id}">
                    <svg class="w-4 h-4 text-brand-muted hover:text-brand-error transition-colors" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                    </svg>
                </button>

                <a href="/products/${p.id}" class="flex flex-col h-full">
                    <div class="w-full aspect-[3/4] overflow-hidden rounded-brand bg-brand-darker mb-3 relative border border-brand-border">
                        <img src="${mainImgUrl}" alt="${escapeHtml ? escapeHtml(p.name) : p.name}" loading="lazy" decoding="async" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                        <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center pointer-events-none">
                            <button type="button" onclick="openQuickView(${p.id}, event)" class="pointer-events-auto bg-brand-bg/95 hover:bg-brand-bg text-brand-text text-[10px] font-bold uppercase tracking-widest py-2 px-4 rounded shadow transition-all translate-y-2 group-hover:translate-y-0 duration-300">
                                Quick View
                            </button>
                        </div>
                    </div>
                    ${p.brand ? `<span class="font-sans text-[10px] font-semibold uppercase tracking-wider text-brand-muted mb-1">${escapeHtml ? escapeHtml(p.brand) : p.brand}</span>` : ''}
                    <h3 class="font-sans text-[13.5px] font-medium leading-relaxed mb-1 text-left text-brand-text line-clamp-1">${escapeHtml ? escapeHtml(p.name) : p.name}</h3>
                    ${discPercent > 0 ? `
                        <p class="font-sans text-[13.5px] font-semibold text-brand-accent text-left flex gap-2 items-center mt-auto">
                            <span class="text-brand-error">$${salePrice.toFixed(2)}</span>
                            <span class="text-[10px] font-semibold text-brand-error bg-[#fee8e6] px-1 py-0.5 rounded-[3px]">-${discPercent}%</span>
                            <span class="text-brand-muted line-through text-[11.5px] font-normal">$${basePrice.toFixed(2)}</span>
                        </p>
                    ` : `
                        <p class="font-sans text-[13.5px] font-semibold text-brand-accent text-left mt-auto">$${basePrice.toFixed(2)}</p>
                    `}
                </a>
            </div>
        `;

        const div = document.createElement('div');
        div.innerHTML = cardHtml.trim();
        gridView.appendChild(div.firstElementChild);
    });
}
</script>

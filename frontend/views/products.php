<?php
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 12;
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
$cats = api_get('/categories');
$categories = $cats['data'] ?? [];
?>

<?php
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
?>

<div class="section">
    <!-- Premium Section Top Banner -->
    <div class="w-full aspect-[21/6] md:aspect-[21/5] max-h-[340px] overflow-hidden rounded-brand relative mb-12 border border-brand-border">
        <!-- Dark gradient overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/30 to-transparent z-10"></div>
        <img src="<?= asset_url($bannerImage) ?>" class="w-full h-full object-cover" alt="<?= htmlspecialchars($bannerTitle) ?>">
        <div class="absolute inset-0 flex flex-col justify-center px-8 md:px-14 z-20">
            <div class="max-w-md page-fade">
                <span class="text-[10px] font-bold tracking-widest text-[#fee8e6] uppercase block mb-1.5">Collection Category</span>
                <h1 class="font-serif text-[1.75rem] md:text-[2.25rem] font-medium text-white leading-tight mb-2"><?= htmlspecialchars($bannerTitle) ?></h1>
                <p class="text-[12px] text-white/80 leading-relaxed max-w-sm"><?= htmlspecialchars($bannerSubtitle) ?></p>
            </div>
        </div>
    </div>

    <form class="filters" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; background: #fafafa; border: 1px solid var(--color-gray-light); padding: 16px; border-radius: var(--border-radius); margin-bottom: 40px; align-items: center;">
        <input type="search" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>" style="flex: 1; min-width: 200px; padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">

        <select name="gender" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; background: #fff; color: var(--color-dark);">
            <option value="">All Sections</option>
            <option value="unisex" <?= $gender === 'unisex' ? 'selected' : '' ?>>Unisex</option>
            <option value="men" <?= $gender === 'men' ? 'selected' : '' ?>>Men</option>
            <option value="women" <?= $gender === 'women' ? 'selected' : '' ?>>Women</option>
            <option value="kids" <?= $gender === 'kids' ? 'selected' : '' ?>>Kids</option>
        </select>

        <select name="category_id" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; background: #fff; color: var(--color-dark);">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $categoryId === $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="number" name="min_price" placeholder="Min price" step="0.01" value="<?= htmlspecialchars($minPrice) ?>" style="width: 100px; padding: 10px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">
        <input type="number" name="max_price" placeholder="Max price" step="0.01" value="<?= htmlspecialchars($maxPrice) ?>" style="width: 100px; padding: 10px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none;">

        <select name="sort_by" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; background: #fff;">
            <option value="created_at" <?= $sortBy === 'created_at' ? 'selected' : '' ?>>Newest</option>
            <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>Name</option>
            <option value="base_price" <?= $sortBy === 'base_price' ? 'selected' : '' ?>>Price</option>
        </select>

        <select name="sort_order" style="padding: 10px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; background: #fff;">
            <option value="desc" <?= $sortOrder === 'desc' ? 'selected' : '' ?>>Desc</option>
            <option value="asc" <?= $sortOrder === 'asc' ? 'selected' : '' ?>>Asc</option>
        </select>

        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Filter</button>
    </form>

    <?php if (empty($products)): ?>
        <p style="color: var(--color-gray); text-align: center; margin: 40px 0;">No products found matching your search.</p>
    <?php else: ?>
        <div class="product-grid">
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
                <div style="position: relative; display: flex; flex-direction: column; height: 100%;">
                    <!-- Favorite Toggle Button Overlay -->
                    <button type="button" onclick="toggleFav(<?= (int)$product['id'] ?>, event)" class="fav-btn" style="position: absolute; top: 12px; right: 12px; z-index: 20; width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.95); border: 1px solid var(--color-gray-light); display: flex; align-items: center; justify-content: center; cursor: pointer; outline: none; transition: transform 0.2s;" data-fav-id="<?= (int)$product['id'] ?>">
                        <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; display: block;">
                            <path fill="none" stroke="currentColor" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                        </svg>
                    </button>

                    <a href="/products/<?= $product['id'] ?>" class="product-card" style="display: flex; flex-direction: column; height: 100%; text-decoration: none; color: inherit;">
                        <img src="<?= htmlspecialchars($mainImg) ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>" 
                             style="width: 100%; height: 240px; object-fit: cover; border-radius: var(--border-radius); margin-bottom: 15px;">
                        <?php if (!empty($product['brand'])): ?>
                            <span class="brand" style="margin-bottom: 4px; font-size: 11px; text-transform: uppercase; color: var(--color-gray);"><?= htmlspecialchars($product['brand']) ?></span>
                        <?php endif; ?>
                        <h3 style="font-size: 1.1rem; margin-bottom: 8px;"><?= htmlspecialchars($product['name']) ?></h3>
                        <?php if ($discountPercent > 0): ?>
                            <p class="price" style="display: flex; gap: 8px; align-items: center; margin-top: auto;">
                                <span style="color: var(--color-error); font-weight: bold;">
                                    $<?= number_format($product['base_price'] * (1 - $discountPercent / 100), 2) ?>
                                </span>
                                <span style="font-size: 11px; font-weight: 600; color: var(--color-error); background: #fee8e6; padding: 1px 4px; border-radius: 3px;">
                                    -<?= $discountPercent ?>%
                                </span>
                                <span style="color: var(--color-gray); text-decoration: line-through; font-size: 12px; font-weight: normal;">
                                    $<?= number_format($product['base_price'], 2) ?>
                                </span>
                            </p>
                        <?php else: ?>
                            <p class="price" style="margin-top: auto;">$<?= number_format($product['base_price'], 2) ?></p>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (($meta['pages'] ?? 0) > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $meta['pages']; $i++): ?>
                    <a href="/products?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="page-link <?= $i === $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

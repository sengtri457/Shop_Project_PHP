<?php
$productId = $productId ?? null;
$result = api_get("/products/$productId");
$product = $result['data'] ?? [];

$reviewsResult = api_get("/products/$productId/reviews");
$reviewsData = $reviewsResult['data'] ?? [];
$reviewSummary = $reviewsData['summary'] ?? ['total_reviews' => 0, 'average_rating' => 0.0];
$reviewsList = $reviewsData['reviews'] ?? [];

if (!$product):
    http_response_code(404);
?>
<div class="section" style="text-align: center; padding: 80px 20px;">
    <h1 style="font-family: var(--font-serif); margin-bottom: 20px;">Product Not Found</h1>
    <p style="color: var(--color-gray); margin-bottom: 30px;">The product you are looking for does not exist or has been removed.</p>
    <a href="/products" class="btn">Back to Products</a>
</div>
<?php else:
    $galleryImages = split_image_urls($product['images'] ?? '');
    
    // Supplement with variant images
    if (!empty($product['variants'])) {
        foreach ($product['variants'] as $v) {
            if (!empty($v['image_url']) && !in_array($v['image_url'], $galleryImages)) {
                $galleryImages[] = $v['image_url'];
            }
        }
    }

    $galleryImages = array_map('asset_url', $galleryImages);
    
    // Fallback if none exist
    if (empty($galleryImages)) {
        $galleryImages[] = '/assets/images/hero_banner.png';
    }

    // Extract unique colors and sizes from variants
    $colors = [];
    $sizes = [];
    $variantsMap = [];
    $variants = $product['variants'] ?? [];

    foreach ($variants as $v) {
        $attrs = json_decode($v['attributes'] ?? '{}', true);
        
        $color = $attrs['color'] ?? '';
        $colorsList = is_array($color) ? $color : [$color];
        foreach ($colorsList as $c) {
            $c = trim($c);
            if ($c && !in_array($c, $colors)) {
                $colors[] = $c;
            }
        }
        
        $size = $attrs['size'] ?? '';
        $sizesList = is_array($size) ? $size : [$size];
        foreach ($sizesList as $s) {
            $s = trim($s);
            if ($s && !in_array($s, $sizes)) {
                $sizes[] = $s;
            }
        }
        
        $variantsMap[] = [
            'id' => $v['id'],
            'sku' => $v['sku'],
            'price' => (float)$v['price'],
            'stock_qty' => (int)$v['stock_qty'],
            'color' => $color,
            'size' => $size,
            'image_url' => $v['image_url'] ?? '',
        ];
    }
?>
<div class="section">
    <a href="/products" class="btn btn-small" style="margin-bottom: 30px;">&larr; Back to Shop</a>

    <div class="product-detail" style="grid-template-columns: 1.2fr 1fr; gap: 50px;">
        
        <!-- LEFT COLUMN: Image Gallery -->
        <div class="product-gallery" style="display: flex; gap: 16px; align-items: flex-start;">
            <!-- Vertical Thumbnail List -->
            <div class="thumbnail-list" style="display: flex; flex-direction: column; gap: 10px; max-height: 500px; overflow-y: auto; width: 80px; flex-shrink: 0;">
                <?php foreach ($galleryImages as $index => $imgUrl): ?>
                    <img src="<?= htmlspecialchars($imgUrl) ?>" 
                         alt="Thumbnail <?= $index + 1 ?>" 
                         onclick="switchMainImage(<?= $index ?>)" 
                         class="thumb-img"
                         style="width: 100%; height: 95px; object-fit: cover; border-radius: 4px; border: 2px solid <?= $index === 0 ? 'var(--color-text-main)' : 'transparent' ?>; cursor: pointer; transition: all 0.2s;"
                         data-index="<?= $index ?>">
                <?php endforeach; ?>
            </div>

            <!-- Main Active Image Viewer -->
            <div class="main-image-container" style="position: relative; flex: 1; height: 500px; background: var(--color-gray-bg); border-radius: var(--border-radius); border: 1px solid var(--color-gray-light); overflow: hidden; display: flex; align-items: center; justify-content: center;">
                <img id="main-product-image" 
                     src="<?= htmlspecialchars($galleryImages[0]) ?>" 
                     alt="<?= htmlspecialchars($product['name']) ?>" 
                     style="width: 100%; height: 100%; object-fit: cover; transition: opacity 0.2s;">
                     
                <!-- Carousel Nav Arrows -->
                <button type="button" onclick="prevImage()" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.85); border: none; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: var(--shadow-soft); z-index: 10; font-weight: bold; outline: none;">
                    &#10094;
                </button>
                <button type="button" onclick="nextImage()" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.85); border: none; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: var(--shadow-soft); z-index: 10; font-weight: bold; outline: none;">
                    &#10095;
                </button>
            </div>
        </div>

        <!-- RIGHT COLUMN: Details & Variant Selection -->
        <div class="product-info" style="border: none; box-shadow: none; padding: 0; display: flex; flex-direction: column;">
            <?php if (!empty($product['brand'])): ?>
                <span class="brand" style="font-size: 0.9rem; font-weight: 600; letter-spacing: 1px; color: var(--color-gray); text-transform: uppercase;"><?= htmlspecialchars($product['brand']) ?></span>
            <?php endif; ?>
            
            <h1 style="font-family: var(--font-serif); font-size: 2.4rem; margin-top: 5px; margin-bottom: 12px; font-weight: 500; line-height: 1.2;"><?= htmlspecialchars($product['name']) ?></h1>

            <?php if ($reviewSummary['total_reviews'] > 0): ?>
                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 16px; font-size: 13px;">
                    <div style="display: flex; color: #fbbf24; gap: 2px;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <svg fill="<?= $i <= round($reviewSummary['average_rating']) ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11.049 2.927c.3-.9 1.603-.9 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.246.582 1.817l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.9-.752 1.661-1.485 1.1l-3.97-2.883a1 1 0 00-1.175 0l-3.97 2.883c-.733.56-1.786-.2-1.485-1.1l1.518-4.674a1 1 0 00-.364-1.118L2.98 10.11c-.778-.57-.379-1.817.582-1.817h4.907a1 1 0 00.95-.69l1.519-4.674z"></path>
                            </svg>
                        <?php endfor; ?>
                    </div>
                    <span style="font-weight: 600; color: var(--color-black);"><?= number_format($reviewSummary['average_rating'], 1) ?></span>
                    <span style="color: var(--color-gray-light);">|</span>
                    <span style="color: var(--color-gray-dark);"><?= $reviewSummary['total_reviews'] ?> <?= $reviewSummary['total_reviews'] === 1 ? 'review' : 'reviews' ?></span>
                </div>
            <?php endif; ?>

            <!-- Before/After Discount Price Display -->
            <div id="variant-price-display" style="font-size: 1.8rem; margin-bottom: 24px; font-family: var(--font-sans);">
                <?php
                $basePrice = (float) $product['base_price'];
                $discountPercent = (int) ($product['discount_percent'] ?? 0);
                if ($discountPercent > 0):
                    $discPrice = $basePrice * (1 - $discountPercent / 100);
                ?>
                    <span style="color: var(--color-error); font-weight: 700; margin-right: 8px;">$<?= number_format($discPrice, 2) ?></span>
                    <span style="color: var(--color-error); font-size: 1.1rem; font-weight: 600; background: #fee8e6; padding: 3px 8px; border-radius: 4px; margin-right: 8px; vertical-align: middle;">-<?= $discountPercent ?>%</span>
                    <span style="color: var(--color-gray); text-decoration: line-through; font-size: 1.3rem;">$<?= number_format($basePrice, 2) ?></span>
                <?php else: ?>
                    <span style="font-weight: 700;">$<?= number_format($basePrice, 2) ?></span>
                <?php endif; ?>
            </div>

            <?php if (!empty($product['description'])): ?>
                <p class="description" style="color: var(--color-gray-dark); font-size: 0.95rem; line-height: 1.7; margin-bottom: 25px; border-top: 1px solid var(--color-gray-light); padding-top: 15px;"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <?php endif; ?>

            <!-- Variants selector -->
            <?php if (empty($variants)): ?>
                <p style="color: var(--color-gray);">No variants available for this product.</p>
            <?php else: ?>
                <form action="/cart" method="POST" class="variant-form" style="border: none; padding: 0; box-shadow: none; background: transparent; display: flex; flex-direction: column;">
                    
                    <input type="hidden" name="variant_id" id="selected-variant-id" required>
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                    <!-- Color Swatches/Buttons -->
                    <?php if (!empty($colors)): ?>
                        <div class="color-selector" style="margin-bottom: 20px; border-top: 1px solid var(--color-gray-light); padding-top: 15px;">
                            <h4 style="font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--color-gray); margin-bottom: 10px;">Color Available</h4>
                            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                <?php foreach ($colors as $index => $color): ?>
                                    <button type="button" 
                                            onclick="selectColor('<?= htmlspecialchars($color) ?>')" 
                                            class="color-btn"
                                            style="padding: 10px 16px; border: 1px solid var(--color-gray-light); border-radius: 4px; background: #fff; cursor: pointer; font-size: 13px; font-weight: 500; outline: none; transition: all 0.2s;">
                                        <?= htmlspecialchars($color) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Size Boxes -->
                    <?php if (!empty($sizes)): ?>
                        <div class="size-selector" style="margin-bottom: 20px; border-top: 1px solid var(--color-gray-light); padding-top: 15px;">
                            <h4 style="font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--color-gray); margin-bottom: 10px;">Size</h4>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <?php foreach ($sizes as $size): ?>
                                    <button type="button" 
                                            id="size-btn-<?= htmlspecialchars($size) ?>"
                                            onclick="selectSize('<?= htmlspecialchars($size) ?>')" 
                                            class="size-btn"
                                            style="min-width: 45px; height: 38px; border: 1px solid var(--color-gray-light); border-radius: 4px; background: #fff; cursor: pointer; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; outline: none; transition: all 0.2s;">
                                        <?= htmlspecialchars($size) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Stock indicator -->
                    <div id="variant-stock-display" style="font-size: 13px; margin-bottom: 20px; min-height: 18px;">
                        <span style="color: var(--color-gray);">Select size & color</span>
                    </div>

                    <!-- Quantity with custom adjust buttons -->
                    <div style="border-top: 1px solid var(--color-gray-light); padding-top: 20px; margin-bottom: 30px;">
                        <h4 style="font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--color-gray); margin-bottom: 8px;">Quantity</h4>
                        <div style="display: flex; align-items: center; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); width: fit-content; background: #fff; overflow: hidden;">
                            <button type="button" onclick="adjustQty(-1)" style="border: none; background: transparent; padding: 10px 16px; font-size: 16px; cursor: pointer; font-weight: 600; outline: none;">&minus;</button>
                            <input type="number" id="quantity-input" name="quantity" value="1" min="1" max="99" style="width: 45px; text-align: center; border: none; outline: none; font-size: 14px; font-weight: 600; -moz-appearance: textfield; appearance: textfield; pointer-events: none;">
                            <button type="button" onclick="adjustQty(1)" style="border: none; background: transparent; padding: 10px 16px; font-size: 16px; cursor: pointer; font-weight: 600; outline: none;">&plus;</button>
                        </div>
                        <div id="qty-error-message" style="color: var(--color-error); font-size: 13px; font-weight: 600; margin-top: 8px; display: none;"></div>
                    </div>

                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 12px; align-items: center; width: 100%;">
                        <!-- Add to Bag Button -->
                        <button type="submit" id="add-to-cart-btn" class="btn btn-primary btn-large" style="flex: 1; justify-content: center; height: 50px;" disabled>
                            Add to Bag
                        </button>
                        
                        <!-- Add to Favorites Button -->
                        <button type="button" onclick="toggleFav(<?= (int)$product['id'] ?>, event)" class="fav-btn btn" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); background: #fff; cursor: pointer; transition: all 0.2s;" data-fav-id="<?= (int)$product['id'] ?>">
                            <svg class="w-5 h-5 text-brand-muted hover:text-brand-error transition-colors" viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            <?php endif; ?>
            
            <!-- Metadata (Categories/Tags) -->
            <?php if (!empty($product['categories']) || !empty($product['tags'])): ?>
                <div style="border-top: 1px solid var(--color-gray-light); padding-top: 20px; margin-top: 25px; display: flex; flex-direction: column; gap: 12px;">
                    <?php if (!empty($product['categories'])): ?>
                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--color-gray);">Categories:</span>
                            <div class="tags">
                                <?php foreach ($product['categories'] as $cat): ?>
                                    <a href="/products?category_id=<?= $cat['id'] ?>" class="tag">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($product['tags'])): ?>
                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--color-gray);">Tags:</span>
                            <div class="tags">
                                <?php foreach ($product['tags'] as $tag): ?>
                                    <span class="tag tag-outline"><?= htmlspecialchars($tag['name']) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- RATINGS & REVIEWS SECTION -->
    <div style="border-top: 1px solid var(--color-gray-light); margin-top: 60px; padding-top: 40px;">
        <h2 style="font-family: var(--font-serif); font-size: 1.8rem; font-weight: 500; margin-bottom: 30px;">Ratings & Reviews</h2>
        
        <div class="reviews-layout" style="display: grid; grid-template-columns: 1fr 2fr; gap: 50px;">
            <!-- Left: Summaries -->
            <div class="reviews-summary" style="display: flex; flex-direction: column; gap: 24px;">
                <div style="background: var(--color-gray-bg); padding: 30px; border-radius: var(--border-radius); border: 1px solid var(--color-gray-light); text-align: center;">
                    <h3 style="font-size: 3rem; font-weight: 700; color: var(--color-black); margin-bottom: 5px;"><?= number_format($reviewSummary['average_rating'], 1) ?></h3>
                    <div style="display: flex; color: #fbbf24; gap: 4px; justify-content: center; margin-bottom: 8px;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <svg fill="<?= $i <= round($reviewSummary['average_rating']) ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.9 1.603-.9 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.246.582 1.817l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.9-.752 1.661-1.485 1.1l-3.97-2.883a1 1 0 00-1.175 0l-3.97 2.883c-.733.56-1.786-.2-1.485-1.1l1.518-4.674a1 1 0 00-.364-1.118L2.98 10.11c-.778-.57-.379-1.817.582-1.817h4.907a1 1 0 00.95-.69l1.519-4.674z"></path>
                            </svg>
                        <?php endfor; ?>
                    </div>
                    <p style="color: var(--color-gray-dark); font-size: 13px; font-weight: 500;"><?= $reviewSummary['total_reviews'] ?> customer <?= $reviewSummary['total_reviews'] === 1 ? 'review' : 'reviews' ?></p>
                </div>

                <!-- Star Distribution Bars -->
                <?php
                $ratingsCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
                foreach ($reviewsList as $r) {
                    $rRating = (int)$r['rating'];
                    if (isset($ratingsCounts[$rRating])) {
                        $ratingsCounts[$rRating]++;
                    }
                }
                ?>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <?php for ($stars = 5; $stars >= 1; $stars--): 
                        $count = $ratingsCounts[$stars];
                        $pct = $reviewSummary['total_reviews'] > 0 ? ($count / $reviewSummary['total_reviews']) * 100 : 0;
                    ?>
                        <div style="display: flex; align-items: center; gap: 10px; font-size: 12px; color: var(--color-gray-dark);">
                            <span style="width: 45px; text-align: right;"><?= $stars ?> stars</span>
                            <div style="flex: 1; height: 6px; background: var(--color-gray-light); border-radius: 3px; overflow: hidden;">
                                <div style="width: <?= $pct ?>%; height: 100%; background: var(--color-gray-dark); border-radius: 3px;"></div>
                            </div>
                            <span style="width: 30px; text-align: right; color: var(--color-gray);"><?= $count ?></span>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Right: Submit Form & Reviews List -->
            <div style="display: flex; flex-direction: column; gap: 40px;">
                <!-- Review Form -->
                <?php if (is_logged_in()): ?>
                    <div style="background: #fff; border: 1px solid var(--color-gray-light); padding: 30px; border-radius: var(--border-radius);">
                        <h3 style="font-family: var(--font-serif); font-size: 1.3rem; font-weight: 500; margin-bottom: 20px;">Write a Review</h3>
                        <form id="review-submit-form" onsubmit="submitReview(event)" style="display: flex; flex-direction: column; gap: 16px;">
                            <!-- Star Selection Input -->
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--color-gray); margin-bottom: 6px;">Select Rating</label>
                                <div style="display: flex; color: #d1d5db; gap: 6px; cursor: pointer;">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <svg class="star-rating-select-svg" data-val="<?= $i ?>" onclick="setReviewRating(<?= $i ?>)" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px; transition: color 0.15s;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.9 1.603-.9 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.246.582 1.817l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.9-.752 1.661-1.485 1.1l-3.97-2.883a1 1 0 00-1.175 0l-3.97 2.883c-.733.56-1.786-.2-1.485-1.1l1.518-4.674a1 1 0 00-.364-1.118L2.98 10.11c-.778-.57-.379-1.817.582-1.817h4.907a1 1 0 00.95-.69l1.519-4.674z"></path>
                                        </svg>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" id="review-rating-input" name="rating" value="0" required>
                            </div>
                            <!-- Comment Textarea -->
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--color-gray); margin-bottom: 6px;">Your Review</label>
                                <textarea name="comment" rows="4" placeholder="Share your experience with this item..." style="width: 100%; padding: 12px 14px; border: 1px solid var(--color-gray-light); border-radius: var(--border-radius); outline: none; font-size: 14px; resize: vertical;" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" style="align-self: flex-start; padding: 10px 24px;">Submit Review</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div style="background: var(--color-gray-bg); padding: 20px; border-radius: var(--border-radius); border: 1px dashed var(--color-gray-light); text-align: center;">
                        <p style="font-size: 13.5px; color: var(--color-gray-dark);">Please <a href="/login" style="color: var(--color-black); font-weight: 600; text-decoration: underline;">log in</a> to write a customer review.</p>
                    </div>
                <?php endif; ?>

                <!-- Reviews List -->
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <h3 style="font-family: var(--font-serif); font-size: 1.3rem; font-weight: 500; border-bottom: 1px solid var(--color-gray-light); padding-bottom: 12px; margin-bottom: 10px;">Reviews (<?= count($reviewsList) ?>)</h3>
                    
                    <?php if (empty($reviewsList)): ?>
                        <p style="color: var(--color-gray); font-size: 13.5px;">No reviews yet for this product. Be the first to share your thoughts!</p>
                    <?php else: ?>
                        <?php foreach ($reviewsList as $rev): ?>
                            <div style="border-bottom: 1px solid var(--color-gray-light); padding-bottom: 20px;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                    <div>
                                        <h4 style="font-size: 14px; font-weight: 600; color: var(--color-black); margin-bottom: 3px;"><?= htmlspecialchars($rev['customer_name'] ?? 'Verified Buyer') ?></h4>
                                        <div style="display: flex; color: #fbbf24; gap: 2px;">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <svg fill="<?= $i <= (int)$rev['rating'] ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24" style="width: 12px; height: 12px;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.9 1.603-.9 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.246.582 1.817l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.9-.752 1.661-1.485 1.1l-3.97-2.883a1 1 0 00-1.175 0l-3.97 2.883c-.733.56-1.786-.2-1.485-1.1l1.518-4.674a1 1 0 00-.364-1.118L2.98 10.11c-.778-.57-.379-1.817.582-1.817h4.907a1 1 0 00.95-.69l1.519-4.674z"></path>
                                                </svg>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <span style="font-size: 11px; color: var(--color-gray);"><?= date('M d, Y', strtotime($rev['created_at'])) ?></span>
                                </div>
                                <p style="font-size: 13.5px; line-height: 1.6; color: var(--color-gray-dark); white-space: pre-line;"><?= htmlspecialchars($rev['comment']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Review form script controllers
let selectedReviewRating = 0;

function setReviewRating(rating) {
    selectedReviewRating = rating;
    document.getElementById('review-rating-input').value = rating;
    
    // Highlight stars
    document.querySelectorAll('.star-rating-select-svg').forEach(svg => {
        const val = parseInt(svg.getAttribute('data-val'));
        if (val <= rating) {
            svg.setAttribute('fill', 'currentColor');
            svg.style.color = '#fbbf24';
        } else {
            svg.setAttribute('fill', 'none');
            svg.style.color = '#d1d5db';
        }
    });
}

function submitReview(event) {
    event.preventDefault();
    const ratingInput = document.getElementById('review-rating-input');
    const rating = parseInt(ratingInput.value);
    
    if (rating < 1) {
        alert('Please select a star rating first');
        return;
    }
    
    const form = event.target;
    const formData = new FormData(form);
    const comment = formData.get('comment');
    
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';
    
    const token = '<?= $_SESSION['token'] ?? '' ?>';
    const productId = '<?= $productId ?>';
    
    fetch('http://localhost:8000/products/' + productId + '/reviews', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify({
            rating: rating,
            comment: comment
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Review';
        } else {
            if (window.navigateTo) {
                window.navigateTo(window.location.href);
            } else {
                window.location.reload();
            }
        }
    })
    .catch(err => {
        console.error(err);
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Review';
    });
}
// Image gallery carousel logic
const galleryImages = <?= json_encode($galleryImages) ?>;
let activeImageIndex = 0;

function switchMainImage(index) {
    activeImageIndex = index;
    const mainImg = document.getElementById('main-product-image');
    mainImg.style.opacity = '0.3';
    setTimeout(() => {
        mainImg.src = galleryImages[index];
        mainImg.style.opacity = '1';
    }, 150);

    // Update active thumb border
    document.querySelectorAll('.thumb-img').forEach(thumb => {
        if (parseInt(thumb.getAttribute('data-index')) === index) {
            thumb.style.borderColor = 'var(--color-text-main)';
        } else {
            thumb.style.borderColor = 'transparent';
        }
    });
}

function prevImage() {
    let nextIndex = activeImageIndex - 1;
    if (nextIndex < 0) nextIndex = galleryImages.length - 1;
    switchMainImage(nextIndex);
}

function nextImage() {
    let nextIndex = activeImageIndex + 1;
    if (nextIndex >= galleryImages.length) nextIndex = 0;
    switchMainImage(nextIndex);
}

// Quantity selector logic
function adjustQty(amount) {
    const input = document.getElementById('quantity-input');
    let val = parseInt(input.value) + amount;
    if (val < 1) val = 1;
    if (val > 99) val = 99;
    input.value = val;
    validateQuantity();
}

function validateQuantity() {
    const qtyInput = document.getElementById('quantity-input');
    const qty = parseInt(qtyInput.value) || 1;
    const errorMsg = document.getElementById('qty-error-message');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    
    if (!errorMsg || !addToCartBtn) return;
    
    errorMsg.style.display = 'none';
    errorMsg.innerText = '';
    
    const variant = variants.find(v => 
        (!selectedColor || (Array.isArray(v.color) ? v.color.includes(selectedColor) : v.color === selectedColor)) && 
        (!selectedSize || (Array.isArray(v.size) ? v.size.includes(selectedSize) : v.size === selectedSize))
    );
    
    if (variant) {
        if (qty > variant.stock_qty) {
            errorMsg.innerText = `Cannot add to cart: Quantity (${qty}) exceeds available stock (${variant.stock_qty}).`;
            errorMsg.style.display = 'block';
            addToCartBtn.disabled = true;
            alert(`Cannot add to cart! Available stock for this variant is only ${variant.stock_qty}.`);
        } else if (variant.stock_qty > 0) {
            addToCartBtn.disabled = false;
        }
    }
}

// Color/Size variant matching logic
const variants = <?= json_encode($variantsMap) ?>;
const discountPercent = <?= $discountPercent ?>;
let selectedColor = <?= !empty($colors) ? json_encode($colors[0]) : 'null' ?>;
let selectedSize = null;

function selectColor(color) {
    selectedColor = color;
    document.querySelectorAll('.color-btn').forEach(btn => {
        if (btn.innerText.trim() === color) {
            btn.style.borderColor = 'var(--color-text-main)';
            btn.style.background = 'var(--color-text-main)';
            btn.style.color = 'var(--color-bg)';
        } else {
            btn.style.borderColor = 'var(--color-border-light)';
            btn.style.background = '#fff';
            btn.style.color = 'var(--color-text-main)';
        }
    });
    
    updateSizeAvailability();
    updateSelectedVariant();
}

function selectSize(size) {
    selectedSize = size;
    document.querySelectorAll('.size-btn').forEach(btn => {
        if (btn.innerText.trim() === size) {
            btn.style.background = 'var(--color-text-main)';
            btn.style.color = 'var(--color-bg)';
            btn.style.borderColor = 'var(--color-text-main)';
        } else {
            btn.style.background = '#fff';
            btn.style.color = 'var(--color-text-main)';
            btn.style.borderColor = 'var(--color-border-light)';
        }
    });
    updateSelectedVariant();
}

function updateSizeAvailability() {
    const availableSizes = [];
    variants.forEach(v => {
        const matchesColor = !selectedColor || (Array.isArray(v.color) ? v.color.includes(selectedColor) : v.color === selectedColor);
        if (matchesColor && v.stock_qty > 0) {
            if (Array.isArray(v.size)) {
                availableSizes.push(...v.size);
            } else if (v.size) {
                availableSizes.push(v.size);
            }
        }
    });
        
    document.querySelectorAll('.size-btn').forEach(btn => {
        const size = btn.innerText.trim();
        if (availableSizes.includes(size)) {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
        } else {
            btn.disabled = true;
            btn.style.opacity = '0.4';
            btn.style.cursor = 'not-allowed';
            if (selectedSize === size) {
                selectedSize = null;
                btn.style.background = '#fff';
                btn.style.color = 'var(--color-dark)';
                btn.style.borderColor = 'var(--color-gray-light)';
            }
        }
    });
}

function updateSelectedVariant() {
    const variant = variants.find(v => 
        (!selectedColor || (Array.isArray(v.color) ? v.color.includes(selectedColor) : v.color === selectedColor)) && 
        (!selectedSize || (Array.isArray(v.size) ? v.size.includes(selectedSize) : v.size === selectedSize))
    );
    
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const priceDisplay = document.getElementById('variant-price-display');
    const stockDisplay = document.getElementById('variant-stock-display');
    const hiddenInput = document.getElementById('selected-variant-id');
    const errorMsg = document.getElementById('qty-error-message');
    
    if (variant) {
        hiddenInput.value = variant.id;
        const price = variant.price;
        const discPrice = price * (1 - discountPercent / 100);
        
        if (discountPercent > 0) {
            priceDisplay.innerHTML = `
                <span style="color: var(--color-error); font-weight: 700; margin-right: 8px;">$${discPrice.toFixed(2)}</span>
                <span style="color: var(--color-error); font-size: 1.1rem; font-weight: 600; background: #fee8e6; padding: 3px 8px; border-radius: 4px; margin-right: 8px; vertical-align: middle;">-${discountPercent}%</span>
                <span style="color: var(--color-gray); text-decoration: line-through; font-size: 1.3rem;">$${price.toFixed(2)}</span>
            `;
        } else {
            priceDisplay.innerHTML = `<span style="font-weight: 700;">$${price.toFixed(2)}</span>`;
        }
        
        if (variant.stock_qty > 0) {
            stockDisplay.innerHTML = `<span style="color: var(--color-success); font-weight: 600;">In Stock (${variant.stock_qty} available)</span>`;
            addToCartBtn.disabled = false;
            validateQuantity();
        } else {
            stockDisplay.innerHTML = `<span style="color: var(--color-error); font-weight: 600;">Out of Stock</span>`;
            addToCartBtn.disabled = true;
            if (errorMsg) errorMsg.style.display = 'none';
        }
        
        // Auto-switch main image if variant has a unique image
        if (variant.image_url) {
            const variantImgIndex = galleryImages.indexOf(variant.image_url);
            if (variantImgIndex !== -1) {
                switchMainImage(variantImgIndex);
            } else {
                document.getElementById('main-product-image').src = variant.image_url;
            }
        }
    } else {
        hiddenInput.value = '';
        addToCartBtn.disabled = true;
        stockDisplay.innerHTML = `<span style="color: var(--color-gray);">Select size & color</span>`;
        if (errorMsg) errorMsg.style.display = 'none';
    }
}

// Add submission AJAX interceptor
const form = document.querySelector('.variant-form');
if (form) {
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        const btn = document.getElementById('add-to-cart-btn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="inline-block animate-spin mr-2">↻</span> Adding to Bag...';
        }
        
        const variantId = document.getElementById('selected-variant-id').value;
        const qty = document.getElementById('quantity-input').value;
        
        fetch('http://localhost:8000/cart/items', {
            method: 'POST',
            headers: getAuthHeaders(),
            body: JSON.stringify({
                session_id: getCartSessionId(),
                variant_id: variantId,
                quantity: qty
            })
        })
        .then(res => res.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = 'Add to Bag';
            }
            if (data.error) {
                alert(data.error);
            } else {
                openMiniCart();
            }
        })
        .catch(err => {
            console.error(err);
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = 'Add to Bag';
            }
            alert('Could not add item to bag. Please try again.');
        });
    });
}

// Initialize selector state
if (selectedColor) {
    selectColor(selectedColor);
} else {
    updateSizeAvailability();
    updateSelectedVariant();
}
</script>
<?php endif; ?>

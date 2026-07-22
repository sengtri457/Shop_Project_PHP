<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

use App\Core\Database;

/**
 * ============================================================================
 * CUSTOM PRODUCT & CATEGORY SEEDER SCRIPT
 * ============================================================================
 * Add your own custom categories and products in the arrays below, then run:
 * 
 *     php backend/src/Database/seeder.php
 * ============================================================================
 */

// 1. ADD YOUR CUSTOM CATEGORIES HERE
$myCategories = [
    'Tops & Shirts',
    'Hoodies & Sweatshirts',
    'Jackets & Coats',
    'Pants & Trousers',
    'Shorts',
    'Footwear',
    'Accessories'
];

// 2. ADD YOUR CUSTOM PRODUCTS HERE
// NOTE: 'img' (or 'images') supports EITHER:
//   - A single image: '/assets/images/hero_banner.png'
//   - An array of multiple images: ['/assets/images/img1.png', '/assets/images/img2.png', '/assets/images/img3.png']
//   - A comma-separated string: '/assets/images/img1.png,/assets/images/img2.png'

$myProducts = [
    // [
    //     'name'        => 'My Custom Hoodie',
    //     'brand'       => 'My Brand',
    //     'price'       => 69.99,
    //     'discount'    => 0, // Discount percentage (e.g. 10 for 10% off)
    //     'gender'      => 'unisex', // Options: 'men', 'women', 'kids', 'unisex'
    //     'category'    => 'Hoodies & Sweatshirts', // Must match one of $myCategories above
    //     'desc'        => 'Premium heavy fleece hoodie designed for everyday comfort.',
    //     // EXAMPLE: Multiple images as an array
    //     'img'         => [
    //         '/assets/images/hero_banner.png',
    //         '/assets/images/bannerMen.gif'
    //     ],
    //     'sizes'       => ['S', 'M', 'L', 'XL'],
    //     'stock'       => 50
    // ],
    // [
    //     'name'        => 'My Custom T-Shirt',
    //     'brand'       => 'My Brand',
    //     'price'       => 29.99,
    //     'discount'    => 10,
    //     'gender'      => 'men',
    //     'category'    => 'Tops & Shirts',
    //     'desc'        => 'Soft organic cotton t-shirt with modern relaxed fit.',
    //     // EXAMPLE: Single image as a string
    //     'img'         => '/assets/images/bannerMen.gif',
    //     'sizes'       => ['M', 'L', 'XL'],
    //     'stock'       => 40
    // ],
    // [
    //     'name'        => 'My Custom Jacket',
    //     'brand'       => 'My Brand',
    //     'price'       => 129.99,
    //     'discount'    => 15,
    //     'gender'      => 'women',
    //     'category'    => 'Jackets & Coats',
    //     'desc'        => 'Weather-resistant lightweight outer jacket.',
    //     // EXAMPLE: Multiple images as comma-separated string
    //     'img'         => '/assets/images/BannerWomen.avif,/assets/images/hero_banner.png',
    //     'sizes'       => ['S', 'M', 'L'],
    //     'stock'       => 25
    // ],
    [
        'name'        => 'Classic Crewneck T-Shirt',
        'brand'       => 'Nike',
        'price'       => 19.99,
        'discount'    => 20,
        'gender'      => 'men',
        'category'    => 'Tops & Shirts',
        'desc'        => 'Nike classic crewneck t-shirt crafted with quality materials for everyday wear.',
        'img'         => [
            'https://static.nike.com/a/images/t_web_pw_592_v2/f_auto/u_9ddf04c7-2a9a-4d76-add1-d15af8f0263d,c_scale,fl_relative,w_1.0,h_1.0,fl_layer_apply/404ae12d-aa11-4f00-b3ed-af2580f3fabf/M+NK+DF+24.7+IS+SHORT.png',
            'https://static.nike.com/a/images/t_web_pw_592_v2/f_auto/u_9ddf04c7-2a9a-4d76-add1-d15af8f0263d,c_scale,fl_relative,w_1.0,h_1.0,fl_layer_apply/404ae12d-aa11-4f00-b3ed-af2580f3fabf/M+NK+DF+24.7+IS+SHORT.pngg',
            'https://static.nike.com/a/images/t_web_pw_592_v2/f_auto/u_9ddf04c7-2a9a-4d76-add1-d15af8f0263d,c_scale,fl_relative,w_1.0,h_1.0,fl_layer_apply/404ae12d-aa11-4f00-b3ed-af2580f3fabf/M+NK+DF+24.7+IS+SHORT.png',
        ],
        'sizes'       => ['S', 'M', 'L', 'XL', 'XXL'],
        'stock'       => 65
    ],
];

// ============================================================================
// AUTOMATED DB SEEDER EXECUTION LOGIC
// ============================================================================

echo "Starting Custom Product Seeder...\n";

try {
    $db = Database::getConnection();

    // Clean tables before seeding
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    $db->exec("TRUNCATE TABLE product_variants");
    $db->exec("TRUNCATE TABLE product_categories");
    $db->exec("TRUNCATE TABLE products");
    $db->exec("TRUNCATE TABLE categories");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");

    $db->beginTransaction();

    // Insert Categories
    $catMap = [];
    $catStmt = $db->prepare("INSERT INTO categories (name) VALUES (?)");
    foreach ($myCategories as $catName) {
        $catStmt->execute([$catName]);
        $catMap[$catName] = (int)$db->lastInsertId();
    }
    echo "Categories created: " . count($catMap) . "\n";

    // Insert Products & Variants
    $prodInsert = $db->prepare("
        INSERT INTO products (name, description, brand, base_price, discount_percent, images, is_active, gender)
        VALUES (?, ?, ?, ?, ?, ?, 1, ?)
    ");
    $catMapStmt = $db->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)");
    $variantStmt = $db->prepare("
        INSERT INTO product_variants (product_id, sku, price, stock_qty, attributes)
        VALUES (?, ?, ?, ?, ?)
    ");

    $totalInserted = 0;

    foreach ($myProducts as $item) {
        // Support single string, array of multiple images, or comma-separated string
        $rawImg = $item['images'] ?? $item['img'] ?? '';
        if (is_array($rawImg)) {
            $formattedImages = implode(',', array_map('trim', $rawImg));
        } else {
            $formattedImages = (string)$rawImg;
        }

        $prodInsert->execute([
            $item['name'],
            $item['desc'],
            $item['brand'],
            $item['price'],
            $item['discount'],
            $formattedImages,
            $item['gender']
        ]);

        $productId = (int)$db->lastInsertId();
        $totalInserted++;

        if (isset($catMap[$item['category']])) {
            $catMapStmt->execute([$productId, $catMap[$item['category']]]);
        }

        $sizes = $item['sizes'] ?? ['S', 'M', 'L'];
        $stock = $item['stock'] ?? 30;

        foreach ($sizes as $size) {
            $sku = "SKU-" . $productId . "-" . $size . "-" . rand(100, 999);
            $attributes = json_encode(['size' => $size]);
            $variantStmt->execute([$productId, $sku, $item['price'], $stock, $attributes]);
        }
    }

    $db->commit();
    echo "Custom Seeding completed successfully! Total products created: " . $totalInserted . "\n";

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "Seeder Error: " . $e->getMessage() . "\n";
    exit(1);
}

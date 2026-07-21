<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

use App\Core\Database;

echo "Starting complete products & categories database cleanup...\n";

try {
    $db = Database::getConnection();

    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Truncate all product-related tables
    $db->exec("TRUNCATE TABLE product_variants");
    $db->exec("TRUNCATE TABLE product_categories");
    $db->exec("TRUNCATE TABLE product_tags");
    if ($db->query("SHOW TABLES LIKE 'product_reviews'")->fetch()) {
        $db->exec("TRUNCATE TABLE product_reviews");
    }
    if ($db->query("SHOW TABLES LIKE 'cart_items'")->fetch()) {
        $db->exec("TRUNCATE TABLE cart_items");
    }
    $db->exec("TRUNCATE TABLE products");
    $db->exec("TRUNCATE TABLE categories");
    $db->exec("TRUNCATE TABLE tags");

    $db->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "Database cleaned successfully! Products, categories, variants, and tags have been removed.\n";
    echo "Your database is now fresh and ready for manual product entry.\n";

} catch (Exception $e) {
    echo "Cleanup Error: " . $e->getMessage() . "\n";
    exit(1);
}

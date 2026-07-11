<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use App\Core\Database;

try {
    $db = Database::getConnection();
    
    // Check if column exists
    $stmt = $db->query("SHOW COLUMNS FROM products LIKE 'gender'");
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $db->exec("ALTER TABLE products ADD COLUMN gender VARCHAR(20) DEFAULT 'unisex' AFTER brand");
        echo "Column 'gender' added successfully to 'products' table.\n";
    } else {
        echo "Column 'gender' already exists.\n";
    }
} catch (Exception $e) {
    echo "Error during migration: " . $e->getMessage() . "\n";
}

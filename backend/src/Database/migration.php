<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

use App\Core\Database;

try {
    $db = Database::getConnection();
    
    // Check if columns already exist to avoid errors if rerun
    $stmt = $db->query("SHOW COLUMNS FROM orders LIKE 'shipping_line1'");
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $sql = "ALTER TABLE orders 
            ADD COLUMN shipping_line1 VARCHAR(255) NOT NULL DEFAULT '',
            ADD COLUMN shipping_line2 VARCHAR(255) NULL,
            ADD COLUMN shipping_city VARCHAR(100) NOT NULL DEFAULT '',
            ADD COLUMN shipping_postal_code VARCHAR(20) NULL,
            ADD COLUMN shipping_country VARCHAR(100) NOT NULL DEFAULT '',
            ADD COLUMN shipping_phone VARCHAR(30) NULL;";
            
        $db->exec($sql);
        echo "Migration successful: shipping columns added to orders table.\n";
    } else {
        echo "Migration skipped: shipping columns already exist.\n";
    }
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

use App\Core\Database;

try {
    $db = Database::getConnection();
    
    // Check if payment_method column exists in orders table
    $stmt = $db->query("SHOW COLUMNS FROM orders LIKE 'payment_method'");
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $sql = "ALTER TABLE orders 
            ADD COLUMN payment_method VARCHAR(50) NOT NULL DEFAULT 'cod',
            ADD COLUMN payment_status VARCHAR(50) NOT NULL DEFAULT 'unpaid',
            ADD COLUMN bakong_md5 VARCHAR(64) NULL;";
            
        $db->exec($sql);
        echo "Bakong migration successful: payment columns added to orders table.\n";
    } else {
        echo "Bakong migration skipped: payment columns already exist.\n";
    }
} catch (Exception $e) {
    echo "Bakong migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

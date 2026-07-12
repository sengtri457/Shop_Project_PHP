<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

use App\Core\Database;

try {
    $db = Database::getConnection();
    
    echo "Starting restock migration...\n";

    // 1. Suppliers Table
    $db->exec("CREATE TABLE IF NOT EXISTS suppliers (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(255) NOT NULL,
      contact_name VARCHAR(255) NULL,
      email VARCHAR(255) NULL,
      phone VARCHAR(30) NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "Table 'suppliers' checked/created.\n";

    // 2. Purchase Orders Table
    $db->exec("CREATE TABLE IF NOT EXISTS purchase_orders (
      id INT AUTO_INCREMENT PRIMARY KEY,
      supplier_id INT NOT NULL,
      status VARCHAR(50) NOT NULL DEFAULT 'draft',
      total_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      received_at TIMESTAMP NULL,
      FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "Table 'purchase_orders' checked/created.\n";

    // 3. Purchase Order Items Table
    $db->exec("CREATE TABLE IF NOT EXISTS purchase_order_items (
      id INT AUTO_INCREMENT PRIMARY KEY,
      purchase_order_id INT NOT NULL,
      variant_id INT NOT NULL,
      quantity INT NOT NULL,
      unit_cost DECIMAL(10,2) NOT NULL,
      FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
      FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE RESTRICT ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "Table 'purchase_order_items' checked/created.\n";

    // 4. Seed Suppliers if empty
    $stmt = $db->query("SELECT COUNT(*) as count FROM suppliers");
    $count = (int) $stmt->fetch()['count'];
    if ($count === 0) {
        $db->exec("INSERT INTO suppliers (name, contact_name, email, phone) VALUES 
            ('Nike Distribution Center', 'Michael Jordan', 'nike-orders@supplier.com', '+1 (555) 019-9023'),
            ('Adidas Restock Corp', 'Lionel Messi', 'adidas-orders@supplier.com', '+1 (555) 021-9988'),
            ('Premium Textiles Co', 'Jane Weaver', 'jane@premiumtextiles.com', '+1 (555) 043-4411')
        ");
        echo "Seed suppliers successfully inserted.\n";
    }

    echo "Restock migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

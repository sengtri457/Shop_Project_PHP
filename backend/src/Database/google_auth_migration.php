<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

$config = require __DIR__ . '/../../config/database.php';

try {
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $pdo->exec("ALTER TABLE customers ADD COLUMN google_id VARCHAR(255) NULL UNIQUE AFTER email");

    echo "Migration successful: added google_id column to customers table.\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

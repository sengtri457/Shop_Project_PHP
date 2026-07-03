<?php

namespace App\Models;

use App\Core\Database;

class Variant
{
    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT pv.*, p.name as product_name, p.base_price
            FROM product_variants pv
            JOIN products p ON pv.product_id = p.id
            WHERE pv.id = ?
        ");
        $stmt->execute([$id]);
        $variant = $stmt->fetch();

        return $variant ?: null;
    }

    public static function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $db = Database::getConnection();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $db->prepare("SELECT * FROM product_variants WHERE id IN ($placeholders)");
        $stmt->execute($ids);

        return $stmt->fetchAll();
    }

    public static function findByProduct(int $productId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM product_variants WHERE product_id = ? ORDER BY price ASC");
        $stmt->execute([$productId]);

        return $stmt->fetchAll();
    }

    public static function updateStock(int $id, int $quantity): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE product_variants SET stock_qty = ? WHERE id = ?");
        $stmt->execute([$quantity, $id]);
    }

    public static function decreaseStock(int $id, int $quantity): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE product_variants SET stock_qty = GREATEST(stock_qty - ?, 0) WHERE id = ?");
        $stmt->execute([$quantity, $id]);
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO product_variants (product_id, sku, price, stock_qty, attributes, image_url)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['product_id'],
            $data['sku'],
            $data['price'],
            $data['stock_qty'] ?? 0,
            isset($data['attributes']) ? json_encode($data['attributes']) : null,
            $data['image_url'] ?? null,
        ]);

        return (int) $db->lastInsertId();
    }
}

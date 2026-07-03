<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Product
{
    public static function all(?int $categoryId = null, ?string $search = null, ?string $brand = null): array
    {
        $db = Database::getConnection();
        $sql = "SELECT p.* FROM products p WHERE p.is_active = 1";
        $params = [];

        if ($categoryId) {
            $ids = Category::allChildIds($categoryId);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql .= " AND p.id IN (
                SELECT pc.product_id FROM product_categories pc WHERE pc.category_id IN ($placeholders)
            )";
            $params = array_merge($params, $ids);
        }

        if ($search) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if ($brand) {
            $sql .= " AND p.brand = ?";
            $params[] = $brand;
        }

        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        return $product ?: null;
    }

    public static function variants(int $productId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM product_variants WHERE product_id = ? ORDER BY price ASC");
        $stmt->execute([$productId]);

        return $stmt->fetchAll();
    }

    public static function categories(int $productId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT c.* FROM categories c
            JOIN product_categories pc ON c.id = pc.category_id
            WHERE pc.product_id = ?
        ");
        $stmt->execute([$productId]);

        return $stmt->fetchAll();
    }

    public static function tags(int $productId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT t.* FROM tags t
            JOIN product_tags pt ON t.id = pt.tag_id
            WHERE pt.product_id = ?
        ");
        $stmt->execute([$productId]);

        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO products (name, description, brand, base_price, is_active)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['brand'] ?? null,
            $data['base_price'],
            $data['is_active'] ?? 1,
        ]);

        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getConnection();
        $fields = [];
        $params = [];

        foreach (['name', 'description', 'brand', 'base_price', 'is_active'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return;
        }

        $params[] = $id;
        $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }

    public static function delete(int $id): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    }
}

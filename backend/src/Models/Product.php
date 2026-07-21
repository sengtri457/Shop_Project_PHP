<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Product
{
    public static function all(
        ?int $categoryId = null,
        ?string $search = null,
        ?string $brand = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?bool $inStock = null,
        ?array $tagIds = null,
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
        int $page = 1,
        int $limit = 20,
        ?string $gender = null
    ): array {
        $db = Database::getConnection();

        $allowedSort = ['created_at', 'name', 'base_price'];
        if (!in_array($sortBy, $allowedSort)) {
            $sortBy = 'created_at';
        }
        $sortOrder = strtolower($sortOrder) === 'asc' ? 'ASC' : 'DESC';

        $where = "WHERE p.is_active = 1";
        $params = [];

        if ($categoryId) {
            $ids = Category::allChildIds($categoryId);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $where .= " AND p.id IN (
                SELECT pc.product_id FROM product_categories pc WHERE pc.category_id IN ($placeholders)
            )";
            $params = array_merge($params, $ids);
        }

        if ($search) {
            $where .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if ($brand) {
            $where .= " AND p.brand = ?";
            $params[] = $brand;
        }

        if ($minPrice !== null) {
            $where .= " AND p.base_price >= ?";
            $params[] = $minPrice;
        }

        if ($maxPrice !== null) {
            $where .= " AND p.base_price <= ?";
            $params[] = $maxPrice;
        }

        if ($inStock) {
            $where .= " AND p.id IN (
                SELECT DISTINCT pv.product_id FROM product_variants pv WHERE pv.stock_qty > 0
            )";
        }

        if (!empty($tagIds)) {
            $placeholders = implode(',', array_fill(0, count($tagIds), '?'));
            $where .= " AND p.id IN (
                SELECT pt.product_id FROM product_tags pt WHERE pt.tag_id IN ($placeholders)
            )";
            $params = array_merge($params, $tagIds);
        }

        if ($gender) {
            $where .= " AND p.gender = ?";
            $params[] = $gender;
        }

        $countStmt = $db->prepare("SELECT COUNT(*) FROM products p $where");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $limit;

        $sql = "SELECT p.* FROM products p $where ORDER BY p.$sortBy $sortOrder LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return [
            'data' => $stmt->fetchAll(),
            'meta' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => $limit > 0 ? (int) ceil($total / $limit) : 0,
            ],
        ];
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
            INSERT INTO products (name, description, brand, gender, base_price, discount_percent, images, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['brand'] ?? null,
            $data['gender'] ?? 'unisex',
            $data['base_price'],
            $data['discount_percent'] ?? 0,
            $data['images'] ?? null,
            $data['is_active'] ?? 1,
        ]);

        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getConnection();
        $fields = [];
        $params = [];

        foreach (['name', 'description', 'brand', 'gender', 'base_price', 'discount_percent', 'images', 'is_active'] as $field) {
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

    public static function syncCategories(int $productId, array $categoryIds): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM product_categories WHERE product_id = ?");
        $stmt->execute([$productId]);

        if (empty($categoryIds)) {
            return;
        }

        $stmt = $db->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)");
        foreach ($categoryIds as $cid) {
            $stmt->execute([$productId, (int) $cid]);
        }
    }

    public static function syncTags(int $productId, array $tagIds): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM product_tags WHERE product_id = ?");
        $stmt->execute([$productId]);

        if (empty($tagIds)) {
            return;
        }

        $stmt = $db->prepare("INSERT INTO product_tags (product_id, tag_id) VALUES (?, ?)");
        foreach ($tagIds as $tid) {
            $stmt->execute([$productId, (int) $tid]);
        }
    }

    public static function bestSellers(int $limit = 10): array
    {
        $db = Database::getConnection();
        
        // Fetch top selling products based on actual completed/placed orders
        $stmt = $db->prepare("
            SELECT p.id, p.name, p.description, p.base_price, p.discount_percent, p.images, p.brand, p.gender, p.created_at,
                   CAST(SUM(oi.quantity) AS UNSIGNED) as total_sold
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN product_variants pv ON oi.variant_id = pv.id
            JOIN products p ON pv.product_id = p.id
            WHERE o.status != 'cancelled' AND p.is_active = 1
            GROUP BY p.id, p.name, p.description, p.base_price, p.discount_percent, p.images, p.brand, p.gender, p.created_at
            ORDER BY total_sold DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $orderedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $foundIds = array_column($orderedProducts, 'id');
        
        // If fewer products have order history than requested limit, append active catalog products
        if (count($orderedProducts) < $limit) {
            $needed = $limit - count($orderedProducts);
            $whereNotIn = '';
            $params = [];
            if (!empty($foundIds)) {
                $placeholders = implode(',', array_fill(0, count($foundIds), '?'));
                $whereNotIn = " AND p.id NOT IN ($placeholders)";
                $params = $foundIds;
            }
            
            $sql = "SELECT p.id, p.name, p.description, p.base_price, p.discount_percent, p.images, p.brand, p.gender, p.created_at, 0 as total_sold FROM products p WHERE p.is_active = 1 {$whereNotIn} ORDER BY p.id DESC LIMIT ?";
            $fillStmt = $db->prepare($sql);
            
            $i = 1;
            foreach ($params as $paramId) {
                $fillStmt->bindValue($i++, $paramId, PDO::PARAM_INT);
            }
            $fillStmt->bindValue($i, $needed, PDO::PARAM_INT);
            $fillStmt->execute();
            
            $additionalProducts = $fillStmt->fetchAll(PDO::FETCH_ASSOC);
            $orderedProducts = array_merge($orderedProducts, $additionalProducts);
        }
        
        // Attach variants
        return array_map(function ($product) {
            $product['variants'] = self::variants((int) $product['id']);
            return $product;
        }, $orderedProducts);
    }
}

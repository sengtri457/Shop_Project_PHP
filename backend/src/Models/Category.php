<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Category
{
    public static function all(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM categories");
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch();

        return $category ?: null;
    }

    // direct children only, e.g. Shoes -> Basketball, Running
    public static function children(int $parentId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM categories WHERE parent_id = ?");
        $stmt->execute([$parentId]);
        return $stmt->fetchAll();
    }

    // all nested child ids, so "Shoes" pulls products from Basketball + Running too
    public static function allChildIds(int $parentId): array
    {
        $db = Database::getConnection();
        $ids = [$parentId];
        $queue = [$parentId];

        while (!empty($queue)) {
            $current = array_shift($queue);
            $stmt = $db->prepare("SELECT id FROM categories WHERE parent_id = ?");
            $stmt->execute([$current]);
            $children = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($children as $childId) {
                $ids[] = $childId;
                $queue[] = $childId;
            }
        }

        return $ids;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO categories (name, parent_id) VALUES (?, ?)");
        $stmt->execute([
            $data['name'],
            $data['parent_id'] ?? null,
        ]);

        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getConnection();
        $fields = [];
        $params = [];

        foreach (['name', 'parent_id'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return;
        }

        $params[] = $id;
        $sql = "UPDATE categories SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }

    public static function delete(int $id): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function tree(): array
    {
        $all = self::all();
        $grouped = [];

        foreach ($all as $cat) {
            $cat['children'] = [];
            $grouped[$cat['id']] = $cat;
        }

        $tree = [];
        foreach ($grouped as &$cat) {
            if ($cat['parent_id'] && isset($grouped[$cat['parent_id']])) {
                $grouped[$cat['parent_id']]['children'][] = &$cat;
            } else {
                $tree[] = &$cat;
            }
        }

        return $tree;
    }

    // products in this category, including all subcategories
    public static function products(int $categoryId): array
    {
        $db = Database::getConnection();
        $ids = self::allChildIds($categoryId);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $db->prepare("
            SELECT DISTINCT p.*
            FROM products p
            JOIN product_categories pc ON p.id = pc.product_id
            WHERE pc.category_id IN ($placeholders)
            AND p.is_active = 1
        ");
        $stmt->execute($ids);

        return $stmt->fetchAll();
    }
}

<?php

namespace App\Models;

use App\Core\Database;

class Tag
{
    public static function all(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM tags ORDER BY name");

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM tags WHERE id = ?");
        $stmt->execute([$id]);
        $tag = $stmt->fetch();

        return $tag ?: null;
    }

    public static function create(string $name): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO tags (name) VALUES (?)");
        $stmt->execute([$name]);

        return (int) $db->lastInsertId();
    }

    public static function findByNames(array $names): array
    {
        if (empty($names)) {
            return [];
        }

        $db = Database::getConnection();
        $placeholders = implode(',', array_fill(0, count($names), '?'));
        $stmt = $db->prepare("SELECT * FROM tags WHERE name IN ($placeholders)");
        $stmt->execute($names);

        return $stmt->fetchAll();
    }
}

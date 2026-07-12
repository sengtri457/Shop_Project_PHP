<?php

namespace App\Models;

use App\Core\Database;

class Supplier
{
    public static function all(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM suppliers ORDER BY name ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM suppliers WHERE id = ?");
        $stmt->execute([$id]);
        $supplier = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $supplier ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO suppliers (name, contact_name, email, phone) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'] ?? '',
            $data['contact_name'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null
        ]);
        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE suppliers 
            SET name = ?, contact_name = ?, email = ?, phone = ? 
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['name'] ?? '',
            $data['contact_name'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $id
        ]);
    }

    public static function delete(int $id): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM suppliers WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

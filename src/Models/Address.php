<?php

namespace App\Models;

use App\Core\Database;

class Address
{
    public static function findByCustomer(int $customerId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM addresses WHERE customer_id = ? ORDER BY is_default DESC");
        $stmt->execute([$customerId]);

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM addresses WHERE id = ?");
        $stmt->execute([$id]);
        $address = $stmt->fetch();

        return $address ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();

        if (!empty($data['is_default'])) {
            $stmt = $db->prepare("UPDATE addresses SET is_default = 0 WHERE customer_id = ?");
            $stmt->execute([$data['customer_id']]);
        }

        $stmt = $db->prepare("
            INSERT INTO addresses (customer_id, line1, line2, city, postal_code, country, phone, is_default)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['customer_id'],
            $data['line1'],
            $data['line2'] ?? null,
            $data['city'],
            $data['postal_code'] ?? null,
            $data['country'],
            $data['phone'] ?? null,
            $data['is_default'] ?? 0,
        ]);

        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getConnection();
        $fields = [];
        $params = [];

        foreach (['line1', 'line2', 'city', 'postal_code', 'country', 'phone', 'is_default'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return;
        }

        if (!empty($data['is_default'])) {
            $address = self::find($id);
            if ($address) {
                $stmt = $db->prepare("UPDATE addresses SET is_default = 0 WHERE customer_id = ?");
                $stmt->execute([$address['customer_id']]);
            }
        }

        $params[] = $id;
        $sql = "UPDATE addresses SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }

    public static function delete(int $id): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM addresses WHERE id = ?");
        $stmt->execute([$id]);
    }
}

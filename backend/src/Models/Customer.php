<?php

namespace App\Models;

use App\Core\Database;

class Customer
{
    public static function findByEmail(string $email): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        $customer = $stmt->fetch();

        return $customer ?: null;
    }

    public static function findByGoogleId(string $googleId): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, name, email, is_admin, created_at FROM customers WHERE google_id = ?");
        $stmt->execute([$googleId]);
        $customer = $stmt->fetch();

        return $customer ?: null;
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, name, email, is_admin, created_at FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        $customer = $stmt->fetch();

        return $customer ?: null;
    }

    public static function create(string $name, string $email, string $password, ?string $googleId = null): int
    {
        $db = Database::getConnection();

        if ($googleId) {
            $stmt = $db->prepare("INSERT INTO customers (name, email, password_hash, google_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $googleId]);
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO customers (name, email, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hash]);
        }

        return (int) $db->lastInsertId();
    }

    public static function verifyPassword(string $email, string $password): ?array
    {
        $customer = self::findByEmail($email);

        if (!$customer || !password_verify($password, $customer['password_hash'])) {
            return null;
        }

        unset($customer['password_hash']);
        return $customer;
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getConnection();
        $fields = [];
        $params = [];

        foreach (['name', 'email'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        if (!empty($data['password'])) {
            $fields[] = "password_hash = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($fields)) {
            return;
        }

        $params[] = $id;
        $sql = "UPDATE customers SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }

    public static function updateGoogleId(int $id, string $googleId): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE customers SET google_id = ? WHERE id = ?");
        $stmt->execute([$googleId, $id]);
    }
}

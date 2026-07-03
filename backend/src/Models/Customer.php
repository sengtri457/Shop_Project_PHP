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

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, name, email, is_admin, created_at FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        $customer = $stmt->fetch();

        return $customer ?: null;
    }

    public static function create(string $name, string $email, string $password): int
    {
        $db = Database::getConnection();
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO customers (name, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hash]);

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
}

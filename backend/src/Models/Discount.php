<?php

namespace App\Models;

use App\Core\Database;

class Discount
{
    public static function findByCode(string $code): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM discounts WHERE code = ? AND is_active = 1");
        $stmt->execute([$code]);
        $discount = $stmt->fetch();

        return $discount ?: null;
    }

    // checks expiry, usage limit, min order amount, and whether this customer already used it
    public static function validate(array $discount, float $orderTotal, ?int $customerId): ?string
    {
        $now = date('Y-m-d H:i:s');

        if ($discount['starts_at'] && $discount['starts_at'] > $now) {
            return 'This discount is not active yet';
        }

        if ($discount['expires_at'] && $discount['expires_at'] < $now) {
            return 'This discount has expired';
        }

        if ($discount['usage_limit'] !== null && $discount['times_used'] >= $discount['usage_limit']) {
            return 'This discount has reached its usage limit';
        }

        if ($discount['min_order_amount'] !== null && $orderTotal < $discount['min_order_amount']) {
            return 'Order does not meet the minimum amount for this discount';
        }

        if ($customerId) {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT id FROM discount_usage WHERE discount_id = ? AND customer_id = ?");
            $stmt->execute([$discount['id'], $customerId]);
            if ($stmt->fetch()) {
                return 'You already used this discount';
            }
        }

        return null; // no error, it's valid
    }

    public static function calculateAmount(array $discount, float $orderTotal): float
    {
        if ($discount['type'] === 'percentage') {
            return round($orderTotal * ($discount['value'] / 100), 2);
        }

        // fixed amount, don't let it discount more than the order itself
        return min($discount['value'], $orderTotal);
    }

    public static function recordUsage(int $discountId, ?int $customerId, int $orderId): void
    {
        $db = Database::getConnection();

        $stmt = $db->prepare("INSERT INTO discount_usage (discount_id, customer_id, order_id) VALUES (?, ?, ?)");
        $stmt->execute([$discountId, $customerId, $orderId]);

        $stmt = $db->prepare("UPDATE discounts SET times_used = times_used + 1 WHERE id = ?");
        $stmt->execute([$discountId]);
    }

    public static function all(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM discounts ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM discounts WHERE id = ?");
        $stmt->execute([$id]);
        $discount = $stmt->fetch();

        return $discount ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO discounts (
                code, type, value, min_order_amount, usage_limit, starts_at, expires_at, is_active
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['code'],
            $data['type'] ?? 'fixed',
            $data['value'],
            $data['min_order_amount'] ?? null,
            $data['usage_limit'] ?? null,
            $data['starts_at'] ?? null,
            $data['expires_at'] ?? null,
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
        ]);

        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getConnection();
        $fields = [];
        $params = [];

        $allowedFields = ['code', 'type', 'value', 'min_order_amount', 'usage_limit', 'starts_at', 'expires_at', 'is_active'];
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return;
        }

        $params[] = $id;
        $sql = "UPDATE discounts SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }

    public static function delete(int $id): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM discounts WHERE id = ?");
        $stmt->execute([$id]);
    }
}

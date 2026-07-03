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
}

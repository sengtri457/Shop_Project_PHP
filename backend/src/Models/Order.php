<?php

namespace App\Models;

use App\Core\Database;

class Order
{
    public static function all(?int $customerId = null): array
    {
        $db = Database::getConnection();

        if ($customerId) {
            $stmt = $db->prepare("
                SELECT o.*, c.name as customer_name 
                FROM orders o
                JOIN customers c ON o.customer_id = c.id
                WHERE o.customer_id = ? 
                ORDER BY o.created_at DESC
            ");
            $stmt->execute([$customerId]);
        } else {
            $stmt = $db->query("
                SELECT o.*, c.name as customer_name 
                FROM orders o
                JOIN customers c ON o.customer_id = c.id
                ORDER BY o.created_at DESC
            ");
        }

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT o.*, c.name as customer_name 
            FROM orders o
            JOIN customers c ON o.customer_id = c.id
            WHERE o.id = ?
        ");
        $stmt->execute([$id]);
        $order = $stmt->fetch();

        return $order ?: null;
    }

    public static function items(int $orderId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT oi.*, pv.sku, pv.attributes, pv.image_url as variant_image, p.name as product_name, p.images as product_images
            FROM order_items oi
            JOIN product_variants pv ON oi.variant_id = pv.id
            JOIN products p ON pv.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);

        return $stmt->fetchAll();
    }

    public static function discounts(int $orderId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT od.*, d.code, d.type
            FROM order_discounts od
            JOIN discounts d ON od.discount_id = d.id
            WHERE od.order_id = ?
        ");
        $stmt->execute([$orderId]);

        return $stmt->fetchAll();
    }

    public static function create(int $customerId, array $items, int $addressId, ?array $discountData = null, string $paymentMethod = 'cod'): int
    {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $address = Address::find($addressId);
            if (!$address || (int) $address['customer_id'] !== $customerId) {
                throw new \RuntimeException("Invalid shipping address selected");
            }

            $total = 0;
            $orderItems = [];

            foreach ($items as $item) {
                $variant = Variant::find($item['variant_id']);

                if (!$variant || $variant['stock_qty'] < $item['quantity']) {
                    throw new \RuntimeException("Insufficient stock for variant {$item['variant_id']}");
                }

                $lineTotal = $variant['price'] * $item['quantity'];
                $total += $lineTotal;

                $orderItems[] = [
                    'variant_id'        => $variant['id'],
                    'quantity'          => $item['quantity'],
                    'price_at_purchase' => $variant['price'],
                ];
            }

            $discountAmount = 0;

            if ($discountData) {
                $discount = Discount::findByCode($discountData['code']);

                if (!$discount) {
                    throw new \RuntimeException('Discount not found');
                }

                $error = Discount::validate($discount, $total, $customerId);

                if ($error) {
                    throw new \RuntimeException($error);
                }

                $discountAmount = Discount::calculateAmount($discount, $total);
                $total -= $discountAmount;
            }

            $stmt = $db->prepare("
                INSERT INTO orders (
                    customer_id, status, total, payment_method, payment_status,
                    shipping_line1, shipping_line2, shipping_city,
                    shipping_postal_code, shipping_country, shipping_phone
                ) VALUES (?, 'pending', ?, ?, 'unpaid', ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $customerId,
                $total,
                $paymentMethod,
                $address['line1'],
                $address['line2'] ?? null,
                $address['city'],
                $address['postal_code'] ?? null,
                $address['country'],
                $address['phone'] ?? null
            ]);
            $orderId = (int) $db->lastInsertId();

            $stmt = $db->prepare("
                INSERT INTO order_items (order_id, variant_id, quantity, price_at_purchase)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($orderItems as $item) {
                $stmt->execute([$orderId, $item['variant_id'], $item['quantity'], $item['price_at_purchase']]);
                Variant::decreaseStock($item['variant_id'], $item['quantity']);
            }

            if ($discountData && isset($discount)) {
                $stmt = $db->prepare("
                    INSERT INTO order_discounts (order_id, discount_id, amount_saved)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$orderId, $discount['id'], $discountAmount]);

                Discount::recordUsage($discount['id'], $customerId, $orderId);
            }

            $db->commit();

            return $orderId;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function updateStatus(int $id, string $status): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }

    public static function updatePaymentStatus(int $id, string $paymentStatus, ?string $orderStatus = null, ?string $bakongMd5 = null, ?string $paymentMethod = null): void
    {
        $db = Database::getConnection();
        $updates = ["payment_status = ?"];
        $params = [$paymentStatus];

        if ($orderStatus !== null) {
            $updates[] = "status = ?";
            $params[] = $orderStatus;
        }

        if ($bakongMd5 !== null) {
            $updates[] = "bakong_md5 = ?";
            $params[] = $bakongMd5;
        }

        if ($paymentMethod !== null) {
            $updates[] = "payment_method = ?";
            $params[] = $paymentMethod;
        }

        $params[] = $id;
        $sql = "UPDATE orders SET " . implode(", ", $updates) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }
}

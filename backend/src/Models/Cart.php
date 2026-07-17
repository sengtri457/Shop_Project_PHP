<?php

namespace App\Models;

use App\Core\Database;

class Cart
{
    // find existing cart or create one
    public static function getOrCreate(?int $customerId, ?string $sessionId): array
    {
        $db = Database::getConnection();

        if ($customerId) {
            $stmt = $db->prepare("SELECT * FROM carts WHERE customer_id = ?");
            $stmt->execute([$customerId]);
        } else {
            $stmt = $db->prepare("SELECT * FROM carts WHERE session_id = ?");
            $stmt->execute([$sessionId]);
        }

        $cart = $stmt->fetch();

        if ($cart) {
            return $cart;
        }

        $stmt = $db->prepare("INSERT INTO carts (customer_id, session_id) VALUES (?, ?)");
        $stmt->execute([$customerId, $sessionId]);

        return [
            'id' => $db->lastInsertId(),
            'customer_id' => $customerId,
            'session_id' => $sessionId,
        ];
    }

    public static function items(int $cartId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT ci.id, ci.quantity, pv.id as variant_id, pv.sku, pv.price, pv.attributes, p.name, p.images, pv.image_url
            FROM cart_items ci
            JOIN product_variants pv ON ci.variant_id = pv.id
            JOIN products p ON pv.product_id = p.id
            WHERE ci.cart_id = ?
        ");
        $stmt->execute([$cartId]);
        return $stmt->fetchAll();
    }

    public static function addItem(int $cartId, int $variantId, int $quantity): void
    {
        $db = Database::getConnection();

        // if it's already in the cart, bump quantity instead of duplicating the row
        $stmt = $db->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND variant_id = ?");
        $stmt->execute([$cartId, $variantId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            $stmt = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $stmt->execute([$newQty, $existing['id']]);
            return;
        }

        $stmt = $db->prepare("INSERT INTO cart_items (cart_id, variant_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$cartId, $variantId, $quantity]);
    }

    public static function removeItem(int $cartId, int $itemId): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM cart_items WHERE cart_id = ? AND id = ?");
        $stmt->execute([$cartId, $itemId]);
    }

    public static function updateQuantity(int $cartId, int $itemId, int $quantity): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND id = ?");
        $stmt->execute([$quantity, $cartId, $itemId]);
    }

    public static function mergeGuestCart(?string $sessionId, int $customerId): void
    {
        if (empty($sessionId)) {
            return;
        }

        $db = Database::getConnection();

        // 1. Find guest cart
        $stmt = $db->prepare("SELECT id FROM carts WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        $guestCart = $stmt->fetch();

        if (!$guestCart) {
            return; // No guest cart to merge
        }

        $guestCartId = (int) $guestCart['id'];

        // 2. Find customer cart
        $stmt = $db->prepare("SELECT id FROM carts WHERE customer_id = ?");
        $stmt->execute([$customerId]);
        $customerCart = $stmt->fetch();

        if (!$customerCart) {
            // Customer doesn't have a cart yet: simple assign guest cart to customer
            $stmt = $db->prepare("UPDATE carts SET customer_id = ?, session_id = NULL WHERE id = ?");
            $stmt->execute([$customerId, $guestCartId]);
            return;
        }

        $customerCartId = (int) $customerCart['id'];

        // 3. Customer cart exists: merge items
        $stmt = $db->prepare("SELECT variant_id, quantity FROM cart_items WHERE cart_id = ?");
        $stmt->execute([$guestCartId]);
        $guestItems = $stmt->fetchAll();

        foreach ($guestItems as $item) {
            $variantId = (int) $item['variant_id'];
            $quantity = (int) $item['quantity'];

            // Check if item exists in customer cart
            $checkStmt = $db->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND variant_id = ?");
            $checkStmt->execute([$customerCartId, $variantId]);
            $existing = $checkStmt->fetch();

            if ($existing) {
                $newQty = $existing['quantity'] + $quantity;
                $updateStmt = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
                $updateStmt->execute([$newQty, $existing['id']]);
            } else {
                $insertStmt = $db->prepare("INSERT INTO cart_items (cart_id, variant_id, quantity) VALUES (?, ?, ?)");
                $insertStmt->execute([$customerCartId, $variantId, $quantity]);
            }
        }

        // 4. Delete the guest cart (cascade delete removes guest items)
        $stmt = $db->prepare("DELETE FROM carts WHERE id = ?");
        $stmt->execute([$guestCartId]);
    }

    public static function clear(int $cartId): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM cart_items WHERE cart_id = ?");
        $stmt->execute([$cartId]);
    }
}

<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Core\Response;

class CartController
{
    private function resolveCartId(): ?int
    {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];

        $customerId = $body['customer_id'] ?? $_GET['customer_id'] ?? null;
        $sessionId  = $body['session_id'] ?? $_GET['session_id'] ?? null;

        if (!$customerId && !$sessionId) {
            return null;
        }

        $cart = Cart::getOrCreate(
            $customerId ? (int) $customerId : null,
            $sessionId
        );

        return (int) $cart['id'];
    }

    public function show(): void
    {
        $cartId = $this->resolveCartId();

        if (!$cartId) {
            Response::error('customer_id or session_id is required');
            return;
        }

        $items = Cart::items($cartId);

        Response::json(['cart_id' => $cartId, 'items' => $items]);
    }

    public function addItem(): void
    {
        $cartId = $this->resolveCartId();

        if (!$cartId) {
            Response::error('customer_id or session_id is required');
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $variantId = (int) ($body['variant_id'] ?? 0);
        $quantity  = (int) ($body['quantity'] ?? 1);

        if (!$variantId || $quantity < 1) {
            Response::error('variant_id and quantity (min 1) are required');
            return;
        }

        Cart::addItem($cartId, $variantId, $quantity);

        Response::json(['message' => 'Item added to cart', 'cart_id' => $cartId]);
    }

    public function updateQuantity(int $itemId): void
    {
        $cartId = $this->resolveCartId();

        if (!$cartId) {
            Response::error('customer_id or session_id is required');
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $quantity = (int) ($body['quantity'] ?? 0);

        if ($quantity < 0) {
            Response::error('quantity must be 0 or greater');
            return;
        }

        if ($quantity === 0) {
            Cart::removeItem($cartId, $itemId);
            Response::json(['message' => 'Item removed from cart']);
            return;
        }

        Cart::updateQuantity($cartId, $itemId, $quantity);

        Response::json(['message' => 'Quantity updated']);
    }

    public function removeItem(int $itemId): void
    {
        $cartId = $this->resolveCartId();

        if (!$cartId) {
            Response::error('customer_id or session_id is required');
            return;
        }

        Cart::removeItem($cartId, $itemId);

        Response::json(['message' => 'Item removed from cart']);
    }
}

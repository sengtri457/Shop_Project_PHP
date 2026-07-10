<?php

namespace App\Controllers;

use App\Models\Order;
use App\Core\Response;
use App\Core\Auth;

class OrderController
{
    public function index(): void
    {
        $customerId = isset($_GET['customer_id']) ? (int) $_GET['customer_id'] : null;

        $orders = Order::all($customerId);

        Response::json($orders);
    }

    public function show(int $id): void
    {
        $order = Order::find($id);

        if (!$order) {
            Response::error('Order not found', 404);
            return;
        }

        $order['items']     = Order::items($id);
        $order['discounts'] = Order::discounts($id);

        Response::json($order);
    }

    public function store(): void
    {
        $body = json_decode(file_get_contents('php://input'), true);

        $customerId = (int) ($body['customer_id'] ?? 0);
        $items      = $body['items'] ?? [];
        $addressId  = (int) ($body['address_id'] ?? 0);
        $discount   = $body['discount'] ?? null;

        if (!$customerId || empty($items) || !$addressId) {
            Response::error('customer_id, items, and address_id are required');
            return;
        }

        try {
            $orderId = Order::create($customerId, $items, $addressId, $discount);
            $order = Order::find($orderId);
            $order['items']     = Order::items($orderId);
            $order['discounts'] = Order::discounts($orderId);

            Response::json($order, 201);
        } catch (\RuntimeException $e) {
            Response::error($e->getMessage());
        }
    }

    public function updateStatus(int $id): void
    {
        Auth::requireAdmin();

        $order = Order::find($id);

        if (!$order) {
            Response::error('Order not found', 404);
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $status = $body['status'] ?? '';

        $allowed = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $allowed)) {
            Response::error('Invalid status. Allowed: ' . implode(', ', $allowed));
            return;
        }

        Order::updateStatus($id, $status);

        Response::json(Order::find($id));
    }
}

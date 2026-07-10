<?php

namespace App\Controllers;

use App\Models\Discount;
use App\Core\Response;
use App\Core\Auth;

class DiscountController
{
    public function index(): void
    {
        Auth::requireAdmin();
        Response::json(Discount::all());
    }

    public function show(int $id): void
    {
        Auth::requireAdmin();
        $discount = Discount::find($id);

        if (!$discount) {
            Response::error('Discount not found', 404);
            return;
        }

        Response::json($discount);
    }

    public function store(): void
    {
        Auth::requireAdmin();

        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['code']) || empty($body['value']) || empty($body['type'])) {
            Response::error('code, value, and type are required');
            return;
        }

        if (!in_array($body['type'], ['percentage', 'fixed'])) {
            Response::error('Invalid type. Allowed: percentage, fixed');
            return;
        }

        // Check if coupon code already exists
        $existing = Discount::findByCode($body['code']);
        if ($existing) {
            Response::error('Discount code already exists');
            return;
        }

        try {
            $id = Discount::create($body);
            Response::json(Discount::find($id), 201);
        } catch (\Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function update(int $id): void
    {
        Auth::requireAdmin();

        $discount = Discount::find($id);
        if (!$discount) {
            Response::error('Discount not found', 404);
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true);

        if (isset($body['type']) && !in_array($body['type'], ['percentage', 'fixed'])) {
            Response::error('Invalid type. Allowed: percentage, fixed');
            return;
        }

        if (isset($body['code'])) {
            $existing = Discount::findByCode($body['code']);
            if ($existing && (int)$existing['id'] !== $id) {
                Response::error('Discount code already exists');
                return;
            }
        }

        try {
            Discount::update($id, $body);
            Response::json(Discount::find($id));
        } catch (\Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function destroy(int $id): void
    {
        Auth::requireAdmin();

        $discount = Discount::find($id);
        if (!$discount) {
            Response::error('Discount not found', 404);
            return;
        }

        Discount::delete($id);
        Response::json(['message' => 'Discount deleted']);
    }

    public function validateCode(): void
    {
        $body = json_decode(file_get_contents('php://input'), true);

        $code = trim($body['code'] ?? '');
        $total = isset($body['total']) ? (float)$body['total'] : null;
        $customerId = isset($body['customer_id']) ? (int)$body['customer_id'] : null;

        if (empty($code)) {
            Response::error('code is required');
            return;
        }

        if ($total === null) {
            Response::error('total is required');
            return;
        }

        $discount = Discount::findByCode($code);
        if (!$discount) {
            Response::json([
                'valid' => false,
                'error' => 'Invalid discount code'
            ]);
            return;
        }

        $error = Discount::validate($discount, $total, $customerId);
        if ($error) {
            Response::json([
                'valid' => false,
                'error' => $error
            ]);
            return;
        }

        $amountSaved = Discount::calculateAmount($discount, $total);

        Response::json([
            'valid' => true,
            'discount' => [
                'id' => $discount['id'],
                'code' => $discount['code'],
                'type' => $discount['type'],
                'value' => (float)$discount['value']
            ],
            'amount_saved' => $amountSaved
        ]);
    }
}

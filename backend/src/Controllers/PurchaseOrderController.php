<?php

namespace App\Controllers;

use App\Models\PurchaseOrder;
use App\Core\Response;
use App\Core\Auth;

class PurchaseOrderController
{
    public function index(): void
    {
        Auth::requireAdmin();
        Response::json(PurchaseOrder::all());
    }

    public function show(int $id): void
    {
        Auth::requireAdmin();
        $po = PurchaseOrder::find($id);

        if (!$po) {
            Response::error('Purchase Order not found', 404);
            return;
        }

        Response::json($po);
    }

    public function store(): void
    {
        Auth::requireAdmin();
        $body = json_decode(file_get_contents('php://input'), true);

        $supplierId = (int) ($body['supplier_id'] ?? 0);
        if ($supplierId <= 0) {
            Response::error('Valid supplier ID is required.');
            return;
        }

        $items = $body['items'] ?? [];
        if (empty($items) || !is_array($items)) {
            Response::error('Purchase order items list is required.');
            return;
        }

        // Validate items structure
        foreach ($items as $item) {
            $variantId = (int) ($item['variant_id'] ?? 0);
            $qty = (int) ($item['quantity'] ?? 0);
            $cost = (float) ($item['unit_cost'] ?? 0);

            if ($variantId <= 0 || $qty <= 0 || $cost < 0) {
                Response::error('Each item must contain valid variant_id, quantity greater than 0, and non-negative unit_cost.');
                return;
            }
        }

        try {
            $id = PurchaseOrder::create($supplierId, $items);
            Response::json(PurchaseOrder::find($id), 201);
        } catch (\Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function updateStatus(int $id): void
    {
        Auth::requireAdmin();
        $body = json_decode(file_get_contents('php://input'), true);

        $status = trim($body['status'] ?? '');
        $validStatuses = ['draft', 'ordered', 'received', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            Response::error('Invalid purchase order status. Allowed: ' . implode(', ', $validStatuses));
            return;
        }

        try {
            $success = PurchaseOrder::updateStatus($id, $status);
            if ($success) {
                Response::json(PurchaseOrder::find($id));
            } else {
                Response::error('Failed to update Purchase Order status.');
            }
        } catch (\Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function destroy(int $id): void
    {
        Auth::requireAdmin();
        try {
            $success = PurchaseOrder::delete($id);
            if ($success) {
                Response::json(['message' => 'Purchase order deleted successfully']);
            } else {
                Response::error('Purchase order not found.', 404);
            }
        } catch (\Exception $e) {
            Response::error($e->getMessage());
        }
    }
}

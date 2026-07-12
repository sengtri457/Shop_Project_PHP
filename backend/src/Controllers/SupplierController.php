<?php

namespace App\Controllers;

use App\Models\Supplier;
use App\Core\Response;
use App\Core\Auth;

class SupplierController
{
    public function index(): void
    {
        Auth::requireAdmin();
        Response::json(Supplier::all());
    }

    public function show(int $id): void
    {
        Auth::requireAdmin();
        $supplier = Supplier::find($id);

        if (!$supplier) {
            Response::error('Supplier not found', 404);
            return;
        }

        Response::json($supplier);
    }

    public function store(): void
    {
        Auth::requireAdmin();
        $body = json_decode(file_get_contents('php://input'), true);

        $name = trim($body['name'] ?? '');
        if (empty($name)) {
            Response::error('Supplier name is required');
            return;
        }

        try {
            $id = Supplier::create([
                'name' => $name,
                'contact_name' => trim($body['contact_name'] ?? '') ?: null,
                'email' => trim($body['email'] ?? '') ?: null,
                'phone' => trim($body['phone'] ?? '') ?: null
            ]);
            Response::json(Supplier::find($id), 201);
        } catch (\Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function update(int $id): void
    {
        Auth::requireAdmin();
        $body = json_decode(file_get_contents('php://input'), true);

        $supplier = Supplier::find($id);
        if (!$supplier) {
            Response::error('Supplier not found', 404);
            return;
        }

        $name = trim($body['name'] ?? '');
        if (empty($name)) {
            Response::error('Supplier name is required');
            return;
        }

        try {
            Supplier::update($id, [
                'name' => $name,
                'contact_name' => trim($body['contact_name'] ?? '') ?: null,
                'email' => trim($body['email'] ?? '') ?: null,
                'phone' => trim($body['phone'] ?? '') ?: null
            ]);
            Response::json(Supplier::find($id));
        } catch (\Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function destroy(int $id): void
    {
        Auth::requireAdmin();
        $supplier = Supplier::find($id);
        if (!$supplier) {
            Response::error('Supplier not found', 404);
            return;
        }

        try {
            Supplier::delete($id);
            Response::json(['message' => 'Supplier deleted successfully']);
        } catch (\Exception $e) {
            Response::error("Cannot delete supplier: It might be referenced by purchase orders.");
        }
    }
}

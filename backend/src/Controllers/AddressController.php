<?php

namespace App\Controllers;

use App\Models\Address;
use App\Core\Response;
use App\Core\Auth;

class AddressController
{
    public function index(): void
    {
        $customerId = Auth::id();

        if (!$customerId) {
            Response::error('Authentication required', 401);
            return;
        }

        Response::json(Address::findByCustomer($customerId));
    }

    public function show(int $id): void
    {
        $address = Address::find($id);

        if (!$address) {
            Response::error('Address not found', 404);
            return;
        }

        if ($address['customer_id'] !== Auth::id()) {
            Response::error('Forbidden', 403);
            return;
        }

        Response::json($address);
    }

    public function store(): void
    {
        $customerId = Auth::id();

        if (!$customerId) {
            Response::error('Authentication required', 401);
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['line1']) || empty($body['city']) || empty($body['country'])) {
            Response::error('line1, city, and country are required');
            return;
        }

        $body['customer_id'] = $customerId;
        $id = Address::create($body);

        Response::json(Address::find($id), 201);
    }

    public function update(int $id): void
    {
        $address = Address::find($id);

        if (!$address) {
            Response::error('Address not found', 404);
            return;
        }

        if ($address['customer_id'] !== Auth::id()) {
            Response::error('Forbidden', 403);
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true);

        Address::update($id, $body);

        Response::json(Address::find($id));
    }

    public function destroy(int $id): void
    {
        $address = Address::find($id);

        if (!$address) {
            Response::error('Address not found', 404);
            return;
        }

        if ($address['customer_id'] !== Auth::id()) {
            Response::error('Forbidden', 403);
            return;
        }

        Address::delete($id);

        Response::json(['message' => 'Address deleted']);
    }
}

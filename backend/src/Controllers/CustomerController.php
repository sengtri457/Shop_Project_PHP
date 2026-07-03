<?php

namespace App\Controllers;

use App\Models\Customer;
use App\Core\Response;
use App\Core\Auth;

class CustomerController
{
    public function show(): void
    {
        $customer = Auth::user();

        if (!$customer) {
            Response::error('Authentication required', 401);
            return;
        }

        Response::json($customer);
    }

    public function update(): void
    {
        $id = Auth::id();

        if (!$id) {
            Response::error('Authentication required', 401);
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true);

        if (isset($body['email'])) {
            $existing = Customer::findByEmail($body['email']);
            if ($existing && $existing['id'] !== $id) {
                Response::error('Email is already taken');
                return;
            }
        }

        Customer::update($id, $body);

        Response::json(Customer::find($id));
    }
}

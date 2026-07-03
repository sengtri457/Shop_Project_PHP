<?php

namespace App\Controllers;

use App\Models\Customer;
use App\Core\Response;

class AuthController
{
    public function register(): void
    {
        $body = json_decode(file_get_contents('php://input'), true);

        $name = trim($body['name'] ?? '');
        $email = trim($body['email'] ?? '');
        $password = $body['password'] ?? '';

        if (!$name || !$email || !$password) {
            Response::error('name, email, and password are required');
            return;
        }

        if (Customer::findByEmail($email)) {
            Response::error('Email is already registered');
            return;
        }

        $id = Customer::create($name, $email, $password);

        Response::json(['message' => 'Account created', 'customer_id' => $id], 201);
    }

    public function login(): void
    {
        $body = json_decode(file_get_contents('php://input'), true);

        $email = trim($body['email'] ?? '');
        $password = $body['password'] ?? '';

        $customer = Customer::verifyPassword($email, $password);

        if (!$customer) {
            Response::error('Invalid email or password', 401);
            return;
        }

        // for now this just returns the customer. once you're ready,
        // swap this for a real session or JWT token
        Response::json(['message' => 'Login successful', 'customer' => $customer]);
    }
}

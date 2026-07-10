<?php

namespace App\Controllers;

use App\Models\Customer;
use App\Core\Response;
use App\Core\JWT;

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

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Invalid email format');
            return;
        }

        if (strlen($password) < 6) {
            Response::error('Password must be at least 6 characters');
            return;
        }

        if (Customer::findByEmail($email)) {
            Response::error('Email is already registered');
            return;
        }

        $id = Customer::create($name, $email, $password);

        if (!empty($body['session_id'])) {
            \App\Models\Cart::mergeGuestCart($body['session_id'], $id);
        }

        $token = JWT::encode(['customer_id' => $id, 'email' => $email]);

        Response::json([
            'message' => 'Account created',
            'token' => $token,
            'customer' => Customer::find($id),
        ], 201);
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

        if (!empty($body['session_id'])) {
            \App\Models\Cart::mergeGuestCart($body['session_id'], (int) $customer['id']);
        }

        $token = JWT::encode([
            'customer_id' => $customer['id'],
            'email' => $customer['email'],
        ]);

        Response::json([
            'message' => 'Login successful',
            'token' => $token,
            'customer' => $customer,
        ]);
    }
}

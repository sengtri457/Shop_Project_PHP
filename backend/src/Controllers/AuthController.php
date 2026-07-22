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

    public function googleLogin(): void
    {
        $body = json_decode(file_get_contents('php://input'), true);
        $code = $body['code'] ?? '';
        $redirectUri = $body['redirect_uri'] ?? 'postmessage';

        if (!$code) {
            Response::error('Authorization code is required');
            return;
        }

        $tokenData = [
            'code' => $code,
            'client_id' => $_ENV['GOOGLE_CLIENT_ID'],
            'client_secret' => $_ENV['GOOGLE_CLIENT_SECRET'],
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ];

        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query($tokenData),
                'ignore_errors' => true,
            ],
        ];

        $tokenResponse = @file_get_contents('https://oauth2.googleapis.com/token', false, stream_context_create($opts));

        if ($tokenResponse === false) {
            Response::error('Failed to contact Google authentication server');
            return;
        }

        $tokenResult = json_decode($tokenResponse, true);
        $accessToken = $tokenResult['access_token'] ?? '';

        if (!$accessToken) {
            Response::error('Invalid authorization code');
            return;
        }

        $userInfo = @file_get_contents('https://www.googleapis.com/oauth2/v2/userinfo', false, stream_context_create([
            'http' => [
                'header' => "Authorization: Bearer $accessToken",
                'ignore_errors' => true,
            ],
        ]));

        if ($userInfo === false) {
            Response::error('Failed to fetch user info from Google');
            return;
        }

        $googleUser = json_decode($userInfo, true);
        $googleId = $googleUser['id'] ?? '';
        $email = $googleUser['email'] ?? '';
        $name = $googleUser['name'] ?? 'Google User';

        if (!$googleId || !$email) {
            Response::error('Failed to retrieve user information from Google');
            return;
        }

        $customer = Customer::findByGoogleId($googleId);

        if (!$customer) {
            $existing = Customer::findByEmail($email);
            if ($existing) {
                Customer::updateGoogleId((int) $existing['id'], $googleId);
                $customer = Customer::find((int) $existing['id']);
            } else {
                $randomPassword = bin2hex(random_bytes(20));
                $id = Customer::create($name, $email, $randomPassword, $googleId);
                $customer = Customer::find($id);
            }
        }

        if (!empty($body['session_id'])) {
            \App\Models\Cart::mergeGuestCart($body['session_id'], (int) $customer['id']);
        }

        $token = JWT::encode([
            'customer_id' => $customer['id'],
            'email' => $customer['email'],
        ]);

        Response::json([
            'message' => 'Google login successful',
            'token' => $token,
            'customer' => $customer,
        ]);
    }
}

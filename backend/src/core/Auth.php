<?php

namespace App\Core;

use App\Models\Customer;

class Auth
{
    private static ?array $user = null;

    public static function user(): ?array
    {
        if (self::$user !== null) {
            return self::$user;
        }

        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return null;
        }

        $decoded = JWT::decode($matches[1]);

        if (!$decoded || !isset($decoded->customer_id)) {
            return null;
        }

        self::$user = Customer::find($decoded->customer_id);

        return self::$user;
    }

    public static function require(): void
    {
        if (!self::user()) {
            Response::error('Authentication required', 401);
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::require();

        if (empty(self::$user['is_admin'])) {
            Response::error('Admin access required', 403);
            exit;
        }
    }

    public static function id(): ?int
    {
        $user = self::user();
        return $user['id'] ?? null;
    }
}

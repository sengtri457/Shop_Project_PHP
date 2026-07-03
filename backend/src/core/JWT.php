<?php

namespace App\Core;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT
{
    private static function secret(): string
    {
        return $_ENV['JWT_SECRET'] ?? 'change-me-in-production-needs-at-least-32-chars';
    }

    public static function encode(array $payload): string
    {
        $payload['iat'] = time();
        $payload['exp'] = time() + 86400 * 7;

        return FirebaseJWT::encode($payload, self::secret(), 'HS256');
    }

    public static function decode(string $token): ?object
    {
        try {
            return FirebaseJWT::decode($token, new Key(self::secret(), 'HS256'));
        } catch (\Exception) {
            return null;
        }
    }
}

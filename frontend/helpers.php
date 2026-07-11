<?php

define('API_BASE', 'http://localhost:8000');

function api_request(string $method, string $path, array $data = null): array
{
    $url = API_BASE . $path;
    $headers = ['Content-Type: application/json'];

    $token = $_SESSION['token'] ?? '';
    if ($token) {
        $headers[] = "Authorization: Bearer $token";
    }

    $opts = [
        'http' => [
            'method' => $method,
            'header' => $headers,
            'ignore_errors' => true,
        ],
    ];

    if ($data !== null) {
        $opts['http']['content'] = json_encode($data);
    }

    $body = @file_get_contents($url, false, stream_context_create($opts));

    $code = 0;
    if (isset($http_response_header[0])) {
        preg_match('/\d{3}/', $http_response_header[0], $m);
        $code = (int) ($m[0] ?? 0);
    }

    if ($body === false) {
        return [
            'code' => 503,
            'data' => ['error' => 'Backend server unavailable'],
        ];
    }

    return [
        'code' => $code,
        'data' => json_decode($body, true),
    ];
}

function api_get(string $path): array
{
    return api_request('GET', $path);
}

function api_post(string $path, array $data): array
{
    return api_request('POST', $path, $data);
}

function api_put(string $path, array $data): array
{
    return api_request('PUT', $path, $data);
}

function api_delete(string $path): array
{
    return api_request('DELETE', $path);
}

function cart_session_id(): string
{
    if (empty($_SESSION['cart_id'])) {
        $_SESSION['cart_id'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['cart_id'];
}

function is_logged_in(): bool
{
    return !empty($_SESSION['token']);
}

function is_admin(): bool
{
    return !empty($_SESSION['is_admin']);
}

function redirect(string $path): void
{
    if (!headers_sent()) {
        header("Location: $path");
        exit;
    }
    
    // Fallback if headers are already sent
    echo '<script type="text/javascript">window.location.href = ' . json_encode($path) . ';</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($path) . '"></noscript>';
    exit;
}

function old(string $key, string $default = ''): string
{
    return $_SESSION['_old'][$key] ?? $default;
}

function error(string $key = ''): string
{
    if ($key) {
        return $_SESSION['_errors'][$key] ?? '';
    }
    return $_SESSION['_errors']['_global'] ?? '';
}

function has_errors(): bool
{
    return !empty($_SESSION['_errors']);
}

function flash(string $key): string
{
    $val = $_SESSION['_flash'][$key] ?? '';
    unset($_SESSION['_flash'][$key]);
    return $val;
}

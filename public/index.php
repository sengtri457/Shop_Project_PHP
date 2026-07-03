<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\OrderController;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$router = new Router();

// ---- Auth ----
$router->post('/auth/register', [AuthController::class, 'register']);
$router->post('/auth/login',    [AuthController::class, 'login']);

// ---- Categories ----
$router->get('/categories',    [CategoryController::class, 'index']);
$router->get('/categories/{id}', [CategoryController::class, 'show']);

// ---- Products ----
$router->get('/products',       [ProductController::class, 'index']);
$router->get('/products/{id}',  [ProductController::class, 'show']);
$router->post('/products',      [ProductController::class, 'store']);

// ---- Cart ----
$router->get('/cart',                    [CartController::class, 'show']);
$router->post('/cart/items',             [CartController::class, 'addItem']);
$router->patch('/cart/items/{itemId}',   [CartController::class, 'updateQuantity']);
$router->delete('/cart/items/{itemId}',  [CartController::class, 'removeItem']);

// ---- Orders ----
$router->get('/orders',       [OrderController::class, 'index']);
$router->post('/orders',      [OrderController::class, 'store']);
$router->get('/orders/{id}',  [OrderController::class, 'show']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

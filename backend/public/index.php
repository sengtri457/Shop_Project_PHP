<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\OrderController;
use App\Controllers\AddressController;
use App\Controllers\CustomerController;
use App\Controllers\TagController;
use App\Controllers\DiscountController;
use App\Controllers\SupplierController;
use App\Controllers\PurchaseOrderController;
use App\Controllers\ProductReviewController;
use App\Controllers\PaymentController;

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
$router->post('/auth/google',   [AuthController::class, 'googleLogin']);

// ---- Categories ----
$router->get('/categories',        [CategoryController::class, 'index']);
$router->get('/categories/tree',   [CategoryController::class, 'tree']);
$router->get('/categories/{id}',   [CategoryController::class, 'show']);
$router->post('/categories',       [CategoryController::class, 'store']);
$router->put('/categories/{id}',   [CategoryController::class, 'update']);
$router->patch('/categories/{id}', [CategoryController::class, 'update']);
$router->delete('/categories/{id}',[CategoryController::class, 'destroy']);

// ---- Products ----
$router->get('/products',             [ProductController::class, 'index']);
$router->get('/products/best-sellers', [ProductController::class, 'bestSellers']);
$router->get('/products/{id}',        [ProductController::class, 'show']);
$router->post('/products',      [ProductController::class, 'store']);
$router->put('/products/{id}',  [ProductController::class, 'update']);
$router->patch('/products/{id}',[ProductController::class, 'update']);
$router->delete('/products/{id}',[ProductController::class, 'destroy']);

// ---- Cart ----
$router->get('/cart',                    [CartController::class, 'show']);
$router->post('/cart/items',             [CartController::class, 'addItem']);
$router->patch('/cart/items/{itemId}',   [CartController::class, 'updateQuantity']);
$router->delete('/cart/items/{itemId}',  [CartController::class, 'removeItem']);
$router->delete('/cart',                 [CartController::class, 'clear']);

// ---- Orders ----
$router->get('/orders',            [OrderController::class, 'index']);
$router->post('/orders',           [OrderController::class, 'store']);
$router->get('/orders/{id}',       [OrderController::class, 'show']);
$router->patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
$router->get('/admin/stats', [OrderController::class, 'adminStats']);

// ---- Payments (Bakong KHQR) ----
$router->get('/payments/bakong/config',    [PaymentController::class, 'config']);
$router->post('/payments/bakong/generate', [PaymentController::class, 'generateBakongQr']);
$router->post('/payments/bakong/check',    [PaymentController::class, 'checkBakongStatus']);

// ---- Suppliers ----
$router->get('/suppliers',             [SupplierController::class, 'index']);
$router->post('/suppliers',            [SupplierController::class, 'store']);
$router->get('/suppliers/{id}',        [SupplierController::class, 'show']);
$router->put('/suppliers/{id}',        [SupplierController::class, 'update']);
$router->delete('/suppliers/{id}',     [SupplierController::class, 'destroy']);

// ---- Purchase Orders ----
$router->get('/purchase-orders',            [PurchaseOrderController::class, 'index']);
$router->post('/purchase-orders',           [PurchaseOrderController::class, 'store']);
$router->get('/purchase-orders/{id}',       [PurchaseOrderController::class, 'show']);
$router->patch('/purchase-orders/{id}/status', [PurchaseOrderController::class, 'updateStatus']);
$router->delete('/purchase-orders/{id}',     [PurchaseOrderController::class, 'destroy']);

// ---- Customer Profile ----
$router->get('/customer',    [CustomerController::class, 'show']);
$router->patch('/customer',  [CustomerController::class, 'update']);

// ---- Addresses ----
$router->get('/addresses',       [AddressController::class, 'index']);
$router->get('/addresses/{id}',  [AddressController::class, 'show']);
$router->post('/addresses',      [AddressController::class, 'store']);
$router->put('/addresses/{id}',  [AddressController::class, 'update']);
$router->patch('/addresses/{id}',[AddressController::class, 'update']);
$router->delete('/addresses/{id}',[AddressController::class, 'destroy']);

// ---- Tags ----
$router->get('/tags',      [TagController::class, 'index']);
$router->get('/tags/{id}', [TagController::class, 'show']);
$router->post('/tags',     [TagController::class, 'store']);

// ---- Discounts ----
$router->get('/discounts',             [DiscountController::class, 'index']);
$router->get('/discounts/{id}',        [DiscountController::class, 'show']);
$router->post('/discounts',            [DiscountController::class, 'store']);
$router->put('/discounts/{id}',        [DiscountController::class, 'update']);
$router->patch('/discounts/{id}',      [DiscountController::class, 'update']);
$router->delete('/discounts/{id}',     [DiscountController::class, 'destroy']);
$router->post('/discounts/validate',   [DiscountController::class, 'validateCode']);

// ---- Reviews ----
$router->get('/products/{id}/reviews',  [ProductReviewController::class, 'index']);
$router->post('/products/{id}/reviews', [ProductReviewController::class, 'store']);

$uri   = $_SERVER['REQUEST_URI'];
$uri   = parse_url($uri, PHP_URL_PATH);
$base  = dirname($_SERVER['SCRIPT_NAME']);
if ($base !== '/' && $base !== '\\') {
    $uri = substr($uri, strlen($base));
}
$uri = rtrim($uri, '/') ?: '/';

$router->dispatch($_SERVER['REQUEST_METHOD'], $uri);

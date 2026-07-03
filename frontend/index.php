<?php

session_start();

require_once __DIR__ . '/helpers.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

$view = null;
$params = [];

if (preg_match('#^/admin(/.*)?$#', $uri, $m)) {
    $adminPath = $m[1] ?: '/';
    route_admin($adminPath, $method);
} else {
    route_public($uri, $method);
}

function render(string $view, array $data = []): void
{
    extract($data);
    $viewFile = __DIR__ . "/views/$view.php";
    if (!file_exists($viewFile)) {
        http_response_code(404);
        echo "View not found";
        return;
    }
    require __DIR__ . '/views/partials/header.php';
    require $viewFile;
    require __DIR__ . '/views/partials/footer.php';
}

function route_public(string $uri, string $method): void
{
    switch (true) {
        case $uri === '/':
            render('home');
            break;

        case $uri === '/products':
            render('products');
            break;

        case preg_match('#^/products/(\d+)$#', $uri, $m):
            render('product', ['productId' => (int) $m[1]]);
            break;

        case $uri === '/cart':
            if ($method === 'POST') {
                handle_add_to_cart();
            } else {
                render('cart');
            }
            break;

        case $uri === '/checkout':
            render('checkout');
            break;

        case $uri === '/order':
            render('order');
            break;

        case $uri === '/login':
            if ($method === 'POST') {
                handle_login();
            } else {
                render('login');
            }
            break;

        case $uri === '/register':
            if ($method === 'POST') {
                handle_register();
            } else {
                render('register');
            }
            break;

        case $uri === '/logout':
            session_destroy();
            redirect('/');
            break;

        default:
            http_response_code(404);
            render('home', ['notFound' => true]);
    }
}

function route_admin(string $path, string $method): void
{
    if (!is_logged_in()) {
        $_SESSION['_flash']['error'] = 'Please login first';
        redirect('/login');
    }

    if (!is_admin()) {
        http_response_code(403);
        echo '<h1>403 Forbidden</h1><p>Admin access required.</p>';
        exit;
    }

    switch (true) {
        case $path === '/':
            render('admin/dashboard');
            break;

        case $path === '/products':
            $view = 'admin/products';
            if ($method === 'POST') handle_admin_product_store();
            elseif (isset($_GET['edit'])) $view = 'admin/product-form';
            elseif (isset($_GET['delete'])) handle_admin_product_delete();
            render($view);
            break;

        case $path === '/products/new':
            render('admin/product-form');
            break;

        case preg_match('#^/products/(\d+)/edit$#', $path, $m):
            render('admin/product-form', ['productId' => (int) $m[1]]);
            break;

        case $path === '/orders':
            render('admin/orders');
            break;

        case $path === '/categories':
            if ($method === 'POST') handle_admin_category_store();
            render('admin/categories');
            break;

        default:
            http_response_code(404);
            echo '<h1>404</h1>';
    }
}

function handle_add_to_cart(): void
{
    $variantId = (int) ($_POST['variant_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 1);

    $result = api_post('/cart/items', [
        'session_id' => cart_session_id(),
        'variant_id' => $variantId,
        'quantity' => $quantity,
    ]);

    $productId = (int) ($_POST['product_id'] ?? 0);
    $redirectTo = $productId ? "/products/$productId" : '/cart';

    if ($result['code'] === 200) {
        $_SESSION['_flash']['success'] = 'Added to cart';
    } else {
        $_SESSION['_flash']['error'] = $result['data']['error'] ?? 'Failed to add';
    }

    redirect($redirectTo);
}

function handle_login(): void
{
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = api_post('/auth/login', [
        'email' => $email,
        'password' => $password,
    ]);

    if ($result['code'] === 200) {
        $_SESSION['token'] = $result['data']['token'];
        $_SESSION['customer'] = $result['data']['customer'];
        $_SESSION['is_admin'] = !empty($result['data']['customer']['is_admin']);

        $_SESSION['_flash']['success'] = 'Welcome back, ' . ($result['data']['customer']['name'] ?? '') . '!';

        if (!empty($_SESSION['is_admin'])) {
            redirect('/admin');
        }
        redirect('/');
    }

    $_SESSION['_errors'] = ['_global' => $result['data']['error'] ?? 'Login failed'];
    $_SESSION['_old'] = ['email' => $email];
    redirect('/login');
}

function handle_register(): void
{
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = api_post('/auth/register', [
        'name' => $name,
        'email' => $email,
        'password' => $password,
    ]);

    if ($result['code'] === 201) {
        $_SESSION['_flash']['success'] = 'Account created! Please login.';
        redirect('/login');
    }

    $_SESSION['_errors'] = [];
    if (isset($result['data']['error'])) {
        $_SESSION['_errors']['_global'] = $result['data']['error'];
    }
    $_SESSION['_old'] = ['name' => $name, 'email' => $email];
    redirect('/register');
}

function handle_admin_product_store(): void
{
    $result = api_post('/products', [
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'brand' => $_POST['brand'] ?? '',
        'base_price' => (float) ($_POST['base_price'] ?? 0),
        'category_ids' => !empty($_POST['category_ids']) ? array_map('intval', (array) $_POST['category_ids']) : [],
        'tag_ids' => !empty($_POST['tag_ids']) ? array_map('intval', (array) $_POST['tag_ids']) : [],
    ]);

    if ($result['code'] === 201) {
        $_SESSION['_flash']['success'] = 'Product created';
    } else {
        $_SESSION['_flash']['error'] = $result['data']['error'] ?? 'Failed to create';
    }
    redirect('/admin/products');
}

function handle_admin_product_delete(): void
{
    $id = (int) ($_GET['delete'] ?? 0);
    if ($id) {
        api_delete("/products/$id");
        $_SESSION['_flash']['success'] = 'Product deleted';
    }
    redirect('/admin/products');
}

function handle_admin_category_store(): void
{
    $result = api_post('/categories', [
        'name' => $_POST['name'] ?? '',
        'parent_id' => !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null,
    ]);

    if ($result['code'] === 201) {
        $_SESSION['_flash']['success'] = 'Category created';
    } else {
        $_SESSION['_flash']['error'] = $result['data']['error'] ?? 'Failed to create';
    }
    redirect('/admin/categories');
}

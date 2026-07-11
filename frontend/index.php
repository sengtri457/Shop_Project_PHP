<?php

// Route static files directly when using the PHP built-in server
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

session_start();


require_once __DIR__ . '/helpers.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

$view = null;
$params = [];

if (preg_match('#^/admin(/.*)?$#', $uri, $m)) {
    $adminPath = $m[1] ?? '/';
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

    // Clear errors and old inputs after render to prevent sticking
    unset($_SESSION['_errors']);
    unset($_SESSION['_old']);
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

        case $uri === '/cart/update':
            if ($method === 'POST') {
                handle_update_cart_quantity();
            }
            break;

        case $uri === '/cart/remove':
            if ($method === 'POST') {
                handle_remove_from_cart();
            }
            break;

        case $uri === '/checkout':
            render('checkout');
            break;

        case $uri === '/order':
            render('order');
            break;

        case preg_match('#^/orders/(\d+)$#', $uri, $m):
            if (!is_logged_in()) {
                $_SESSION['_flash']['error'] = 'Please login first';
                redirect('/login');
            }
            render('order-detail', ['orderId' => (int) $m[1]]);
            break;

        case preg_match('#^/orders/(\d+)/status$#', $uri, $m):
            if (!is_logged_in() || !is_admin()) {
                $_SESSION['_flash']['error'] = 'Admin access required';
                redirect('/');
            }
            if ($method === 'POST') {
                handle_admin_order_status_update((int) $m[1]);
            }
            break;

        case $uri === '/customer/addresses':
            if (!is_logged_in()) {
                $_SESSION['_flash']['error'] = 'Please login first';
                redirect('/login');
            }
            if ($method === 'POST') {
                handle_customer_address_store();
            } else {
                if (isset($_GET['delete'])) {
                    handle_customer_address_delete((int) $_GET['delete']);
                } else {
                    render('customer/addresses');
                }
            }
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
            if ($method === 'POST') {
                handle_admin_product_store();
            } else {
                render('admin/product-form');
            }
            break;

        case preg_match('#^/products/(\d+)/edit$#', $path, $m):
            $pId = (int) $m[1];
            if ($method === 'POST') {
                handle_admin_product_update($pId);
            } else {
                render('admin/product-form', ['productId' => $pId]);
            }
            break;

        case $path === '/orders':
            render('admin/orders');
            break;

        case $path === '/categories':
            if ($method === 'POST') {
                if (isset($_GET['edit_cat'])) {
                    handle_admin_category_update((int) $_GET['edit_cat']);
                } else {
                    handle_admin_category_store();
                }
            } elseif (isset($_GET['delete_cat'])) {
                handle_admin_category_delete((int) $_GET['delete_cat']);
            }
            render('admin/categories');
            break;

        case $path === '/discounts':
            if ($method === 'POST') {
                if (isset($_GET['edit'])) {
                    handle_admin_discount_update((int) $_GET['edit']);
                } else {
                    handle_admin_discount_store();
                }
            } elseif (isset($_GET['delete'])) {
                handle_admin_discount_delete((int) $_GET['delete']);
            }
            render('admin/discounts');
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
        'session_id' => cart_session_id(),
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
        'session_id' => cart_session_id(),
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
    $payload = [
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'brand' => $_POST['brand'] ?? '',
        'gender' => $_POST['gender'] ?? 'unisex',
        'base_price' => (float) ($_POST['base_price'] ?? 0),
        'discount_percent' => (int) ($_POST['discount_percent'] ?? 0),
        'images' => trim($_POST['images'] ?? ''),
        'category_ids' => !empty($_POST['category_ids']) ? array_map('intval', (array) $_POST['category_ids']) : [],
        'tag_ids' => !empty($_POST['tag_ids']) ? array_map('intval', (array) $_POST['tag_ids']) : [],
    ];

    if (isset($_POST['variants'])) {
        $payload['variants'] = $_POST['variants'];
    }

    $result = api_post('/products', $payload);

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

function handle_admin_product_update(int $id): void
{
    $payload = [
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'brand' => $_POST['brand'] ?? '',
        'gender' => $_POST['gender'] ?? 'unisex',
        'base_price' => (float) ($_POST['base_price'] ?? 0),
        'discount_percent' => (int) ($_POST['discount_percent'] ?? 0),
        'images' => trim($_POST['images'] ?? ''),
        'category_ids' => !empty($_POST['category_ids']) ? array_map('intval', (array) $_POST['category_ids']) : [],
        'tag_ids' => !empty($_POST['tag_ids']) ? array_map('intval', (array) $_POST['tag_ids']) : [],
    ];

    if (isset($_POST['variants'])) {
        $payload['variants'] = $_POST['variants'];
    }

    $result = api_put("/products/$id", $payload);

    if ($result['code'] === 200) {
        $_SESSION['_flash']['success'] = 'Product updated';
        redirect('/admin/products');
    } else {
        $_SESSION['_flash']['error'] = $result['data']['error'] ?? 'Failed to update product';
        redirect("/admin/products/$id/edit");
    }
}

function handle_customer_address_store(): void
{
    $result = api_post('/addresses', [
        'line1' => $_POST['line1'] ?? '',
        'line2' => $_POST['line2'] ?? '',
        'city' => $_POST['city'] ?? '',
        'postal_code' => $_POST['postal_code'] ?? '',
        'country' => $_POST['country'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'is_default' => isset($_POST['is_default']) ? 1 : 0,
    ]);

    if ($result['code'] === 201) {
        $_SESSION['_flash']['success'] = 'Address added';
    } else {
        $_SESSION['_flash']['error'] = $result['data']['error'] ?? 'Failed to add address';
    }
    redirect('/customer/addresses');
}

function handle_customer_address_delete(int $id): void
{
    $result = api_delete("/addresses/$id");

    if ($result['code'] === 200) {
        $_SESSION['_flash']['success'] = 'Address deleted';
    } else {
        $_SESSION['_flash']['error'] = $result['data']['error'] ?? 'Failed to delete address';
    }
    redirect('/customer/addresses');
}

function handle_update_cart_quantity(): void
{
    $itemId = (int) ($_POST['item_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 1);

    $result = api_request('PATCH', "/cart/items/$itemId", [
        'session_id' => cart_session_id(),
        'quantity' => $quantity,
    ]);

    if ($result['code'] === 200) {
        $_SESSION['_flash']['success'] = 'Cart updated';
    } else {
        $_SESSION['_flash']['error'] = $result['data']['error'] ?? 'Failed to update cart';
    }

    redirect('/cart');
}

function handle_remove_from_cart(): void
{
    $itemId = (int) ($_POST['item_id'] ?? 0);

    $result = api_request('DELETE', "/cart/items/$itemId?session_id=" . cart_session_id());

    if ($result['code'] === 200) {
        $_SESSION['_flash']['success'] = 'Item removed from cart';
    } else {
        $_SESSION['_flash']['error'] = $result['data']['error'] ?? 'Failed to remove item';
    }

    redirect('/cart');
}

function handle_admin_order_status_update(int $orderId): void
{
    $status = $_POST['status'] ?? '';

    $result = api_request('PATCH', "/orders/$orderId/status", [
        'status' => $status
    ]);

    if ($result['code'] === 200) {
        $_SESSION['_flash']['success'] = 'Order status updated';
    } else {
        $_SESSION['_flash']['error'] = $result['data']['error'] ?? 'Failed to update order status';
    }

    redirect("/orders/$orderId");
}

function handle_admin_category_update(int $id): void
{
    $result = api_put("/categories/$id", [
        'name' => $_POST['name'] ?? '',
        'parent_id' => !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null,
    ]);

    if ($result['code'] === 200) {
        $_SESSION['_flash']['success'] = 'Category updated successfully';
    } else {
        $_SESSION['_flash']['error'] = $result['data']['error'] ?? 'Failed to update category';
    }
    redirect('/admin/categories');
}

function handle_admin_category_delete(int $id): void
{
    $result = api_delete("/categories/$id");

    if ($result['code'] === 200) {
        $_SESSION['_flash']['success'] = 'Category deleted successfully';
    } else {
        $_SESSION['_flash']['error'] = $result['data']['error'] ?? 'Failed to delete category';
    }
    redirect('/admin/categories');
}

function handle_admin_discount_store(): void
{
    $payload = [
        'code' => trim($_POST['code'] ?? ''),
        'type' => $_POST['type'] ?? 'fixed',
        'value' => (float) ($_POST['value'] ?? 0),
        'min_order_amount' => !empty($_POST['min_order_amount']) ? (float) $_POST['min_order_amount'] : null,
        'usage_limit' => !empty($_POST['usage_limit']) ? (int) $_POST['usage_limit'] : null,
        'starts_at' => !empty($_POST['starts_at']) ? str_replace('T', ' ', $_POST['starts_at']) . ':00' : null,
        'expires_at' => !empty($_POST['expires_at']) ? str_replace('T', ' ', $_POST['expires_at']) . ':00' : null,
        'is_active' => (int) ($_POST['is_active'] ?? 1),
    ];

    $result = api_post('/discounts', $payload);

    if ($result['code'] === 201) {
        $_SESSION['_flash']['success'] = 'Discount code created successfully';
    } else {
        $_SESSION['_flash']['error'] = $result['data']['error'] ?? 'Failed to create discount code';
    }
    redirect('/admin/discounts');
}

function handle_admin_discount_update(int $id): void
{
    $payload = [
        'code' => trim($_POST['code'] ?? ''),
        'type' => $_POST['type'] ?? 'fixed',
        'value' => (float) ($_POST['value'] ?? 0),
        'min_order_amount' => !empty($_POST['min_order_amount']) ? (float) $_POST['min_order_amount'] : null,
        'usage_limit' => !empty($_POST['usage_limit']) ? (int) $_POST['usage_limit'] : null,
        'starts_at' => !empty($_POST['starts_at']) ? str_replace('T', ' ', $_POST['starts_at']) . ':00' : null,
        'expires_at' => !empty($_POST['expires_at']) ? str_replace('T', ' ', $_POST['expires_at']) . ':00' : null,
        'is_active' => (int) ($_POST['is_active'] ?? 1),
    ];

    $result = api_put("/discounts/$id", $payload);

    if ($result['code'] === 200) {
        $_SESSION['_flash']['success'] = 'Discount code updated successfully';
    } else {
        $_SESSION['_flash']['error'] = $result['data']['error'] ?? 'Failed to update discount code';
    }
    redirect('/admin/discounts');
}

function handle_admin_discount_delete(int $id): void
{
    $result = api_delete("/discounts/$id");

    if ($result['code'] === 200) {
        $_SESSION['_flash']['success'] = 'Discount code deleted successfully';
    } else {
        $_SESSION['_flash']['error'] = $result['data']['error'] ?? 'Failed to delete discount code';
    }
    redirect('/admin/discounts');
}

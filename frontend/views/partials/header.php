<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | Shop' : 'Shop - Clean & Modern E-Commerce' ?></title>
    <meta name="description" content="<?= isset($metaDescription) ? htmlspecialchars($metaDescription) : 'Discover a curated collection of premium clothing, variants, sizes, and colors.' ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php
    $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $currentUri = rtrim($currentUri, '/') ?: '/';
    
    function isActive(string $path, string $currentUri): string {
        if ($path === '/' && $currentUri === '/') return 'active';
        if ($path !== '/' && strpos($currentUri, $path) === 0) return 'active';
        return '';
    }
    ?>
    <nav class="navbar">
        <div class="container">
            <a href="/" class="logo">Shop</a>
            
            <div class="nav-links">
                <a href="/" class="<?= isActive('/', $currentUri) ?>">Home</a>
                <a href="/products" class="<?= isActive('/products', $currentUri) ?>">Products</a>
                <a href="/cart" class="<?= isActive('/cart', $currentUri) ?>">Cart</a>
                <?php if (is_logged_in()): ?>
                    <span class="nav-user">
                        <?= htmlspecialchars($_SESSION['customer']['name'] ?? '') ?>
                    </span>
                    <?php if (is_admin()): ?>
                        <a href="/admin" class="<?= isActive('/admin', $currentUri) ?>">Admin</a>
                    <?php endif; ?>
                    <a href="/customer/addresses" class="<?= isActive('/customer/addresses', $currentUri) ?>">My Addresses</a>
                    <a href="/logout">Logout</a>
                <?php else: ?>
                    <a href="/login" class="<?= isActive('/login', $currentUri) ?>">Login</a>
                    <a href="/register" class="<?= isActive('/register', $currentUri) ?>">Register</a>
                <?php endif; ?>
            </div>

            <button class="hamburger-toggle" onclick="toggleMobileNav()" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <div class="mobile-nav" id="mobileNavMenu">
            <a href="/" class="<?= isActive('/', $currentUri) ?>" onclick="toggleMobileNav()">Home</a>
            <a href="/products" class="<?= isActive('/products', $currentUri) ?>" onclick="toggleMobileNav()">Products</a>
            <a href="/cart" class="<?= isActive('/cart', $currentUri) ?>" onclick="toggleMobileNav()">Cart</a>
            <?php if (is_logged_in()): ?>
                <span class="nav-user" style="display:inline-block; margin-bottom: 8px;">
                    <?= htmlspecialchars($_SESSION['customer']['name'] ?? '') ?>
                </span>
                <?php if (is_admin()): ?>
                    <a href="/admin" class="<?= isActive('/admin', $currentUri) ?>" onclick="toggleMobileNav()">Admin</a>
                <?php endif; ?>
                <a href="/customer/addresses" class="<?= isActive('/customer/addresses', $currentUri) ?>" onclick="toggleMobileNav()">My Addresses</a>
                <a href="/logout" onclick="toggleMobileNav()">Logout</a>
            <?php else: ?>
                <a href="/login" class="<?= isActive('/login', $currentUri) ?>" onclick="toggleMobileNav()">Login</a>
                <a href="/register" class="<?= isActive('/register', $currentUri) ?>" onclick="toggleMobileNav()">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <script>
    function toggleMobileNav() {
        const menu = document.getElementById('mobileNavMenu');
        menu.classList.toggle('active');
    }
    </script>

    <?php $flash = flash('success'); ?>
    <?php if ($flash): ?>
        <div class="alert alert-success" id="flash-message"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <?php $flash = flash('error'); ?>
    <?php if ($flash): ?>
        <div class="alert alert-error" id="flash-message"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <script>
    const flashEl = document.getElementById('flash-message');
    if (flashEl) {
        setTimeout(() => {
            flashEl.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
            flashEl.style.opacity = '0';
            flashEl.style.transform = 'translateY(-15px)';
            setTimeout(() => flashEl.remove(), 500);
        }, 3500);
    }
    </script>

    <main class="container">

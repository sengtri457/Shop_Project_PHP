<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/" class="logo">Shop</a>
            <div class="nav-links">
                <a href="/">Home</a>
                <a href="/products">Products</a>
                <a href="/cart">Cart</a>
                <?php if (is_logged_in()): ?>
                    <span class="nav-user">
                        <?= htmlspecialchars($_SESSION['customer']['name'] ?? '') ?>
                    </span>
                    <?php if (is_admin()): ?>
                        <a href="/admin">Admin</a>
                    <?php endif; ?>
                    <a href="/logout">Logout</a>
                <?php else: ?>
                    <a href="/login">Login</a>
                    <a href="/register">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <?php $flash = flash('success'); ?>
    <?php if ($flash): ?>
        <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <?php $flash = flash('error'); ?>
    <?php if ($flash): ?>
        <div class="alert alert-error"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <main class="container">

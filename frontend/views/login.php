<div class="section auth-page">
    <h1>Login</h1>

    <?php if (has_errors()): ?>
        <div class="alert alert-error"><?= error() ?></div>
    <?php endif; ?>

    <form method="POST" class="auth-form">
        <label>
            Email
            <input type="email" name="email" value="<?= htmlspecialchars(old('email')) ?>" required>
        </label>
        <label>
            Password
            <input type="password" name="password" required>
        </label>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <p class="auth-link">Don't have an account? <a href="/register">Register</a></p>
</div>

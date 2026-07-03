<div class="section auth-page">
    <h1>Register</h1>

    <?php if (has_errors()): ?>
        <div class="alert alert-error"><?= error() ?></div>
    <?php endif; ?>

    <form method="POST" class="auth-form">
        <label>
            Name
            <input type="text" name="name" value="<?= htmlspecialchars(old('name')) ?>" required>
        </label>
        <label>
            Email
            <input type="email" name="email" value="<?= htmlspecialchars(old('email')) ?>" required>
        </label>
        <label>
            Password
            <input type="password" name="password" required minlength="6">
        </label>
        <button type="submit" class="btn btn-primary">Create Account</button>
    </form>

    <p class="auth-link">Already have an account? <a href="/login">Login</a></p>
</div>

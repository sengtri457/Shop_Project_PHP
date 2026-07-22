<?php
$pageTitle = 'Create Account';
$metaDescription = 'Join Clothes to enjoy exclusive offers, faster checkout, order tracking, and curated style recommendations.';
?>

<div class="py-8 md:py-16 flex items-center justify-center min-h-[calc(100vh-250px)] px-4">
    <div class="w-full max-w-sm">
        <div class="bg-white border border-brand-border rounded-lg shadow-sm p-8 md:p-10">
            <div class="text-center mb-8">
                <a href="/" class="inline-block">
                    <img src="/assets/images/logoDevs.png" alt="Logo" class="h-10 w-auto mx-auto">
                </a>
                <h1 class="font-serif text-2xl font-semibold text-brand-text mt-6">Create Account</h1>
                <p class="text-xs text-brand-muted mt-1.5">Join us and start shopping today.</p>
            </div>

            <?php if (has_errors()): ?>
                <div class="mb-6 p-4 rounded bg-brand-errorBg border border-brand-error/20 flex items-start gap-3 text-brand-error text-xs">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-sm flex-shrink-0"></i>
                    <div><?= error() ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="/register" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-2" for="name">
                        Full Name
                    </label>
                    <div class="relative">
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars(old('name')) ?>" required placeholder="Jane Doe" class="w-full bg-brand-bg border border-brand-border rounded px-4 py-3 text-xs text-brand-text placeholder-brand-muted/50 focus:outline-none focus:border-brand-text focus:bg-white transition-all">
                        <span class="absolute right-3.5 top-3.5 text-brand-muted/40 text-xs">
                            <i class="fa-regular fa-user"></i>
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-2" for="email">
                        Email Address
                    </label>
                    <div class="relative">
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars(old('email')) ?>" required placeholder="name@example.com" class="w-full bg-brand-bg border border-brand-border rounded px-4 py-3 text-xs text-brand-text placeholder-brand-muted/50 focus:outline-none focus:border-brand-text focus:bg-white transition-all">
                        <span class="absolute right-3.5 top-3.5 text-brand-muted/40 text-xs">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-2" for="password">
                        Password (Min. 6 characters)
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required minlength="6" placeholder="••••••••" class="w-full bg-brand-bg border border-brand-border rounded px-4 py-3 text-xs text-brand-text placeholder-brand-muted/50 focus:outline-none focus:border-brand-text focus:bg-white transition-all pr-10">
                        <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute right-3.5 top-3.5 text-brand-muted/60 hover:text-brand-text text-xs focus:outline-none">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 bg-brand-text hover:bg-brand-text/90 text-white text-xs font-bold uppercase tracking-widest rounded transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                        <span>Create Account</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </button>
                </div>
            </form>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-brand-border"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-white px-3 text-brand-muted">or</span>
                </div>
            </div>

            <?php $gUrl = google_auth_url(); ?>
            <?php if ($gUrl): ?>
            <a href="<?= $gUrl ?>" class="w-full py-3 border border-brand-border rounded text-xs font-semibold tracking-wider text-brand-text hover:bg-brand-darker transition-all flex items-center justify-center gap-3">
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span>Sign up with Google</span>
            </a>
            <?php endif; ?>

            <div class="mt-8 pt-6 border-t border-brand-border text-center">
                <p class="text-xs text-brand-muted">
                    Already have an account?
                    <a href="/login" class="font-bold text-brand-text hover:text-brand-accent transition-colors ml-1">
                        Sign in
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-regular fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fa-regular fa-eye';
    }
}
</script>


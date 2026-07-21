<?php
$pageTitle = 'Create Account';
$metaDescription = 'Join Clothes to enjoy exclusive offers, faster checkout, order tracking, and curated style recommendations.';
?>

<div class="py-8 md:py-16 flex items-center justify-center min-h-[calc(100vh-250px)]">
    <div class="w-full max-w-4xl bg-white border border-brand-border rounded-lg shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-2">
        
        <!-- Left Side: Editorial Image & Brand Showcase -->
        <div class="relative hidden md:flex flex-col justify-between p-10 bg-brand-text text-white overflow-hidden">
            <div class="absolute inset-0 opacity-40 bg-cover bg-center transition-transform duration-700 hover:scale-105" style="background-image: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=1200&auto=format&fit=crop');"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-brand-text via-brand-text/60 to-transparent"></div>

            <!-- Top Logo -->
            <div class="relative z-10">
                <a href="/" class="font-serif text-2xl font-bold tracking-tight text-white">
                    CLOTHES<span class="text-brand-accent">.</span>
                </a>
            </div>

            <!-- Middle Quote -->
            <div class="relative z-10 my-auto">
                <span class="text-[10px] font-bold uppercase tracking-widest text-brand-accent">Member Perks</span>
                <h2 class="font-serif text-3xl font-semibold mt-2 leading-snug text-white">
                    Join a community of modern style enthusiasts.
                </h2>
                <ul class="text-xs text-white/80 mt-4 space-y-2.5 font-light">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-brand-accent text-[10px]"></i>
                        <span>Complimentary shipping on orders over $100</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-brand-accent text-[10px]"></i>
                        <span>Early access to seasonal sales & new drops</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-brand-accent text-[10px]"></i>
                        <span>One-click checkout & order management</span>
                    </li>
                </ul>
            </div>

            <!-- Bottom Note -->
            <div class="relative z-10 text-[11px] text-white/50">
                &copy; <?= date('Y') ?> Clothes Inc. Premium E-Commerce
            </div>
        </div>

        <!-- Right Side: Clean Form Container -->
        <div class="p-8 md:p-12 flex flex-col justify-center bg-white">
            <div class="mb-8">
                <h1 class="font-serif text-3xl font-semibold text-brand-text tracking-tight">Create Account</h1>
                <p class="text-xs text-brand-muted mt-1.5">Sign up today for a luxury shopping experience.</p>
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
                    <button type="submit" class="w-full py-3.5 bg-brand-text hover:bg-brand-text/90 text-white text-xs font-bold uppercase tracking-widest rounded transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <span>Register Account</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-brand-border text-center">
                <p class="text-xs text-brand-muted">
                    Already have an account? 
                    <a href="/login" class="font-bold text-brand-text hover:text-brand-accent transition-colors ml-1">
                        Sign In
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


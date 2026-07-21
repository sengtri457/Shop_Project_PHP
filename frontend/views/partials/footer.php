    <?php 
    $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $currentUri = rtrim($currentUri, '/') ?: '/';
    $isAdminRoute = is_admin() && (strpos($currentUri, '/admin') === 0);
    if ($isAdminRoute): 
    ?>
            </div>
        </div>
    <?php endif; ?>
    </main>

    <footer class="<?= $isAdminRoute ? 'bg-brand-darker border-t border-brand-border py-4 text-brand-muted text-[12px]' : 'bg-brand-text text-white/90 pt-12 pb-8 border-t border-brand-border/10' ?>">
    <?php if ($isAdminRoute): ?>
        <div class="max-w-[1280px] mx-auto px-6 flex justify-between items-center text-xs">
            <p>&copy; <?= date('Y') ?> Clothes Admin Panel. All rights reserved.</p>
            <div class="flex gap-4 text-brand-muted">
                <span>System Status: <span class="text-brand-success font-medium">Online</span></span>
                <span>Version 2.4.0</span>
            </div>
        </div>
    <?php else: ?>
        <div class="max-w-[1280px] mx-auto px-6">
            <!-- Minimal Trust Bar (Text-driven, clean typography) -->
            <div class="flex flex-wrap items-center justify-between gap-4 pb-8 mb-8 border-b border-white/10 text-[12px] text-white/70">
                <span class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-accent"></span>
                    Complimentary Shipping over $100
                </span>
                <span class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-accent"></span>
                    30-Day Hassle-Free Returns
                </span>
                <span class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-accent"></span>
                    Encrypted Secure Checkout
                </span>
                <span class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-accent"></span>
                    Sustainably Sourced Fabrics
                </span>
            </div>

            <!-- Main Footer Links & Newsletter Grid -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 pb-10 border-b border-white/10">
                <!-- Brand & Story Column -->
                <div class="md:col-span-4 flex flex-col justify-between">
                    <div>
                        <a href="/" class="inline-block mb-3 hover:opacity-90 transition-opacity">
                            <img src="/assets/images/logoDevs.png" alt="Logo" class="h-9 w-auto object-contain brightness-0 invert opacity-90">
                        </a>
                        <p class="text-[12.5px] text-white/60 leading-relaxed max-w-sm font-light mb-6">
                            Curated apparel designed for modern living. Elevate your everyday wardrobe with timeless silhouettes and sustainable luxury.
                        </p>
                    </div>
                    
                    <!-- Clean Minimalist Social Links -->
                    <div class="flex items-center gap-3 text-[12px] text-white/60">
                        <a href="#" class="hover:text-white transition-colors">Instagram</a>
                        <span>·</span>
                        <a href="#" class="hover:text-white transition-colors">TikTok</a>
                        <span>·</span>
                        <a href="#" class="hover:text-white transition-colors">Pinterest</a>
                        <span>·</span>
                        <a href="#" class="hover:text-white transition-colors">Twitter/X</a>
                    </div>
                </div>

                <!-- Shop Column -->
                <div class="md:col-span-2">
                    <h4 class="text-[11px] font-bold uppercase tracking-widest text-white/50 mb-4">Shop</h4>
                    <ul class="space-y-2.5 text-[12.5px]">
                        <li><a href="/products" class="text-white/70 hover:text-white transition-colors">All Products</a></li>
                        <li><a href="/products?gender=men" class="text-white/70 hover:text-white transition-colors">Men's Apparel</a></li>
                        <li><a href="/products?gender=women" class="text-white/70 hover:text-white transition-colors">Women's Apparel</a></li>
                        <li><a href="/products?gender=kids" class="text-white/70 hover:text-white transition-colors">Kids & Youth</a></li>
                        <li><a href="/products?sort=newest" class="text-white/70 hover:text-white transition-colors">New Arrivals</a></li>
                    </ul>
                </div>

                <!-- Customer Care Column -->
                <div class="md:col-span-2">
                    <h4 class="text-[11px] font-bold uppercase tracking-widest text-white/50 mb-4">Customer Care</h4>
                    <ul class="space-y-2.5 text-[12.5px]">
                        <li><a href="/customer/orders" class="text-white/70 hover:text-white transition-colors">Track Order</a></li>
                        <li><a href="/cart" class="text-white/70 hover:text-white transition-colors">Shopping Bag</a></li>
                        <li><a href="/favorites" class="text-white/70 hover:text-white transition-colors">Saved Items</a></li>
                        <li><a href="#" class="text-white/70 hover:text-white transition-colors">Shipping & Returns</a></li>
                        <li><a href="#" class="text-white/70 hover:text-white transition-colors">Size Guide</a></li>
                    </ul>
                </div>

                <!-- Newsletter Signup Column -->
                <div class="md:col-span-4">
                    <h4 class="text-[11px] font-bold uppercase tracking-widest text-white/50 mb-4">Newsletter</h4>
                    <p class="text-[12.5px] text-white/60 leading-relaxed font-light mb-4">
                        Subscribe to receive private sale invites, new drop notifications, and 10% off your first order.
                    </p>
                    <form id="footer-newsletter-form" onsubmit="handleNewsletterSubmit(event)" class="flex flex-col gap-2">
                        <div class="relative flex items-center">
                            <input type="email" id="footer-email-input" required placeholder="Enter your email address" class="w-full bg-white/5 border border-white/15 rounded px-4 py-2.5 text-[12.5px] text-white placeholder-white/40 focus:outline-none focus:border-brand-accent transition-colors pr-24">
                            <button type="submit" id="footer-newsletter-btn" class="absolute right-1 px-3.5 py-1.5 bg-brand-accent hover:bg-brand-accentHover text-white text-[10.5px] font-bold uppercase tracking-wider rounded transition-colors">
                                Subscribe
                            </button>
                        </div>
                        <p id="footer-newsletter-msg" class="text-[11.5px] hidden font-medium mt-1"></p>
                    </form>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="pt-6 flex flex-col md:flex-row items-center justify-between gap-4 text-[11.5px] text-white/40">
                <p>&copy; <?= date('Y') ?> Clothes Inc. All rights reserved.</p>
                
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white/70 transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-white/70 transition-colors">Terms of Service</a>
                    <a href="#" class="hover:text-white/70 transition-colors">Accessibility</a>
                </div>
            </div>
        </div>

        <script>
        function handleNewsletterSubmit(e) {
            e.preventDefault();
            const input = document.getElementById('footer-email-input');
            const btn = document.getElementById('footer-newsletter-btn');
            const msg = document.getElementById('footer-newsletter-msg');
            
            if (!input.value) return;
            
            btn.disabled = true;
            btn.textContent = 'Joining...';
            
            setTimeout(() => {
                btn.disabled = false;
                btn.textContent = 'Subscribe';
                input.value = '';
                msg.textContent = 'Thank you for subscribing! Check your inbox soon.';
                msg.className = 'text-[11.5px] font-medium mt-1 text-emerald-400 block';
                
                setTimeout(() => {
                    msg.className = 'text-[11.5px] hidden font-medium mt-1';
                }, 5000);
            }, 600);
        }
        </script>
    <?php endif; ?>
    </footer>
    <script src="/assets/js/loader.js"></script>
</body>
</html>

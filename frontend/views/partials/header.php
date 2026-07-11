<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | Clothes' : 'Clothes - Clean & Modern E-Commerce' ?></title>
    <meta name="description" content="<?= isset($metaDescription) ? htmlspecialchars($metaDescription) : 'Discover a curated collection of premium clothing, variants, sizes, and colors.' ?>">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        if (!window.tailwind) {
            console.error('Tailwind Play CDN failed to load. Running diagnostics...');
            window.addEventListener('DOMContentLoaded', () => {
                const diag = document.createElement('div');
                diag.style.cssText = 'position:fixed;top:0;left:0;width:100%;background:#9e2a2b;color:#fff;text-align:center;padding:16px;z-index:99999;font-weight:600;font-family:sans-serif;box-shadow:0 4px 12px rgba(0,0,0,0.15);';
                diag.innerHTML = '⚠️ Tailwind CSS CDN failed to load. The page layouts will appear unstyled. Please check your internet connection or browser console (F12) for network/CSP blocks.';
                document.body.insertBefore(diag, document.body.firstChild);
            });
        }
    </script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            bg: '#FAFAF8',
                            darker: '#F4F4F0',
                            text: '#1A1A1A',
                            muted: '#6A6A65',
                            border: '#E6E6E2',
                            accent: '#A35C49',
                            accentHover: '#8C4E3D',
                            accentLight: '#F6EDEB',
                            success: '#2B5B3C',
                            successBg: '#EEF4F0',
                            error: '#9E2A2B',
                            errorBg: '#F9EBEB',
                        }
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'sans-serif'],
                    },
                    borderRadius: {
                        brand: '4px',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-brand-bg text-brand-text font-sans antialiased">
    <?php
    $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $currentUri = rtrim($currentUri, '/') ?: '/';
    
    function isActive(string $path, string $currentUri): string {
        if ($path === '/' && $currentUri === '/') return 'active';
        if ($path !== '/' && strpos($currentUri, $path) === 0) return 'active';
        return '';
    }
    ?>
    <nav class="sticky top-0 z-50 bg-brand-bg/95 backdrop-blur-md border-b border-brand-border py-6 transition-all duration-300">
        <div class="max-w-[1280px] mx-auto px-8 flex justify-between items-center static">
            <a href="/" class="font-serif text-[1.8rem] font-semibold tracking-tighter text-brand-text">ClothesByKTRI</a>
            
            <div class="hidden md:flex items-center gap-8">
                <a href="/" class="text-[12px] font-medium tracking-widest uppercase text-brand-muted hover:text-brand-text py-1.5 relative <?= isActive('/', $currentUri) === 'active' ? 'text-brand-text after:absolute after:bottom-0 after:left-0 after:w-full after:h-[1.5px] after:bg-brand-accent' : '' ?>">Home</a>
                <a href="/products" class="text-[12px] font-medium tracking-widest uppercase text-brand-muted hover:text-brand-text py-1.5 relative <?= isActive('/products', $currentUri) && empty($_GET['gender']) ? 'text-brand-text after:absolute after:bottom-0 after:left-0 after:w-full after:h-[1.5px] after:bg-brand-accent' : '' ?>">All Products</a>
                
                <!-- MEN MEGAMENU -->
                <div class="group static">
                    <a href="/products?gender=men" class="text-[12px] font-medium tracking-widest uppercase text-brand-muted hover:text-brand-text py-1.5 relative <?= isset($_GET['gender']) && $_GET['gender'] === 'men' ? 'text-brand-text after:absolute after:bottom-0 after:left-0 after:w-full after:h-[1.5px] after:bg-brand-accent' : '' ?>">Men</a>
                    <div class="absolute top-full left-0 w-full bg-brand-bg border-b border-brand-border py-10 shadow-[0_16px_32px_rgba(0,0,0,0.04)] z-[99] opacity-0 translate-y-3 invisible pointer-events-none transition-all duration-300 group-hover:opacity-100 group-hover:translate-y-0 group-hover:visible group-hover:pointer-events-auto before:content-[''] before:absolute before:bottom-full before:left-0 before:w-full before:h-8 before:bg-transparent bg-overlay mb-0">
                        <div class="max-w-[1280px] mx-auto px-8">
                            <div class="flex justify-center gap-24">
                                <div class="flex flex-col">
                                    <h4 class="font-sans text-[12px] font-semibold uppercase tracking-wider text-brand-text mb-4.5 border-b-2 border-brand-accent pb-1 w-fit">Highlights</h4>
                                    <ul class="flex flex-col gap-3">
                                        <li><a href="/products?gender=men&sort_by=created_at&sort_order=desc" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">New Arrivals</a></li>
                                        <li><a href="/products?gender=men" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">Best Sellers</a></li>
                                        <li><a href="/products?gender=men&min_price=50" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">Premium Essentials</a></li>
                                    </ul>
                                </div>
                                <div class="flex flex-col">
                                    <h4 class="font-sans text-[12px] font-semibold uppercase tracking-wider text-brand-text mb-4.5 border-b-2 border-brand-accent pb-1 w-fit">Shoes</h4>
                                    <ul class="flex flex-col gap-1">
                                        <li><a href="/products?gender=men&category_id=1" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">All Shoes</a></li>
                                        <li><a href="/products?gender=men&category_id=3" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">Basketball Shoes</a></li>
                                        <li><a href="/products?gender=men&category_id=1&search=running" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">Running</a></li>
                                        <li><a href="/products?gender=men&category_id=1&search=lifestyle" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">Lifestyle</a></li>
                                    </ul>
                                </div>
                                <div class="flex flex-col">
                                    <h4 class="font-sans text-[12px] font-semibold uppercase tracking-wider text-brand-text mb-4.5 border-b-2 border-brand-accent pb-1 w-fit">Clothing</h4>
                                    <ul class="flex flex-col gap-3">
                                        <li><a href="/products?gender=men" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">All Clothing</a></li>
                                        <li><a href="/products?gender=men&category_id=2" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">T-Shirts & Tops</a></li>
                                        <li><a href="/products?gender=men&search=hoodie" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">Hoodies</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WOMEN MEGAMENU -->
                <div class="group static">
                    <a href="/products?gender=women" class="text-[12px] font-medium tracking-widest uppercase text-brand-muted hover:text-brand-text py-1.5 relative <?= isset($_GET['gender']) && $_GET['gender'] === 'women' ? 'text-brand-text after:absolute after:bottom-0 after:left-0 after:w-full after:h-[1.5px] after:bg-brand-accent' : '' ?>">Women</a>
                    <div class="absolute top-full left-0 w-full bg-brand-bg border-b border-brand-border py-10 shadow-[0_16px_32px_rgba(0,0,0,0.04)] z-[99] opacity-0 translate-y-3 invisible pointer-events-none transition-all duration-300 group-hover:opacity-100 group-hover:translate-y-0 group-hover:visible group-hover:pointer-events-auto before:content-[''] before:absolute before:bottom-full before:left-0 before:w-full before:h-8 before:bg-transparent">
                        <div class="max-w-[1280px] mx-auto px-8">
                            <div class="flex justify-center gap-24">
                                <div class="flex flex-col">
                                    <h4 class="font-sans text-[12px] font-semibold uppercase tracking-wider text-brand-text mb-4.5 border-b-2 border-brand-accent pb-1 w-fit">Highlights</h4>
                                    <ul class="flex flex-col gap-3">
                                        <li><a href="/products?gender=women&sort_by=created_at&sort_order=desc" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">New Arrivals</a></li>
                                        <li><a href="/products?gender=women" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">Best Sellers</a></li>
                                        <li><a href="/products?gender=women&min_price=50" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">Premium Essentials</a></li>
                                    </ul>
                                </div>
                                <div class="flex flex-col">
                                    <h4 class="font-sans text-[12px] font-semibold uppercase tracking-wider text-brand-text mb-4.5 border-b-2 border-brand-accent pb-1 w-fit">Shoes</h4>
                                    <ul class="flex flex-col gap-3">
                                        <li><a href="/products?gender=women&category_id=1" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">All Shoes</a></li>
                                        <li><a href="/products?gender=women&category_id=3" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">Basketball Shoes</a></li>
                                        <li><a href="/products?gender=women&category_id=1&search=running" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">Running</a></li>
                                        <li><a href="/products?gender=women&category_id=1&search=lifestyle" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">Lifestyle</a></li>
                                    </ul>
                                </div>
                                <div class="flex flex-col">
                                    <h4 class="font-sans text-[12px] font-semibold uppercase tracking-wider text-brand-text mb-4.5 border-b-2 border-brand-accent pb-1 w-fit">Clothing</h4>
                                    <ul class="flex flex-col gap-3">
                                        <li><a href="/products?gender=women" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">All Clothing</a></li>
                                        <li><a href="/products?gender=women&category_id=2" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">T-Shirts & Tops</a></li>
                                        <li><a href="/products?gender=women&search=hoodie" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">Hoodies</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KIDS MEGAMENU -->
                <div class="group static">
                    <a href="/products?gender=kids" class="text-[12px] font-medium tracking-widest uppercase text-brand-muted hover:text-brand-text py-1.5 relative <?= isset($_GET['gender']) && $_GET['gender'] === 'kids' ? 'text-brand-text after:absolute after:bottom-0 after:left-0 after:w-full after:h-[1.5px] after:bg-brand-accent' : '' ?>">Kids</a>
                    <div class="absolute top-full left-0 w-full bg-brand-bg border-b border-brand-border py-10 shadow-[0_16px_32px_rgba(0,0,0,0.04)] z-[99] opacity-0 translate-y-3 invisible pointer-events-none transition-all duration-300 group-hover:opacity-100 group-hover:translate-y-0 group-hover:visible group-hover:pointer-events-auto before:content-[''] before:absolute before:bottom-full before:left-0 before:w-full before:h-8 before:bg-transparent">
                        <div class="max-w-[1280px] mx-auto px-8">
                            <div class="flex justify-center gap-24">
                                <div class="flex flex-col">
                                    <h4 class="font-sans text-[12px] font-semibold uppercase tracking-wider text-brand-text mb-4.5 border-b-2 border-brand-accent pb-1 w-fit">Highlights</h4>
                                    <ul class="flex flex-col gap-3">
                                        <li><a href="/products?gender=kids&sort_by=created_at&sort_order=desc" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">New Arrivals</a></li>
                                        <li><a href="/products?gender=kids" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">Best Sellers</a></li>
                                    </ul>
                                </div>
                                <div class="flex flex-col">
                                    <h4 class="font-sans text-[12px] font-semibold uppercase tracking-wider text-brand-text mb-4.5 border-b-2 border-brand-accent pb-1 w-fit">Shoes</h4>
                                    <ul class="flex flex-col gap-3">
                                        <li><a href="/products?gender=kids&category_id=1" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">All Shoes</a></li>
                                        <li><a href="/products?gender=kids&category_id=3" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">Basketball Shoes</a></li>
                                    </ul>
                                </div>
                                <div class="flex flex-col">
                                    <h4 class="font-sans text-[12px] font-semibold uppercase tracking-wider text-brand-text mb-4.5 border-b-2 border-brand-accent pb-1 w-fit">Clothing</h4>
                                    <ul class="flex flex-col gap-3">
                                        <li><a href="/products?gender=kids" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">All Clothing</a></li>
                                        <li><a href="/products?gender=kids&category_id=2" class="font-sans text-[13px] text-brand-muted hover:text-brand-text hover:pl-0.5 transition-all duration-300">T-Shirts & Tops</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- SEARCH PILL -->
                <form action="/products" method="GET" class="relative hidden lg:flex items-center">
                    <input type="text" name="search" placeholder="Search" 
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                           class="bg-brand-darker text-[12px] font-sans text-brand-text placeholder-brand-muted pl-10 pr-4 py-2 w-40 focus:w-56 rounded-full transition-all duration-300 focus:outline-none focus:ring-1 focus:ring-brand-accent focus:bg-white border border-transparent focus:border-brand-border">
                    <div class="absolute left-3.5 pointer-events-none">
                        <svg class="w-3.5 h-3.5 text-brand-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>

                <a href="/cart" class="text-[12px] font-medium tracking-widest uppercase text-brand-muted hover:text-brand-text py-1.5 relative <?= isActive('/cart', $currentUri) === 'active' ? 'text-brand-text after:absolute after:bottom-0 after:left-0 after:w-full after:h-[1.5px] after:bg-brand-accent' : '' ?>">Cart</a>
                
                <!-- ACCOUNT DROPDOWN -->
                <div class="group relative">
                    <button class="flex items-center gap-1 text-[12px] font-medium tracking-widest uppercase text-brand-muted hover:text-brand-text py-1.5 focus:outline-none">
                        <span>Account</span>
                        <svg class="w-3 h-3 text-brand-muted group-hover:text-brand-text transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <!-- Dropdown Panel -->
                    <div class="absolute right-0 top-full w-48 bg-brand-bg border border-brand-border py-2 mt-1 rounded shadow-[0_4px_12px_rgba(0,0,0,0.05)] z-[100] opacity-0 translate-y-2 invisible pointer-events-none transition-all duration-200 group-hover:opacity-100 group-hover:translate-y-0 group-hover:visible group-hover:pointer-events-auto before:content-[''] before:absolute before:bottom-full before:right-0 before:w-full before:h-4 before:bg-transparent">
                        <?php if (is_logged_in()): ?>
                            <div class="px-4 py-2 border-b border-brand-border mb-1">
                                <p class="text-[10px] text-brand-muted tracking-wider uppercase">Welcome,</p>
                                <p class="text-[13px] font-semibold text-brand-text truncate"><?= htmlspecialchars($_SESSION['customer']['name'] ?? '') ?></p>
                            </div>
                            <a href="/customer/addresses" class="block px-4 py-1.5 text-[12px] tracking-wide text-brand-muted hover:text-brand-text hover:bg-brand-darker transition-colors">My Addresses</a>
                            <?php if (is_admin()): ?>
                                <a href="/admin" class="block px-4 py-1.5 text-[12px] tracking-wide text-brand-muted hover:text-brand-text hover:bg-brand-darker transition-colors font-medium text-brand-accent">Admin Dashboard</a>
                            <?php endif; ?>
                            <div class="border-t border-brand-border mt-1 pt-1">
                                <a href="/logout" class="block px-4 py-1.5 text-[12px] tracking-wide text-brand-error hover:bg-brand-darker transition-colors">Logout</a>
                            </div>
                        <?php else: ?>
                            <a href="/login" class="block px-4 py-1.5 text-[12px] tracking-wide text-brand-muted hover:text-brand-text hover:bg-brand-darker transition-colors">Login</a>
                            <a href="/register" class="block px-4 py-1.5 text-[12px] tracking-wide text-brand-muted hover:text-brand-text hover:bg-brand-darker transition-colors">Register</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <button class="md:hidden flex flex-col gap-1.5 p-1 bg-transparent border-none cursor-pointer" onclick="toggleMobileNav()" aria-label="Toggle navigation">
                <span class="block w-6 h-[1.5px] bg-brand-text transition-all duration-300"></span>
                <span class="block w-6 h-[1.5px] bg-brand-text transition-all duration-300"></span>
                <span class="block w-6 h-[1.5px] bg-brand-text transition-all duration-300"></span>
            </button>
        </div>

        <div class="hidden flex-col absolute top-[79px] left-0 w-full bg-brand-bg border-b border-brand-border p-6 gap-4 z-[99] shadow-md [&.active]:flex" id="mobileNavMenu">
            <!-- MOBILE SEARCH -->
            <form action="/products" method="GET" class="relative flex items-center mb-1">
                <input type="text" name="search" placeholder="Search products..." 
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                       class="w-full bg-brand-darker text-[14px] font-sans text-brand-text placeholder-brand-muted pl-10 pr-4 py-2.5 rounded-brand focus:outline-none focus:ring-1 focus:ring-brand-accent focus:bg-white border border-brand-border">
                <div class="absolute left-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </form>
            <a href="/" class="text-[14px] font-medium tracking-widest uppercase text-brand-text <?= isActive('/', $currentUri) === 'active' ? 'text-brand-accent' : '' ?>" onclick="toggleMobileNav()">Home</a>
            <a href="/products" class="text-[14px] font-medium tracking-widest uppercase text-brand-text <?= isActive('/products', $currentUri) && empty($_GET['gender']) ? 'text-brand-accent' : '' ?>" onclick="toggleMobileNav()">All Products</a>
            <a href="/products?gender=men" class="text-[14px] font-medium tracking-widest uppercase text-brand-text <?= isset($_GET['gender']) && $_GET['gender'] === 'men' ? 'text-brand-accent' : '' ?>" onclick="toggleMobileNav()">Men</a>
            <a href="/products?gender=women" class="text-[14px] font-medium tracking-widest uppercase text-brand-text <?= isset($_GET['gender']) && $_GET['gender'] === 'women' ? 'text-brand-accent' : '' ?>" onclick="toggleMobileNav()">Women</a>
            <a href="/products?gender=kids" class="text-[14px] font-medium tracking-widest uppercase text-brand-text <?= isset($_GET['gender']) && $_GET['gender'] === 'kids' ? 'text-brand-accent' : '' ?>" onclick="toggleMobileNav()">Kids</a>
            <a href="/cart" class="text-[14px] font-medium tracking-widest uppercase text-brand-text <?= isActive('/cart', $currentUri) === 'active' ? 'text-brand-accent' : '' ?>" onclick="toggleMobileNav()">Cart</a>
            <?php if (is_logged_in()): ?>
                <span class="text-[13px] font-semibold tracking-widest uppercase text-brand-text mb-2 inline-block">
                    <?= htmlspecialchars($_SESSION['customer']['name'] ?? '') ?>
                </span>
                <?php if (is_admin()): ?>
                    <a href="/admin" class="text-[14px] font-medium tracking-widest uppercase text-brand-text <?= isActive('/admin', $currentUri) === 'active' ? 'text-brand-accent' : '' ?>" onclick="toggleMobileNav()">Admin</a>
                <?php endif; ?>
                <a href="/customer/addresses" class="text-[14px] font-medium tracking-widest uppercase text-brand-text <?= isActive('/customer/addresses', $currentUri) === 'active' ? 'text-brand-accent' : '' ?>" onclick="toggleMobileNav()">My Addresses</a>
                <a href="/logout" class="text-[14px] font-medium tracking-widest uppercase text-brand-text" onclick="toggleMobileNav()">Logout</a>
            <?php else: ?>
                <a href="/login" class="text-[14px] font-medium tracking-widest uppercase text-brand-text <?= isActive('/login', $currentUri) === 'active' ? 'text-brand-accent' : '' ?>" onclick="toggleMobileNav()">Login</a>
                <a href="/register" class="text-[14px] font-medium tracking-widest uppercase text-brand-text <?= isActive('/register', $currentUri) === 'active' ? 'text-brand-accent' : '' ?>" onclick="toggleMobileNav()">Register</a>
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
        <div class="fixed top-6 right-6 p-4 px-6 rounded bg-brand-text text-brand-bg text-[13px] font-semibold shadow-md z-[9999] flex items-center gap-3 before:content-['✓'] before:inline-flex before:items-center before:justify-center before:bg-brand-accent before:text-white before:w-5 before:h-5 before:rounded-full before:text-[11px]" id="flash-message"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <?php $flash = flash('error'); ?>
    <?php if ($flash): ?>
        <div class="fixed top-6 right-6 p-4 px-6 rounded bg-brand-error text-brand-bg text-[13px] font-semibold shadow-md z-[9999] flex items-center gap-3 before:content-['✕'] before:inline-flex before:items-center before:justify-center before:bg-[#ff7675] before:text-white before:w-5 before:h-5 before:rounded-full before:text-[11px]" id="flash-message"><?= htmlspecialchars($flash) ?></div>
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

    <main class="max-w-[1280px] mx-auto px-8 pt-12 pb-24 min-h-[calc(100vh-200px)]">

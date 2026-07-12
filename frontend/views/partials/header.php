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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        if (!window.tailwind) {
            console.error('Tailwind Play CDN failed to load. Running diagnostics...');
            window.addEventListener('DOMContentLoaded', () => {
                const diag = document.createElement('div');
                diag.style.cssText = 'position:fixed;top:0;left:0;width:100%;background:#9e2a2b;color:#fff;text-align:center;padding:16px;z-index:99999;font-weight:600;font-family:sans-serif;box-shadow:0 4px 12px rgba(0,0,0,0.15);';
                diag.innerHTML = 'Tailwind CSS CDN failed to load. The page layouts will appear unstyled. Please check your internet connection or browser console (F12) for network/CSP blocks.';
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
    <!-- Top Progress Loading Bar -->
    <div id="topLoadingBar" class="fixed top-0 left-0 h-[3.5px] bg-brand-accent z-[10000] w-0 transition-all duration-200 pointer-events-none opacity-0"></div>
    <?php
    $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $currentUri = rtrim($currentUri, '/') ?: '/';
    
    function isActive(string $path, string $currentUri): string {
        if ($path === '/' && $currentUri === '/') return 'active';
        if ($path !== '/' && strpos($currentUri, $path) === 0) return 'active';
        return '';
    }
    ?>
    <?php 
    $isAdminRoute = is_admin() && (strpos($currentUri, '/admin') === 0);
    if (!$isAdminRoute): 
    ?>
    <nav class="sticky top-0 z-50 bg-brand-bg/95 backdrop-blur-md border-b border-brand-border py-6 transition-all duration-300">
        <div class="max-w-[1280px] mx-auto px-8 flex justify-around items-center static">
            <a href="/" class="flex items-center">
                <img src="/assets/images/logoDevs.png" alt="Logo" class="h-10 w-auto">
            </a>
            
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
               
                </div>
                        <div class="flex items-center gap-6">
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

                <a href="/favorites" class="text-[12px] font-medium tracking-widest uppercase text-brand-muted hover:text-brand-text py-1.5 relative <?= strpos($currentUri, '/favorites') === 0 ? 'text-brand-text after:absolute after:bottom-0 after:left-0 after:w-full after:h-[1.5px] after:bg-brand-accent' : '' ?> flex items-center gap-1">
                    <span>Favorites</span>
                    <span id="fav-badge-count" class="hidden text-[9px] font-bold bg-brand-accent text-white px-1.5 py-0.5 rounded-full leading-none">0</span>
                </a>

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
    <?php endif; ?>

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

    <main class="max-w-[1280px] mx-auto px-8 pt-8 pb-24 min-h-[calc(100vh-200px)]">
    <?php if ($isAdminRoute): ?>
        <!-- Mobile Admin Top Bar Header -->
        <div class="lg:hidden w-full bg-brand-darker border-b border-brand-border py-4 px-6 flex justify-between items-center sticky top-0 z-[100]">
            <a href="/" class="flex items-center">
                <img src="/assets/images/Logo.png" alt="Logo" class="h-8 w-auto object-contain">
            </a>
            <button onclick="toggleAdminSidebar()" class="p-2 text-brand-text hover:bg-brand-border/40 rounded transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>

        <!-- Sidebar Overlay Backdrop for Mobile -->
        <div id="adminSidebarOverlay" onclick="toggleAdminSidebar()" class="fixed inset-0 bg-black/45 z-[98] hidden transition-opacity duration-300 opacity-0"></div>

        <!-- Sidebar Container -->
        <div id="adminSidebar" class="w-[260px] fixed top-0 left-0 h-screen bg-brand-darker border-r border-brand-border p-6 z-[99] overflow-y-auto -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <!-- Admin Info -->
            <div class="mb-6 p-4 bg-brand-bg border border-brand-border rounded-brand text-center">
                <span class="text-[10px] font-bold tracking-wider uppercase text-brand-accent block mb-1">Administrator</span>
                <div class="font-serif text-[15px] font-semibold text-brand-text truncate"><?= htmlspecialchars($_SESSION['customer']['name'] ?? 'Admin') ?></div>
                <div class="text-[11px] text-brand-muted truncate mt-0.5"><?= htmlspecialchars($_SESSION['customer']['email'] ?? '') ?></div>
            </div>
            
            <h3 class="font-serif text-[1.25rem] font-semibold text-brand-text mb-4 pb-2 border-b border-brand-border">Admin Console</h3>
            <nav class="flex flex-col gap-1.5">
                <a href="/admin" class="flex items-center gap-3 px-4 py-3.5 rounded-brand text-[13px] font-semibold transition-all duration-300 <?= $currentUri === '/admin' ? 'bg-brand-text text-brand-bg' : 'text-brand-muted hover:text-brand-text hover:bg-brand-border/40' ?>" onclick="closeAdminSidebarOnMobile()">
                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Dashboard
                </a>
                <a href="/admin/products" class="flex items-center gap-3 px-4 py-3.5 rounded-brand text-[13px] font-semibold transition-all duration-300 <?= strpos($currentUri, '/admin/products') === 0 ? 'bg-brand-text text-brand-bg' : 'text-brand-muted hover:text-brand-text hover:bg-brand-border/40' ?>" onclick="closeAdminSidebarOnMobile()">
                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Products
                </a>
                <a href="/admin/categories" class="flex items-center gap-3 px-4 py-3.5 rounded-brand text-[13px] font-semibold transition-all duration-300 <?= strpos($currentUri, '/admin/categories') === 0 ? 'bg-brand-text text-brand-bg' : 'text-brand-muted hover:text-brand-text hover:bg-brand-border/40' ?>" onclick="closeAdminSidebarOnMobile()">
                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                    Categories
                </a>
                <a href="/admin/orders" class="flex items-center gap-3 px-4 py-3.5 rounded-brand text-[13px] font-semibold transition-all duration-300 <?= strpos($currentUri, '/admin/orders') === 0 ? 'bg-brand-text text-brand-bg' : 'text-brand-muted hover:text-brand-text hover:bg-brand-border/40' ?>" onclick="closeAdminSidebarOnMobile()">
                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Orders
                </a>
                <a href="/admin/discounts" class="flex items-center gap-3 px-4 py-3.5 rounded-brand text-[13px] font-semibold transition-all duration-300 <?= strpos($currentUri, '/admin/discounts') === 0 ? 'bg-brand-text text-brand-bg' : 'text-brand-muted hover:text-brand-text hover:bg-brand-border/40' ?>" onclick="closeAdminSidebarOnMobile()">
                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M6 20h.01M10 21l-7-7 1.414-1.414 7 7L10 21zm2-10l-7-7 1.414-1.414 7 7L12 11zm8-3a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Discounts
                </a>
                <a href="/admin/suppliers" class="flex items-center gap-3 px-4 py-3.5 rounded-brand text-[13px] font-semibold transition-all duration-300 <?= strpos($currentUri, '/admin/suppliers') === 0 ? 'bg-brand-text text-brand-bg' : 'text-brand-muted hover:text-brand-text hover:bg-brand-border/40' ?>" onclick="closeAdminSidebarOnMobile()">
                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Suppliers
                </a>
                <a href="/admin/purchase-orders" class="flex items-center gap-3 px-4 py-3.5 rounded-brand text-[13px] font-semibold transition-all duration-300 <?= strpos($currentUri, '/admin/purchase-orders') === 0 ? 'bg-brand-text text-brand-bg' : 'text-brand-muted hover:text-brand-text hover:bg-brand-border/40' ?>" onclick="closeAdminSidebarOnMobile()">
                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    Purchase Orders
                </a>
                
                <hr class="border-brand-border my-3">

                <a href="/" class="flex items-center gap-3 px-4 py-3.5 rounded-brand text-[13px] font-semibold text-brand-muted hover:text-brand-text hover:bg-brand-border/40 transition-all duration-300">
                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Storefront
                </a>
                <a href="/logout" class="flex items-center gap-3 px-4 py-3.5 rounded-brand text-[13px] font-semibold text-brand-error hover:bg-brand-errorBg transition-all duration-300">
                    <svg class="w-4 h-4 stroke-current" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
                </a>
            </nav>
        </div>

        <script>
        function toggleAdminSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('adminSidebarOverlay');
            if (sidebar && overlay) {
                const isClosed = sidebar.classList.contains('-translate-x-full');
                if (isClosed) {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                    setTimeout(() => overlay.classList.add('opacity-100'), 10);
                } else {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.remove('opacity-100');
                    setTimeout(() => overlay.classList.add('hidden'), 300);
                }
            }
        }
        function closeAdminSidebarOnMobile() {
            if (window.innerWidth < 1024) {
                toggleAdminSidebar();
            }
        }
        </script>

        <main class="w-full lg:pl-[260px] min-h-screen bg-brand-bg">
            <div class="p-6 md:p-10 max-w-[1400px] mx-auto">
    <?php else: ?>
        <main class="max-w-[1280px] mx-auto px-8 pb-24 min-h-[calc(100vh-200px)]">
    <?php endif; ?>

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
    <style>
        .fav-btn.favorited svg {
            color: #9E2A2B !important;
        }
        .fav-btn.favorited svg path {
            fill: currentColor !important;
            stroke: currentColor !important;
        }
    </style>
    <script>
        const API_BASE = '<?= API_BASE ?>';
    </script>
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

    $cartCount = 0;
    if (function_exists('cart_session_id')) {
        $cartRes = api_get('/cart?session_id=' . cart_session_id());
        if (!empty($cartRes['data']['items'])) {
            foreach ($cartRes['data']['items'] as $item) {
                $cartCount += (int)$item['quantity'];
            }
        }
    }
    ?>
    <?php 
    $isAdminRoute = is_admin() && (strpos($currentUri, '/admin') === 0);
    if (!$isAdminRoute): 
    ?>
    <nav class="sticky top-0 z-50 bg-brand-bg/95 backdrop-blur-md border-b border-brand-border py-3.5 transition-all duration-300">
        <div class="max-w-[1280px] mx-auto px-6 flex justify-around items-center static">
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
                    <input type="text" name="search" placeholder="Search" autocomplete="off"
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                           oninput="handleSearchSuggestions(event)" onblur="hideSearchSuggestions()"
                           class="bg-brand-darker text-[12px] font-sans text-brand-text placeholder-brand-muted pl-10 pr-4 py-2 w-40 focus:w-56 rounded-full transition-all duration-300 focus:outline-none focus:ring-1 focus:ring-brand-accent focus:bg-white border border-transparent focus:border-brand-border">
                    <div class="absolute left-3.5 pointer-events-none">
                        <svg class="w-3.5 h-3.5 text-brand-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <div id="search-suggestions" class="absolute top-full left-0 right-0 mt-2 bg-brand-bg border border-brand-border rounded shadow-[0_8px_24px_rgba(0,0,0,0.08)] z-[200] hidden flex-col max-h-[350px] overflow-y-auto"></div>
                </form>

                <a href="/favorites" class="text-[12px] font-medium tracking-widest uppercase text-brand-muted hover:text-brand-text py-1.5 relative <?= strpos($currentUri, '/favorites') === 0 ? 'text-brand-text after:absolute after:bottom-0 after:left-0 after:w-full after:h-[1.5px] after:bg-brand-accent' : '' ?> flex items-center gap-1">
                    <span>Favorites</span>
                    <span id="fav-badge-count" class="hidden text-[9px] font-bold bg-brand-accent text-white px-1.5 py-0.5 rounded-full leading-none">0</span>
                </a>

                <a href="/cart" onclick="openMiniCart(event)" class="text-[12px] font-medium tracking-widest uppercase text-brand-muted hover:text-brand-text py-1.5 relative <?= isActive('/cart', $currentUri) === 'active' ? 'text-brand-text after:absolute after:bottom-0 after:left-0 after:w-full after:h-[1.5px] after:bg-brand-accent' : '' ?> flex items-center gap-1">
                    <span>Cart</span>
                    <span id="cart-badge-count" class="<?= $cartCount > 0 ? '' : 'hidden' ?> text-[9px] font-bold bg-brand-accent text-white px-1.5 py-0.5 rounded-full leading-none"><?= $cartCount ?></span>
                </a>
                
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
                            <a href="/customer/orders" class="block px-4 py-1.5 text-[12px] tracking-wide text-brand-muted hover:text-brand-text hover:bg-brand-darker transition-colors">My Orders</a>
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
            <a href="/cart" onclick="openMiniCart(event); toggleMobileNav();" class="text-[14px] font-medium tracking-widest uppercase text-brand-text <?= isActive('/cart', $currentUri) === 'active' ? 'text-brand-accent' : '' ?> flex items-center gap-1.5">
                <span>Cart</span>
                <span id="mobile-cart-badge-count" class="<?= $cartCount > 0 ? '' : 'hidden' ?> text-[10px] font-bold bg-brand-accent text-white px-2 py-0.5 rounded-full leading-none"><?= $cartCount ?></span>
            </a>
            <?php if (is_logged_in()): ?>
                <span class="text-[13px] font-semibold tracking-widest uppercase text-brand-text mb-2 inline-block">
                    <?= htmlspecialchars($_SESSION['customer']['name'] ?? '') ?>
                </span>
                <?php if (is_admin()): ?>
                    <a href="/admin" class="text-[14px] font-medium tracking-widest uppercase text-brand-text <?= isActive('/admin', $currentUri) === 'active' ? 'text-brand-accent' : '' ?>" onclick="toggleMobileNav()">Admin</a>
                <?php endif; ?>
                <a href="/customer/orders" class="text-[14px] font-medium tracking-widest uppercase text-brand-text <?= isActive('/customer/orders', $currentUri) === 'active' ? 'text-brand-accent' : '' ?>" onclick="toggleMobileNav()">My Orders</a>
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

    <main class="w-full max-w-[1280px] mx-auto min-h-[calc(100vh-200px)]">
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
        <!-- Slide-Out Mini-Cart Drawer -->
        <div id="mini-cart-backdrop" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[9999] opacity-0 pointer-events-none transition-opacity duration-300" onclick="closeMiniCart()"></div>
        <div id="mini-cart-drawer" class="fixed right-0 top-0 bottom-0 w-full max-w-[420px] bg-brand-bg border-l border-brand-border z-[10000] translate-x-full transition-transform duration-300 flex flex-col shadow-[0_0_30px_rgba(0,0,0,0.1)]">
            <!-- Drawer Header -->
            <div class="p-6 border-b border-brand-border flex justify-between items-center bg-brand-darker">
                <h3 class="font-serif text-[1.4rem] font-medium text-brand-text">Shopping Bag</h3>
                <button onclick="closeMiniCart()" class="text-brand-muted hover:text-brand-text text-xl focus:outline-none">&times;</button>
            </div>
            
            <!-- Drawer Content -->
            <div id="mini-cart-items-list" class="flex-1 overflow-y-auto p-6 flex flex-col gap-6">
                <!-- Items injected here -->
            </div>

            <!-- Drawer Footer -->
            <div class="p-6 border-t border-brand-border bg-brand-darker flex flex-col gap-4">
                <div class="flex justify-between items-center font-semibold text-[14px]">
                    <span class="text-brand-muted uppercase tracking-wider text-[11px]">Subtotal</span>
                    <span class="text-brand-text text-base" id="mini-cart-subtotal">$0.00</span>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-2">
                    <a href="/cart" class="py-3 bg-transparent border border-brand-border text-brand-text hover:border-brand-text rounded text-center text-[11px] font-bold uppercase tracking-widest transition-colors">View Bag</a>
                    <a href="/checkout" class="py-3 bg-brand-text text-brand-bg hover:bg-brand-text/90 rounded text-center text-[11px] font-bold uppercase tracking-widest transition-colors">Checkout</a>
                </div>
            </div>
        </div>

        <!-- Slide-Out Quick View Modal -->
        <div id="quick-view-backdrop" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] opacity-0 pointer-events-none transition-opacity duration-300 flex items-center justify-center p-4" onclick="closeQuickView(event)">
            <div id="quick-view-container" class="bg-brand-bg rounded border border-brand-border w-full max-w-[850px] shadow-[0_20px_50px_rgba(0,0,0,0.15)] overflow-hidden scale-95 opacity-0 transition-all duration-300 relative flex flex-col md:flex-row max-h-[90vh] md:max-h-none overflow-y-auto md:overflow-visible" onclick="event.stopPropagation()">
                <!-- Close Button -->
                <button onclick="closeQuickView(null)" class="absolute right-4 top-4 text-brand-muted hover:text-brand-text text-2xl z-10 focus:outline-none">&times;</button>
                
                <!-- Left: Gallery -->
                <div class="w-full md:w-1/2 bg-brand-darker border-r border-brand-border flex items-center justify-center p-6 min-h-[300px] md:min-h-[450px]">
                    <img id="qv-main-image" src="" alt="Product Preview" class="max-w-full max-h-[400px] object-contain transition-opacity duration-150">
                </div>
                
                <!-- Right: Details -->
                <div class="w-full md:w-1/2 p-8 flex flex-col justify-between">
                    <div>
                        <span id="qv-brand" class="text-[10px] font-bold tracking-widest uppercase text-brand-muted">Brand</span>
                        <h2 id="qv-name" class="font-serif text-[1.8rem] text-brand-text font-semibold mt-1 mb-2 leading-tight">Product Name</h2>
                        
                        <div id="qv-price-display" class="text-xl font-sans mb-5">
                            <!-- Prices go here -->
                        </div>
                        
                        <p id="qv-description" class="text-[12.5px] text-brand-muted leading-relaxed mb-6 max-h-[120px] overflow-y-auto pr-2">Description...</p>
                        
                        <form id="qv-variant-form" onsubmit="submitQvCart(event)" class="flex flex-col gap-5">
                            <input type="hidden" id="qv-selected-variant-id" required>
                            
                            <!-- Colors -->
                            <div id="qv-colors-container" class="hidden">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-2">Color</label>
                                <div id="qv-colors-list" class="flex gap-2 flex-wrap"></div>
                            </div>
                            
                            <!-- Sizes -->
                            <div id="qv-sizes-container" class="hidden">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-2">Size</label>
                                <div id="qv-sizes-list" class="flex gap-2 flex-wrap"></div>
                            </div>
                            
                            <!-- Stock display -->
                            <div id="qv-stock-display" class="text-[11.5px] text-brand-muted min-h-[16px]">Select size & color</div>
                            
                            <!-- Action row -->
                            <div class="flex gap-3 items-center mt-2">
                                <div class="flex items-center border border-brand-border rounded overflow-hidden bg-white">
                                    <button type="button" onclick="adjustQvQty(-1)" class="px-3 py-2 text-brand-muted hover:text-brand-text focus:outline-none">&minus;</button>
                                    <input type="number" id="qv-quantity-input" value="1" min="1" max="99" class="w-10 text-center text-xs font-semibold border-none outline-none pointer-events-none">
                                    <button type="button" onclick="adjustQvQty(1)" class="px-3 py-2 text-brand-muted hover:text-brand-text focus:outline-none">&plus;</button>
                                </div>
                                <button type="submit" id="qv-add-btn" class="flex-1 py-3 bg-brand-text text-brand-bg hover:bg-brand-text/90 rounded text-center text-[11px] font-bold uppercase tracking-widest transition-colors focus:outline-none disabled:bg-brand-border disabled:text-brand-muted" disabled>Add to Bag</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
        // Global cart drawer controllers
        function openMiniCart(event) {
            if (event) event.preventDefault();
            document.getElementById('mini-cart-backdrop').classList.remove('opacity-0', 'pointer-events-none');
            document.getElementById('mini-cart-drawer').classList.remove('translate-x-full');
            loadMiniCartItems();
        }

        function closeMiniCart() {
            document.getElementById('mini-cart-backdrop').classList.add('opacity-0', 'pointer-events-none');
            document.getElementById('mini-cart-drawer').classList.add('translate-x-full');
        }

        function loadMiniCartItems() {
            const listEl = document.getElementById('mini-cart-items-list');
            const subtotalEl = document.getElementById('mini-cart-subtotal');
            const badgeEl = document.getElementById('cart-badge-count');
            const mobileBadgeEl = document.getElementById('mobile-cart-badge-count');
            
            listEl.innerHTML = `
                <div class="flex justify-center items-center h-48">
                    <span class="animate-spin text-brand-accent text-2xl">↻</span>
                </div>
            `;
            
            fetch('http://localhost:8000/cart?session_id=' + getCartSessionId(), {
                headers: getAuthHeaders()
            })
            .then(res => res.json())
            .then(result => {
                const items = result.items || [];
                
                let totalQty = 0;
                items.forEach(i => totalQty += parseInt(i.quantity));
                
                if (totalQty > 0) {
                    if (badgeEl) {
                        badgeEl.textContent = totalQty;
                        badgeEl.classList.remove('hidden');
                    }
                    if (mobileBadgeEl) {
                        mobileBadgeEl.textContent = totalQty;
                        mobileBadgeEl.classList.remove('hidden');
                    }
                } else {
                    if (badgeEl) badgeEl.classList.add('hidden');
                    if (mobileBadgeEl) mobileBadgeEl.classList.add('hidden');
                }
                
                if (items.length === 0) {
                    listEl.innerHTML = `
                        <div class="text-center py-16 flex flex-col items-center justify-center">
                            <span class="text-3xl mb-3 text-brand-muted">🛍️</span>
                            <p class="text-[13px] text-brand-muted font-medium">Your bag is empty.</p>
                        </div>
                    `;
                    subtotalEl.textContent = '$0.00';
                    return;
                }
                
                let html = '';
                let subtotal = 0;
                
                items.forEach(item => {
                    const price = parseFloat(item.price);
                    const qty = parseInt(item.quantity);
                    const total = price * qty;
                    subtotal += total;
                    
                    let images = [];
                    if (item.image_url) {
                        images = [item.image_url];
                    } else {
                        images = splitImageUrls(item.images);
                    }
                    const imgUrl = images.length > 0 ? getAssetUrl(images[0]) : '/assets/images/hero_banner.png';
                    
                    let attrStr = '';
                    if (item.attributes) {
                        try {
                            const attrs = JSON.parse(item.attributes);
                            attrStr = Object.values(attrs).join(' / ');
                        } catch(e) {}
                    }
                    
                    html += `
                        <div class="flex gap-4 items-center border-b border-brand-border pb-4 last:border-0 last:pb-0">
                            <img src="${imgUrl}" alt="${escapeHtml(item.name)}" class="w-16 h-20 object-cover bg-brand-darker border border-brand-border rounded">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-[13px] font-semibold text-brand-text truncate">${escapeHtml(item.name)}</h4>
                                <p class="text-[10px] text-brand-muted mt-0.5">${escapeHtml(attrStr)}</p>
                                <div class="flex items-center gap-3 mt-2">
                                    <div class="flex items-center border border-brand-border rounded">
                                        <button onclick="updateCartItemQty(${item.cart_item_id}, ${qty - 1})" class="px-2 py-0.5 text-brand-muted hover:text-brand-text text-xs focus:outline-none">-</button>
                                        <span class="px-2 text-xs font-semibold">${qty}</span>
                                        <button onclick="updateCartItemQty(${item.cart_item_id}, ${qty + 1})" class="px-2 py-0.5 text-brand-muted hover:text-brand-text text-xs focus:outline-none">+</button>
                                    </div>
                                    <button onclick="removeCartItem(${item.cart_item_id})" class="text-[10px] text-brand-error hover:underline uppercase tracking-wider font-bold">Remove</button>
                                </div>
                            </div>
                            <div class="text-[13px] font-semibold text-brand-text">
                                $${total.toFixed(2)}
                            </div>
                        </div>
                    `;
                });
                
                listEl.innerHTML = html;
                subtotalEl.textContent = '$' + subtotal.toFixed(2);
            })
            .catch(err => {
                console.error(err);
                listEl.innerHTML = `
                    <div class="text-center py-16 text-brand-error">
                        Failed to load shopping bag.
                    </div>
                `;
            });
        }

        function updateCartItemQty(itemId, newQty) {
            if (newQty < 1) {
                removeCartItem(itemId);
                return;
            }
            
            fetch('http://localhost:8000/cart/items/' + itemId, {
                method: 'PUT',
                headers: getAuthHeaders(),
                body: JSON.stringify({
                    quantity: newQty
                })
            })
            .then(() => loadMiniCartItems());
        }

        function removeCartItem(itemId) {
            fetch('http://localhost:8000/cart/items/' + itemId, {
                method: 'DELETE',
                headers: getAuthHeaders()
            })
            .then(() => loadMiniCartItems());
        }

        const USER_JWT_TOKEN = '<?= $_SESSION['token'] ?? '' ?>';

        function getAuthHeaders() {
            const headers = { 'Content-Type': 'application/json' };
            if (USER_JWT_TOKEN) {
                headers['Authorization'] = 'Bearer ' + USER_JWT_TOKEN;
            }
            return headers;
        }

        function getCartSessionId() {
            return '<?= cart_session_id() ?>';
        }

        function getAssetUrl(url) {
            if (!url) return '/assets/images/hero_banner.png';
            url = url.trim();
            if (url.startsWith('http://') || url.startsWith('https://')) {
                return url;
            }
            if (url.startsWith('frontend/')) {
                url = url.substring(9);
            }
            if (url.startsWith('/')) {
                return url;
            }
            return '/' + url;
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text
                .toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function splitImageUrls(imagesStr) {
            if (!imagesStr) return [];
            imagesStr = imagesStr.trim();
            return imagesStr.split(/,(?=\s*(?:https?:\/\/|\/))/i).map(s => s.trim()).filter(Boolean);
        }

        // Live Search Suggestions controllers
        let searchTimeout = null;

        function handleSearchSuggestions(event) {
            const input = event.target;
            const query = input.value.trim();
            const suggestionsPanel = document.getElementById('search-suggestions');
            
            if (searchTimeout) clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                suggestionsPanel.innerHTML = '';
                suggestionsPanel.classList.add('hidden');
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch('http://localhost:8000/products?search=' + encodeURIComponent(query) + '&limit=5')
                .then(res => res.json())
                .then(result => {
                    const products = result.data?.data || result.data || [];
                    
                    if (products.length === 0) {
                        suggestionsPanel.innerHTML = `
                            <div class="p-4 text-[12px] text-brand-muted text-center">No results found for "${escapeHtml(query)}"</div>
                        `;
                        suggestionsPanel.classList.remove('hidden');
                        return;
                    }
                    
                    let html = '';
                    products.forEach(p => {
                        let images = [];
                        try {
                            images = splitImageUrls(p.images);
                        } catch(e) {}
                        const imgUrl = images.length > 0 ? getAssetUrl(images[0]) : '/assets/images/hero_banner.png';
                        
                        html += `
                            <a href="/products/${p.id}" class="flex items-center gap-3 p-3 hover:bg-brand-darker border-b border-brand-border last:border-0 transition-colors">
                                <img src="${imgUrl}" alt="${escapeHtml(p.name)}" class="w-8 h-10 object-cover bg-brand-darker rounded border border-brand-border">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-[12px] font-semibold text-brand-text truncate">${escapeHtml(p.name)}</h4>
                                    <p class="text-[10px] text-brand-muted mt-0.5">${escapeHtml(p.brand || 'Premium')}</p>
                                </div>
                                <div class="text-[12px] font-bold text-brand-text">
                                    $${parseFloat(p.base_price).toFixed(2)}
                                </div>
                            </a>
                        `;
                    });
                    
                    suggestionsPanel.innerHTML = html;
                    suggestionsPanel.classList.remove('hidden');
                })
                .catch(err => {
                    console.error(err);
                });
            }, 250);
        }

        function hideSearchSuggestions() {
            setTimeout(() => {
                const suggestionsPanel = document.getElementById('search-suggestions');
                if (suggestionsPanel) suggestionsPanel.classList.add('hidden');
            }, 200);
        }

        // Quick View Modal JS Controllers
        let qvProduct = null;
        let qvSelectedColor = null;
        let qvSelectedSize = null;

        function openQuickView(productId, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            const backdrop = document.getElementById('quick-view-backdrop');
            const container = document.getElementById('quick-view-container');
            
            // Show modal card instantly
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            container.classList.remove('scale-95', 'opacity-0');
            
            // Try to extract the already-loaded details from DOM context
            let cardImgSrc = '/assets/images/hero_banner.png';
            let cardName = 'Product Name';
            let cardBrand = 'Premium';
            let cardPriceHtml = '';
            
            if (event && event.target) {
                const btn = event.target.closest('button');
                if (btn) {
                    const parentCard = btn.closest('.group') || btn.closest('.product-card') || btn.closest('.flex');
                    if (parentCard) {
                        const img = parentCard.querySelector('img');
                        if (img) cardImgSrc = img.src;
                        
                        const nameEl = parentCard.querySelector('h3') || parentCard.querySelector('h4');
                        if (nameEl) cardName = nameEl.textContent.trim();
                        
                        const brandEl = parentCard.querySelector('.brand') || parentCard.querySelector('.font-sans.text-\\[10px\\]');
                        if (brandEl) cardBrand = brandEl.textContent.trim();
                        
                        const priceEl = parentCard.querySelector('.price') || parentCard.querySelector('.font-sans.text-\\[14px\\].font-semibold');
                        if (priceEl) {
                            cardPriceHtml = priceEl.innerHTML;
                        }
                    }
                }
            }
            
            // Render basic card data instantly, but show skeletons for description and variants
            container.innerHTML = `
                <button onclick="closeQuickView(null)" class="absolute right-4 top-4 text-brand-muted hover:text-brand-text text-2xl z-10 focus:outline-none">&times;</button>
                
                <!-- Left: Product Image (instant, zero network lag) -->
                <div class="w-full md:w-1/2 bg-brand-darker border-r border-brand-border flex items-center justify-center p-6 min-h-[300px] md:min-h-[450px]">
                    <img id="qv-main-image" src="${cardImgSrc}" alt="Product Preview" class="max-w-full max-h-[400px] object-contain transition-opacity duration-150">
                </div>
                
                <!-- Right: Content -->
                <div class="w-full md:w-1/2 p-8 flex flex-col justify-between">
                    <div>
                        <span id="qv-brand" class="text-[10px] font-bold tracking-widest uppercase text-brand-muted">${escapeHtml(cardBrand)}</span>
                        <h2 id="qv-name" class="font-serif text-[1.8rem] text-brand-text font-semibold mt-1 mb-2 leading-tight">${escapeHtml(cardName)}</h2>
                        
                        <div id="qv-price-display" class="text-xl font-sans mb-5">
                            ${cardPriceHtml}
                        </div>
                        
                        <!-- Shimmering description skeleton -->
                        <div id="qv-description-skeleton" class="flex flex-col gap-2 mb-6">
                            <div class="w-full h-3 bg-brand-border/30 rounded animate-pulse"></div>
                            <div class="w-full h-3 bg-brand-border/30 rounded animate-pulse"></div>
                            <div class="w-5/6 h-3 bg-brand-border/30 rounded animate-pulse"></div>
                        </div>
                        
                        <p id="qv-description" class="text-[12.5px] text-brand-muted leading-relaxed mb-6 max-h-[120px] overflow-y-auto pr-2 hidden"></p>
                        
                        <form id="qv-variant-form" onsubmit="submitQvCart(event)" class="flex flex-col gap-5">
                            <input type="hidden" id="qv-selected-variant-id" required>
                            
                            <!-- Shimmering variants skeleton -->
                            <div id="qv-variants-skeleton" class="flex flex-col gap-4">
                                <div class="flex flex-col gap-2">
                                    <div class="w-16 h-3 bg-brand-border/40 rounded animate-pulse"></div>
                                    <div class="flex gap-2">
                                        <div class="w-12 h-8 bg-brand-border/35 rounded animate-pulse"></div>
                                        <div class="w-12 h-8 bg-brand-border/35 rounded animate-pulse"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Colors -->
                            <div id="qv-colors-container" class="hidden">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-2">Color</label>
                                <div id="qv-colors-list" class="flex gap-2 flex-wrap"></div>
                            </div>
                            
                            <!-- Sizes -->
                            <div id="qv-sizes-container" class="hidden">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-2">Size</label>
                                <div id="qv-sizes-list" class="flex gap-2 flex-wrap"></div>
                            </div>
                            
                            <!-- Stock display -->
                            <div id="qv-stock-display" class="text-[11.5px] text-brand-muted min-h-[16px] hidden">Select size & color</div>
                            
                            <!-- Action row -->
                            <div id="qv-actions-skeleton" class="flex gap-3 items-center mt-2">
                                <div class="w-24 h-10 bg-brand-border/40 rounded animate-pulse"></div>
                                <div class="flex-1 h-10 bg-brand-border/40 rounded animate-pulse"></div>
                            </div>
                            
                            <div id="qv-actions-container" class="flex gap-3 items-center mt-2 hidden">
                                <div class="flex items-center border border-brand-border rounded overflow-hidden bg-white">
                                    <button type="button" onclick="adjustQvQty(-1)" class="px-3 py-2 text-brand-muted hover:text-brand-text focus:outline-none">&minus;</button>
                                    <input type="number" id="qv-quantity-input" value="1" min="1" max="99" class="w-10 text-center text-xs font-semibold border-none outline-none pointer-events-none">
                                    <button type="button" onclick="adjustQvQty(1)" class="px-3 py-2 text-brand-muted hover:text-brand-text focus:outline-none">&plus;</button>
                                </div>
                                <button type="submit" id="qv-add-btn" class="flex-1 py-3 bg-brand-text text-brand-bg hover:bg-brand-text/90 rounded text-center text-[11px] font-bold uppercase tracking-widest transition-colors focus:outline-none disabled:bg-brand-border disabled:text-brand-muted" disabled>Add to Bag</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            
            fetch('http://localhost:8000/products/' + productId)
            .then(res => res.json())
            .then(result => {
                const product = result.data || result;
                if (!product || !product.id) return;
                
                qvProduct = product;
                qvSelectedColor = null;
                qvSelectedSize = null;
                
                // Hide placeholders and show real elements
                document.getElementById('qv-description-skeleton').classList.add('hidden');
                document.getElementById('qv-description').classList.remove('hidden');
                document.getElementById('qv-variants-skeleton').classList.add('hidden');
                document.getElementById('qv-actions-skeleton').classList.add('hidden');
                document.getElementById('qv-actions-container').classList.remove('hidden');
                document.getElementById('qv-stock-display').classList.remove('hidden');
                
                // Populate detailed contents
                document.getElementById('qv-brand').textContent = product.brand || 'Premium';
                document.getElementById('qv-name').textContent = product.name;
                document.getElementById('qv-description').innerHTML = product.description || 'No description available.';
                
                // Fetch catalog image or variant image URL from backend database if set, else keep card image
                const images = splitImageUrls(product.images);
                const mainImgUrl = images.length > 0 ? getAssetUrl(images[0]) : cardImgSrc;
                document.getElementById('qv-main-image').src = mainImgUrl;
                
                const colors = new Set();
                const sizes = new Set();
                
                (product.variants || []).forEach(v => {
                    if (v.color) colors.add(v.color);
                    if (v.size) sizes.add(v.size);
                });
                
                const colorsContainer = document.getElementById('qv-colors-container');
                const colorsList = document.getElementById('qv-colors-list');
                colorsList.innerHTML = '';
                if (colors.size > 0) {
                    colors.forEach(color => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'qv-color-btn px-3 py-1.5 border border-brand-border rounded text-xs hover:border-brand-text transition-colors';
                        btn.textContent = color;
                        btn.onclick = () => selectQvColor(color, btn);
                        colorsList.appendChild(btn);
                    });
                    colorsContainer.classList.remove('hidden');
                } else {
                    colorsContainer.classList.add('hidden');
                }
                
                const sizesContainer = document.getElementById('qv-sizes-container');
                const sizesList = document.getElementById('qv-sizes-list');
                sizesList.innerHTML = '';
                if (sizes.size > 0) {
                    sizes.forEach(size => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'qv-size-btn px-3 py-1.5 border border-brand-border rounded text-xs hover:border-brand-text transition-colors';
                        btn.textContent = size;
                        btn.onclick = () => selectQvSize(size, btn);
                        sizesList.appendChild(btn);
                    });
                    sizesContainer.classList.remove('hidden');
                } else {
                    sizesContainer.classList.add('hidden');
                }
                
                updateQvPrice();
                updateQvVariant();
            })
            .catch(err => {
                console.error(err);
                closeQuickView(null);
            });
        }

        function closeQuickView(event) {
            if (event && event.target !== document.getElementById('quick-view-backdrop')) return;
            
            document.getElementById('quick-view-backdrop').classList.add('opacity-0', 'pointer-events-none');
            document.getElementById('quick-view-container').classList.add('scale-95', 'opacity-0');
        }

        function selectQvColor(color, btn) {
            qvSelectedColor = qvSelectedColor === color ? null : color;
            
            document.querySelectorAll('.qv-color-btn').forEach(b => {
                if (b === btn && qvSelectedColor) {
                    b.classList.add('bg-brand-text', 'text-brand-bg', 'border-brand-text');
                } else {
                    b.classList.remove('bg-brand-text', 'text-brand-bg', 'border-brand-text');
                }
            });
            
            updateQvSizeAvailability();
            updateQvVariant();
        }

        function selectQvSize(size, btn) {
            qvSelectedSize = qvSelectedSize === size ? null : size;
            
            document.querySelectorAll('.qv-size-btn').forEach(b => {
                if (b === btn && qvSelectedSize) {
                    b.classList.add('bg-brand-text', 'text-brand-bg', 'border-brand-text');
                } else {
                    b.classList.remove('bg-brand-text', 'text-brand-bg', 'border-brand-text');
                }
            });
            
            updateQvVariant();
        }

        function updateQvSizeAvailability() {
            const availableSizes = [];
            (qvProduct.variants || []).forEach(v => {
                if (!qvSelectedColor || v.color === qvSelectedColor) {
                    if (v.stock_qty > 0) availableSizes.push(v.size);
                }
            });
            
            document.querySelectorAll('.qv-size-btn').forEach(btn => {
                const size = btn.textContent;
                if (availableSizes.includes(size)) {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                } else {
                    btn.disabled = true;
                    btn.style.opacity = '0.3';
                    if (qvSelectedSize === size) {
                        qvSelectedSize = null;
                        btn.classList.remove('bg-brand-text', 'text-brand-bg', 'border-brand-text');
                    }
                }
            });
        }

        function updateQvPrice() {
            const priceDisplay = document.getElementById('qv-price-display');
            const basePrice = parseFloat(qvProduct.base_price);
            const discountPercent = parseInt(qvProduct.discount_percent || 0);
            
            if (discountPercent > 0) {
                const discPrice = basePrice * (1 - discountPercent / 100);
                priceDisplay.innerHTML = `
                    <span class="text-brand-error font-bold mr-2">$${discPrice.toFixed(2)}</span>
                    <span class="text-[10px] bg-brand-errorBg text-brand-error px-1.5 py-0.5 rounded font-bold mr-2">-${discountPercent}%</span>
                    <span class="text-brand-muted text-xs line-through">$${basePrice.toFixed(2)}</span>
                `;
            } else {
                priceDisplay.innerHTML = `<span class="font-bold text-brand-text">$${basePrice.toFixed(2)}</span>`;
            }
        }

        function updateQvVariant() {
            const variants = qvProduct.variants || [];
            const variant = variants.find(v => 
                (!qvSelectedColor || v.color === qvSelectedColor) && 
                (!qvSelectedSize || v.size === qvSelectedSize)
            );
            
            const addBtn = document.getElementById('qv-add-btn');
            const stockDisplay = document.getElementById('qv-stock-display');
            const hiddenInput = document.getElementById('qv-selected-variant-id');
            const priceDisplay = document.getElementById('qv-price-display');
            
            if (variant) {
                hiddenInput.value = variant.id;
                
                const basePrice = parseFloat(variant.price);
                const discountPercent = parseInt(qvProduct.discount_percent || 0);
                if (discountPercent > 0) {
                    const discPrice = basePrice * (1 - discountPercent / 100);
                    priceDisplay.innerHTML = `
                        <span class="text-brand-error font-bold mr-2">$${discPrice.toFixed(2)}</span>
                        <span class="text-[10px] bg-brand-errorBg text-brand-error px-1.5 py-0.5 rounded font-bold mr-2">-${discountPercent}%</span>
                        <span class="text-brand-muted text-xs line-through">$${basePrice.toFixed(2)}</span>
                    `;
                } else {
                    priceDisplay.innerHTML = `<span class="font-bold text-brand-text">$${basePrice.toFixed(2)}</span>`;
                }
                
                if (variant.stock_qty > 0) {
                    stockDisplay.innerHTML = `<span class="text-brand-success font-semibold">In Stock (${variant.stock_qty} available)</span>`;
                    addBtn.disabled = false;
                } else {
                    stockDisplay.innerHTML = `<span class="text-brand-error font-semibold">Out of Stock</span>`;
                    addBtn.disabled = true;
                }
                
                if (variant.image_url) {
                    document.getElementById('qv-main-image').src = getAssetUrl(variant.image_url);
                } else {
                    const images = splitImageUrls(qvProduct.images);
                    const mainImgUrl = images.length > 0 ? getAssetUrl(images[0]) : '/assets/images/hero_banner.png';
                    document.getElementById('qv-main-image').src = mainImgUrl;
                }
            } else {
                hiddenInput.value = '';
                addBtn.disabled = true;
                stockDisplay.innerHTML = `<span class="text-brand-muted">Select size & color</span>`;
                
                const images = splitImageUrls(qvProduct.images);
                const mainImgUrl = images.length > 0 ? getAssetUrl(images[0]) : '/assets/images/hero_banner.png';
                document.getElementById('qv-main-image').src = mainImgUrl;
            }
        }

        function adjustQvQty(dir) {
            const qtyInput = document.getElementById('qv-quantity-input');
            let qty = parseInt(qtyInput.value) + dir;
            if (qty < 1) qty = 1;
            qtyInput.value = qty;
        }

        function submitQvCart(event) {
            event.preventDefault();
            const variantId = document.getElementById('qv-selected-variant-id').value;
            const qty = document.getElementById('qv-quantity-input').value;
            const btn = document.getElementById('qv-add-btn');
            
            if (!variantId) {
                alert('Please select variant options first.');
                return;
            }
            
            btn.disabled = true;
            btn.textContent = 'Adding...';
            
            fetch('http://localhost:8000/cart/items', {
                method: 'POST',
                headers: getAuthHeaders(),
                body: JSON.stringify({
                    session_id: getCartSessionId(),
                    variant_id: variantId,
                    quantity: qty
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.textContent = 'Add to Bag';
                
                if (data.error) {
                    alert(data.error);
                } else {
                    closeQuickView(null);
                    openMiniCart();
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.textContent = 'Add to Bag';
                alert('Error adding item to bag');
            });
        }
        </script>
        <main class="w-full max-w-[1280px] mx-auto min-h-[calc(100vh-200px)]">
    <?php endif; ?>

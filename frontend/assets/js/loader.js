/**
 * Hybrid SPA Navigation Engine with Shimmering Skeleton Wireframes
 * ClothesByKTRI Web Designer
 */

document.addEventListener('DOMContentLoaded', () => {
    const loadingBar = document.getElementById('topLoadingBar');
    const mainContent = document.querySelector('main');
    
    if (!loadingBar || !mainContent) return;

    let loadingTimer = null;
    let currentLoadingWidth = 0;

    // Start progress loading bar
    function startLoading() {
        clearInterval(loadingTimer);
        loadingBar.style.transition = 'width 0.2s ease-out, opacity 0.15s ease-in-out';
        loadingBar.style.opacity = '1';
        currentLoadingWidth = 5;
        loadingBar.style.width = currentLoadingWidth + '%';

        loadingTimer = setInterval(() => {
            if (currentLoadingWidth < 85) {
                // Slower increment as it approaches 85%
                currentLoadingWidth += Math.random() * (currentLoadingWidth < 50 ? 5 : 2);
                loadingBar.style.width = currentLoadingWidth + '%';
            }
        }, 150);
    }

    // Stop progress loading bar
    function stopLoading() {
        clearInterval(loadingTimer);
        loadingBar.style.transition = 'width 0.3s ease-out, opacity 0.3s ease-out';
        loadingBar.style.width = '100%';
        
        setTimeout(() => {
            loadingBar.style.opacity = '0';
            setTimeout(() => {
                loadingBar.style.width = '0%';
            }, 300);
        }, 200);
    }

    // Generate Shimmering Skeleton Templates
    function getSkeletonTemplate(urlPath) {
        const path = urlPath.toLowerCase();
        
        // Admin Pages Skeleton
        if (path.startsWith('/admin')) {
            return `
                <div class="section max-w-[1280px] mx-auto page-fade py-8 animate-pulse">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                        <!-- Left Sidebar Skeleton -->
                        <div class="lg:col-span-1">
                            <div class="bg-brand-darker border border-brand-border rounded-brand p-6 flex flex-col gap-4">
                                <div class="skeleton-shimmer h-6 w-32 rounded-brand mb-2"></div>
                                <div class="skeleton-shimmer h-11 w-full rounded-brand"></div>
                                <div class="skeleton-shimmer h-11 w-full rounded-brand"></div>
                                <div class="skeleton-shimmer h-11 w-full rounded-brand"></div>
                                <div class="skeleton-shimmer h-11 w-full rounded-brand"></div>
                                <div class="skeleton-shimmer h-11 w-full rounded-brand"></div>
                            </div>
                        </div>

                        <!-- Right Content Area Skeleton -->
                        <div class="lg:col-span-3 flex flex-col gap-8">
                            <!-- Header -->
                            <div class="flex justify-between items-center">
                                <div class="flex flex-col gap-2 w-1/2">
                                    <div class="skeleton-shimmer h-9 w-3/4 rounded-brand"></div>
                                    <div class="skeleton-shimmer h-4 w-5/6 rounded-brand"></div>
                                </div>
                                <div class="flex gap-3">
                                    <div class="skeleton-shimmer h-10 w-28 rounded-brand"></div>
                                    <div class="skeleton-shimmer h-10 w-28 rounded-brand"></div>
                                </div>
                            </div>

                            <!-- Stat Cards -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
                                <div class="skeleton-shimmer h-[100px] rounded-brand"></div>
                                <div class="skeleton-shimmer h-[100px] rounded-brand"></div>
                                <div class="skeleton-shimmer h-[100px] rounded-brand"></div>
                                <div class="skeleton-shimmer h-[100px] rounded-brand"></div>
                            </div>

                            <!-- Chart and alerts -->
                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                                <div class="xl:col-span-2 skeleton-shimmer h-[350px] rounded-brand"></div>
                                <div class="skeleton-shimmer h-[350px] rounded-brand"></div>
                            </div>

                            <!-- Table -->
                            <div class="skeleton-shimmer h-[250px] w-full rounded-brand"></div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Products List Page Skeleton
        if (path.includes('/products') && !path.match(/\/products\/\d+/)) {
            let cards = '';
            for (let i = 0; i < 6; i++) {
                cards += `
                    <div class="flex flex-col animate-pulse">
                        <div class="skeleton-shimmer w-full h-[240px] rounded-brand mb-4"></div>
                        <div class="skeleton-shimmer h-3.5 w-1/3 rounded-brand mb-2.5"></div>
                        <div class="skeleton-shimmer h-5 w-3/4 rounded-brand mb-3"></div>
                        <div class="skeleton-shimmer h-4.5 w-1/4 rounded-brand"></div>
                    </div>
                `;
            }
            return `
                <div class="section max-w-[1280px] mx-auto page-fade">
                    <div class="skeleton-shimmer h-10 w-48 rounded-brand mb-8 animate-pulse"></div>
                    <div class="skeleton-shimmer w-full h-[76px] rounded-brand mb-10 animate-pulse"></div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        ${cards}
                    </div>
                </div>
            `;
        }

        // Product Details Page Skeleton
        if (path.match(/\/products\/\d+/)) {
            return `
                <div class="section max-w-[1280px] mx-auto page-fade">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-16">
                        <!-- Left Column Image -->
                        <div class="skeleton-shimmer w-full h-[450px] md:h-[520px] rounded-brand animate-pulse"></div>
                        <!-- Right Column Info -->
                        <div class="flex flex-col gap-4 animate-pulse">
                            <div class="skeleton-shimmer h-4 w-1/4 rounded-brand"></div>
                            <div class="skeleton-shimmer h-10 w-3/4 rounded-brand mb-2"></div>
                            <div class="skeleton-shimmer h-6 w-1/3 rounded-brand mb-4"></div>
                            <div class="border-t border-brand-border pt-6 flex flex-col gap-4">
                                <div class="skeleton-shimmer h-4 w-full rounded-brand"></div>
                                <div class="skeleton-shimmer h-4 w-5/6 rounded-brand"></div>
                                <div class="skeleton-shimmer h-4 w-2/3 rounded-brand mb-6"></div>
                            </div>
                            <div class="skeleton-shimmer h-12 w-full rounded-brand mt-4"></div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Home Page Skeleton
        if (path === '/' || path === '' || path === '/index.php') {
            return `
                <div class="section max-w-[1280px] mx-auto page-fade flex flex-col gap-12 animate-pulse">
                    <!-- Hero Banner -->
                    <div class="skeleton-shimmer w-full h-[450px] rounded-brand"></div>
                    <!-- Highlights -->
                    <div class="flex flex-col gap-6">
                        <div class="skeleton-shimmer h-8 w-64 rounded-brand"></div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="skeleton-shimmer w-full h-[200px] rounded-brand"></div>
                            <div class="skeleton-shimmer w-full h-[200px] rounded-brand"></div>
                            <div class="skeleton-shimmer w-full h-[200px] rounded-brand"></div>
                            <div class="skeleton-shimmer w-full h-[200px] rounded-brand"></div>
                        </div>
                    </div>
                </div>
            `;
        }

        // General Fallback Skeleton (Cart, Auth, Info)
        return `
            <div class="section max-w-[800px] mx-auto page-fade animate-pulse py-12 flex flex-col gap-6">
                <div class="skeleton-shimmer h-9 w-1/2 rounded-brand mb-4"></div>
                <div class="skeleton-shimmer h-4 w-full rounded-brand"></div>
                <div class="skeleton-shimmer h-4 w-5/6 rounded-brand"></div>
                <div class="skeleton-shimmer h-4 w-2/3 rounded-brand mb-8"></div>
                <div class="skeleton-shimmer h-[120px] w-full rounded-brand"></div>
            </div>
        `;
    }

    // Intercept Link Clicks and Forms
    function shouldIntercept(anchor) {
        const href = anchor.getAttribute('href');
        if (!href) return false;
        
        // Skip target="_blank", anchors, mailto, tel, etc.
        if (anchor.target === '_blank' || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) {
            return false;
        }

        // Parse link URL
        const url = new URL(href, window.location.href);

        // Check if same origin (domain)
        if (url.origin !== window.location.origin) return false;

        const path = url.pathname;

        // Bypassed URLs (Logout)
        if (path.startsWith('/logout')) {
            return false;
        }

        // Exclude direct static file downloads or media files
        if (path.match(/\.(png|jpg|jpeg|gif|avif|webp|svg|pdf|zip|txt)$/i)) {
            return false;
        }

        return true;
    }

    // Dynamic Navigation Function
    async function navigateTo(urlStr, isPushState = true) {
        startLoading();

        const url = new URL(urlStr, window.location.href);
        const urlPath = url.pathname;

        // Transition 1: Fade out current main content
        mainContent.classList.add('page-fade', 'page-fade-out');

        // Wait for fade transition, then render wireframe skeleton
        await new Promise(resolve => setTimeout(resolve, 180));

        // Inject skeleton placeholder
        mainContent.innerHTML = getSkeletonTemplate(urlPath);
        mainContent.classList.remove('page-fade-out');

        const startTime = Date.now();

        try {
            // Fetch content
            const response = await fetch(urlStr);
            if (!response.ok) throw new Error('Failed to load page');

            const html = await response.text();
            
            // Parse DOM
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');
            const newMain = newDoc.querySelector('main');
            const newTitle = newDoc.querySelector('title');

            if (!newMain) throw new Error('No main element found in response');

            // Introduce a subtle minimum transition time (350ms) to prevent flickering if cache is too fast
            const elapsed = Date.now() - startTime;
            const remaining = Math.max(0, 350 - elapsed);
            if (remaining > 0) {
                await new Promise(resolve => setTimeout(resolve, remaining));
            }

            // Transition 2: Fade out skeleton
            mainContent.classList.add('page-fade-out');
            await new Promise(resolve => setTimeout(resolve, 150));

            // Swap page content
            mainContent.innerHTML = newMain.innerHTML;
            
            // Restore class lists of main if they differ
            mainContent.className = newMain.className;

            // Extract and execute scripts within main content
            const scripts = mainContent.querySelectorAll('script');
            scripts.forEach(script => {
                const newScript = document.createElement('script');
                if (script.src) {
                    newScript.src = script.src;
                } else {
                    let code = script.textContent;
                    // Convert const and let to var to prevent SyntaxError redeclarations in global scope
                    code = code.replace(/\bconst\s+/g, 'var ')
                               .replace(/\blet\s+/g, 'var ');
                    newScript.textContent = code;
                }
                document.body.appendChild(newScript);
                newScript.remove(); // Clean up script tag from DOM, global scope variables persist
            });

            // Update Title
            if (newTitle) {
                document.title = newTitle.textContent;
            }

            // Update URL in browser history
            if (isPushState) {
                window.history.pushState({ url: urlStr }, '', urlStr);
            }

            // Update active states on navigation links
            updateNavActiveStates(urlStr);

            // Scroll smoothly to top
            window.scrollTo({ top: 0, behavior: 'instant' });

            // Re-bind dynamically injected scripts/handlers
            rebindPageHandlers();

        } catch (error) {
            console.error('AJAX Load Error:', error);
            // On error, fallback to native navigation
            window.location.href = urlStr;
        } finally {
            // Transition 3: Fade in new content
            mainContent.classList.remove('page-fade-out');
            stopLoading();
        }
    }

    // Update active visual cues on navbar links
    function updateNavActiveStates(urlStr) {
        const url = new URL(urlStr, window.location.href);
        const currentPathname = url.pathname;
        const genderParam = url.searchParams.get('gender');

        // 1. Update storefront top sticky header links
        const headerActiveClasses = ['text-brand-text', 'after:absolute', 'after:bottom-0', 'after:left-0', 'after:w-full', 'after:h-[1.5px]', 'after:bg-brand-accent'];
        const headerInactiveClasses = ['text-brand-muted'];

        const headerLinks = document.querySelectorAll('nav.sticky a');
        headerLinks.forEach(link => {
            const linkHref = link.getAttribute('href');
            if (!linkHref) return;

            const linkUrl = new URL(linkHref, window.location.origin);
            const linkPathname = linkUrl.pathname;
            const linkGender = linkUrl.searchParams.get('gender');

            let isLinkActive = false;
            if (linkHref === '/') {
                isLinkActive = (currentPathname === '/');
            } else if (linkPathname === '/products') {
                if (linkGender) {
                    isLinkActive = (genderParam === linkGender);
                } else {
                    isLinkActive = (currentPathname === '/products' && !genderParam);
                }
            } else {
                isLinkActive = (currentPathname.startsWith(linkPathname));
            }

            if (isLinkActive) {
                link.classList.remove(...headerInactiveClasses);
                link.classList.add(...headerActiveClasses);
            } else {
                link.classList.remove(...headerActiveClasses);
                link.classList.add(...headerInactiveClasses);
            }
        });

        // 2. Update admin sidebar links
        const sidebarActiveClasses = ['bg-brand-text', 'text-brand-bg'];
        const sidebarInactiveClasses = ['text-brand-muted', 'hover:text-brand-text', 'hover:bg-brand-border/40'];

        const sidebarLinks = document.querySelectorAll('#adminSidebar nav a');
        sidebarLinks.forEach(link => {
            const linkHref = link.getAttribute('href');
            if (!linkHref) return;

            const linkUrl = new URL(linkHref, window.location.origin);
            const linkPathname = linkUrl.pathname;

            let isLinkActive = false;
            if (linkPathname === '/admin') {
                isLinkActive = (currentPathname === '/admin');
            } else {
                isLinkActive = (currentPathname.startsWith(linkPathname));
            }

            if (isLinkActive) {
                link.classList.remove(...sidebarInactiveClasses);
                link.classList.add(...sidebarActiveClasses);
            } else {
                link.classList.remove(...sidebarActiveClasses);
                link.classList.add(...sidebarInactiveClasses);
            }
        });
    }

    // Rebind page events like cart forms, image selectors, hamburger toggle, and flash timeouts
    function rebindPageHandlers() {
        // 1. Flash Timeout
        const flashEl = document.getElementById('flash-message');
        if (flashEl) {
            setTimeout(() => {
                flashEl.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
                flashEl.style.opacity = '0';
                flashEl.style.transform = 'translateY(-15px)';
                setTimeout(() => flashEl.remove(), 500);
            }, 3500);
        }

        // 2. Add to Cart Forms (Submit via AJAX optionally or direct browser native POST)
        // Since POST is destructive, we let standard form actions run unless they are GET forms like filter lists.
    }

    // Global click listener for SPA interception
    document.addEventListener('click', (e) => {
        const anchor = e.target.closest('a');
        if (anchor && shouldIntercept(anchor)) {
            e.preventDefault();
            navigateTo(anchor.href);
        }
    });

    // Global form submission listener for GET filters and search
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (form.method.toLowerCase() === 'get') {
            const action = form.getAttribute('action') || window.location.pathname;
            const url = new URL(action, window.location.origin);
            
            // Gather input fields
            const formData = new FormData(form);
            for (const [key, value] of formData.entries()) {
                if (value.trim()) {
                    url.searchParams.set(key, value);
                }
            }

            // Exclude empty search values or filters
            e.preventDefault();
            navigateTo(url.toString());
        }
    });

    // Handle browser back and forward actions
    window.addEventListener('popstate', (e) => {
        if (e.state && e.state.url) {
            navigateTo(e.state.url, false);
        } else {
            navigateTo(window.location.href, false);
        }
    });

    // Push initial history state for current load
    window.history.replaceState({ url: window.location.href }, '', window.location.href);
});

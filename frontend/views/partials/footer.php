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

    <footer class="bg-brand-darker border-t border-brand-border py-12 text-center text-brand-muted text-[12px] tracking-wide">
        <div class="max-w-[1280px] mx-auto px-8">
            <p>&copy; <?= date('Y') ?> Shop. All rights reserved.</p>
        </div>
    </footer>
    <script src="/assets/js/loader.js"></script>
</body>
</html>

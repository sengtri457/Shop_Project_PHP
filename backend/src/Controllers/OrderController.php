<?php

namespace App\Controllers;

use App\Models\Order;
use App\Core\Response;
use App\Core\Auth;

class OrderController
{
    public function index(): void
    {
        $customerId = isset($_GET['customer_id']) ? (int) $_GET['customer_id'] : null;

        $orders = Order::all($customerId);

        Response::json($orders);
    }

    public function show(int $id): void
    {
        $order = Order::find($id);

        if (!$order) {
            Response::error('Order not found', 404);
            return;
        }

        $order['items']     = Order::items($id);
        $order['discounts'] = Order::discounts($id);

        Response::json($order);
    }

    public function store(): void
    {
        $body = json_decode(file_get_contents('php://input'), true);

        $customerId = (int) ($body['customer_id'] ?? 0);
        $items      = $body['items'] ?? [];
        $addressId  = (int) ($body['address_id'] ?? 0);
        $discount   = $body['discount'] ?? null;
        $paymentMethod = $body['payment_method'] ?? 'cod';

        if (!$customerId || empty($items) || !$addressId) {
            Response::error('customer_id, items, and address_id are required');
            return;
        }

        try {
            $orderId = Order::create($customerId, $items, $addressId, $discount, $paymentMethod);
            $order = Order::find($orderId);
            $order['items']     = Order::items($orderId);
            $order['discounts'] = Order::discounts($orderId);

            Response::json($order, 201);
        } catch (\RuntimeException $e) {
            Response::error($e->getMessage());
        }
    }

    public function updateStatus(int $id): void
    {
        Auth::requireAdmin();

        $order = Order::find($id);

        if (!$order) {
            Response::error('Order not found', 404);
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $status = $body['status'] ?? '';

        $allowed = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $allowed)) {
            Response::error('Invalid status. Allowed: ' . implode(', ', $allowed));
            return;
        }

        Order::updateStatus($id, $status);

        Response::json(Order::find($id));
    }

    public function adminStats(): void
    {
        Auth::requireAdmin();

        $db = \App\Core\Database::getConnection();

        // 1. Total Revenue (exclude cancelled)
        $stmt = $db->query("SELECT SUM(total) as revenue FROM orders WHERE status != 'cancelled'");
        $totalRevenue = (float) ($stmt->fetch()['revenue'] ?? 0);

        // 2. Total Orders
        $stmt = $db->query("SELECT COUNT(id) as count FROM orders");
        $totalOrders = (int) ($stmt->fetch()['count'] ?? 0);

        // 3. Total Customers
        $stmt = $db->query("SELECT COUNT(id) as count FROM customers WHERE is_admin = 0");
        $totalCustomers = (int) ($stmt->fetch()['count'] ?? 0);

        // 4. Low Stock Variants (Alerts)
        $stmt = $db->query("
            SELECT pv.id, pv.sku, pv.stock_qty, p.name as product_name, pv.attributes
            FROM product_variants pv
            JOIN products p ON pv.product_id = p.id
            WHERE pv.stock_qty < 10
            ORDER BY pv.stock_qty ASC
        ");
        $lowStockVariants = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $totalAlerts = count($lowStockVariants);

        // 5. Order Status Counts
        $stmt = $db->query("SELECT status, COUNT(id) as count FROM orders GROUP BY status");
        $statusCounts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 6. Sales Over Time (Last 30 days)
        $stmt = $db->query("
            SELECT DATE(created_at) as date, SUM(total) as daily_total
            FROM orders
            WHERE status != 'cancelled' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at) ASC
        ");
        $salesOverTime = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 7. Recent Orders
        $stmt = $db->query("
            SELECT o.*, c.name as customer_name
            FROM orders o
            JOIN customers c ON o.customer_id = c.id
            ORDER BY o.created_at DESC
            LIMIT 5
        ");
        $recentOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 8. Top Selling Products
        $stmt = $db->query("
            SELECT p.id, p.name, p.images, SUM(oi.quantity) as total_qty, SUM(oi.price_at_purchase * oi.quantity) as total_sales
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN product_variants pv ON oi.variant_id = pv.id
            JOIN products p ON pv.product_id = p.id
            WHERE o.status != 'cancelled'
            GROUP BY p.id, p.name, p.images
            ORDER BY total_qty DESC
            LIMIT 5
        ");
        $topSelling = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 9. Category Sales
        $stmt = $db->query("
            SELECT c.name as category_name, SUM(oi.quantity) as total_qty, SUM(oi.price_at_purchase * oi.quantity) as total_sales
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN product_variants pv ON oi.variant_id = pv.id
            JOIN products p ON pv.product_id = p.id
            JOIN product_categories pc ON p.id = pc.product_id
            JOIN categories c ON pc.category_id = c.id
            WHERE o.status != 'cancelled'
            GROUP BY c.id, c.name
            ORDER BY total_sales DESC
        ");
        $categorySales = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 10. Recent Customers
        $stmt = $db->query("
            SELECT id, name, email, created_at 
            FROM customers 
            WHERE is_admin = 0 
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        $recentCustomers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 11. Average Order Value (AOV)
        $aov = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        Response::json([
            'revenue' => $totalRevenue,
            'orders_count' => $totalOrders,
            'customers_count' => $totalCustomers,
            'alerts_count' => $totalAlerts,
            'low_stock' => $lowStockVariants,
            'status_counts' => $statusCounts,
            'sales_chart' => $salesOverTime,
            'recent_orders' => $recentOrders,
            'top_selling' => $topSelling,
            'category_sales' => $categorySales,
            'recent_customers' => $recentCustomers,
            'aov' => $aov,
        ]);
    }
}

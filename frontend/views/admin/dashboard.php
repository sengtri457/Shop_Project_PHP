<?php
$res = api_get('/admin/stats');
$stats = $res['data'] ?? [];

$revenue = $stats['revenue'] ?? 0;
$ordersCount = $stats['orders_count'] ?? 0;
$customersCount = $stats['customers_count'] ?? 0;
$alertsCount = $stats['alerts_count'] ?? 0;
$lowStock = $stats['low_stock'] ?? [];
$statusCounts = $stats['status_counts'] ?? [];
$salesChart = $stats['sales_chart'] ?? [];
$recentOrders = $stats['recent_orders'] ?? [];
$topSelling = $stats['top_selling'] ?? [];
$categorySales = $stats['category_sales'] ?? [];
$recentCustomers = $stats['recent_customers'] ?? [];
$aov = $stats['aov'] ?? 0;

// Group chart data for Chart.js
$chartLabels = [];
$chartData = [];
foreach ($salesChart as $day) {
    $chartLabels[] = date('M d', strtotime($day['date']));
    $chartData[] = (float) $day['daily_total'];
}
if (empty($chartLabels)) {
    $chartLabels = [date('M d')];
    $chartData = [0];
}
?>


            <!-- Header Info -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
                <div>
                    <h1 class="font-serif text-[2.5rem] font-medium text-brand-text">Admin Dashboard</h1>
                    <p class="text-[14px] text-brand-muted mt-1">Real-time overview of your store's performance and inventory workflow.</p>
                </div>
                <div class="flex gap-3">
                    <a href="/admin/products" class="px-5 py-2.5 bg-brand-text text-brand-bg hover:bg-brand-accent rounded-brand text-[12px] font-semibold tracking-wider uppercase transition-all duration-300">Quick Inventory</a>
                    <a href="/admin/orders" class="px-5 py-2.5 bg-transparent border border-brand-border hover:border-brand-text text-brand-text rounded-brand text-[12px] font-semibold tracking-wider uppercase transition-all duration-300">Order Queue</a>
                </div>
            </div>

            <!-- Stat Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-5 mb-10">
                <!-- Revenue -->
                <div class="bg-brand-darker border border-brand-border rounded-brand p-5 flex flex-col justify-between">
                    <span class="text-[10px] font-semibold tracking-widest uppercase text-brand-muted">Total Revenue</span>
                    <div class="mt-4">
                        <span class="text-xl font-serif font-semibold text-brand-text">$<?= number_format($revenue, 2) ?></span>
                    </div>
                    <p class="text-[11px] text-brand-muted mt-2">Exclude cancelled</p>
                </div>

                <!-- Average Order Value (AOV) -->
                <div class="bg-brand-darker border border-brand-border rounded-brand p-5 flex flex-col justify-between">
                    <span class="text-[10px] font-semibold tracking-widest uppercase text-brand-muted">Avg Order Value</span>
                    <div class="mt-4">
                        <span class="text-xl font-serif font-semibold text-brand-text">$<?= number_format($aov, 2) ?></span>
                    </div>
                    <p class="text-[11px] text-brand-muted mt-2">AOV per checkout</p>
                </div>
                
                <!-- Orders -->
                <div class="bg-brand-darker border border-brand-border rounded-brand p-5 flex flex-col justify-between">
                    <span class="text-[10px] font-semibold tracking-widest uppercase text-brand-muted">Total Orders</span>
                    <div class="mt-4">
                        <span class="text-xl font-serif font-semibold text-brand-text"><?= $ordersCount ?></span>
                    </div>
                    <p class="text-[11px] text-brand-muted mt-2">Lifetime purchases</p>
                </div>

                <!-- Customers -->
                <div class="bg-brand-darker border border-brand-border rounded-brand p-5 flex flex-col justify-between">
                    <span class="text-[10px] font-semibold tracking-widest uppercase text-brand-muted">Active Customers</span>
                    <div class="mt-4">
                        <span class="text-xl font-serif font-semibold text-brand-text"><?= $customersCount ?></span>
                    </div>
                    <p class="text-[11px] text-brand-muted mt-2">Buyer accounts</p>
                </div>

                <!-- Low Stock Alerts -->
                <div class="bg-brand-darker border <?= $alertsCount > 0 ? 'border-brand-accent/50 bg-brand-accentLight/20' : 'border-brand-border' ?> rounded-brand p-5 flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <span class="text-[10px] font-semibold tracking-widest uppercase <?= $alertsCount > 0 ? 'text-brand-accent' : 'text-brand-muted' ?>">Stock Alerts</span>
                        <?php if ($alertsCount > 0): ?>
                            <span class="h-2 w-2 rounded-full bg-brand-accent animate-pulse"></span>
                        <?php endif; ?>
                    </div>
                    <div class="mt-4">
                        <span class="text-xl font-serif font-semibold <?= $alertsCount > 0 ? 'text-brand-accent' : 'text-brand-text' ?>"><?= $alertsCount ?></span>
                    </div>
                    <p class="text-[11px] <?= $alertsCount > 0 ? 'text-brand-accent' : 'text-brand-muted' ?> mt-2">
                        <?= $alertsCount > 0 ? 'Items under 10 units' : 'Fully stocked' ?>
                    </p>
                </div>
            </div>

            <!-- Charts & Alerts Block -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-10">
                <!-- Sales Trend Line Chart -->
                <div class="xl:col-span-2 bg-brand-bg border border-brand-border rounded-brand p-6">
                    <h3 class="font-serif text-[1.25rem] font-semibold mb-6 text-brand-text">Revenue Timeline (Last 30 Days)</h3>
                    <div class="relative w-full h-[300px]">
                        <canvas id="salesTrendChart"></canvas>
                    </div>
                </div>

                <!-- Low Stock Alert Panel -->
                <div class="bg-brand-bg border border-brand-border rounded-brand p-6 flex flex-col">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-serif text-[1.25rem] font-semibold text-brand-text">Critical Stock</h3>
                        <span class="text-[10px] font-semibold px-2 py-0.5 bg-brand-accentLight text-brand-accent rounded">Alerts</span>
                    </div>
                    <div class="flex-1 overflow-y-auto max-h-[300px] pr-2">
                        <?php if (empty($lowStock)): ?>
                            <div class="flex flex-col items-center justify-center h-full py-10 text-center">
                                <svg class="w-8 h-8 text-brand-muted mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-brand-muted text-[12px]">Inventory is running smoothly. No warnings.</p>
                            </div>
                        <?php else: ?>
                            <div class="flex flex-col gap-3">
                                <?php foreach ($lowStock as $item): 
                                    $attrs = json_decode($item['attributes'] ?? '{}', true);
                                    $attrStr = !empty($attrs) ? implode(' / ', $attrs) : '';
                                ?>
                                    <div class="p-3.5 bg-brand-darker border-l-[3px] border-brand-accent rounded flex justify-between items-center gap-4">
                                        <div>
                                            <h4 class="font-sans text-[12.5px] font-semibold text-brand-text leading-snug"><?= htmlspecialchars($item['product_name']) ?></h4>
                                            <p class="text-[10.5px] text-brand-muted mt-0.5">SKU: <?= htmlspecialchars($item['sku']) ?> <?= $attrStr ? " | $attrStr" : "" ?></p>
                                        </div>
                                        <div class="flex items-center gap-3.5 flex-shrink-0">
                                            <span class="text-[13px] font-bold text-brand-accent"><?= $item['stock_qty'] ?> left</span>
                                            <a href="/admin/purchase-orders/new?variant_id=<?= $item['id'] ?>&qty=20" class="p-1.5 bg-brand-accent text-white hover:bg-brand-accentHover rounded transition-colors shadow-sm" title="Reorder 20 units">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="width: 14px; height: 14px;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Analytics Breakdown Block -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
                <!-- Top Selling Products -->
                <div class="bg-brand-bg border border-brand-border rounded-brand p-6">
                    <h3 class="font-serif text-[1.2rem] font-semibold text-brand-text mb-6">Top Selling Products</h3>
                    <div class="flex flex-col gap-4">
                        <?php if (empty($topSelling)): ?>
                            <p class="text-brand-muted text-[12px] text-center py-6">No products sold yet.</p>
                        <?php else: ?>
                            <?php foreach ($topSelling as $index => $item): 
                                $imgList = !empty($item['images']) ? split_image_urls($item['images']) : [];
                                $image = !empty($imgList[0]) ? asset_url($imgList[0]) : '/assets/images/placeholder.png';
                            ?>
                                <div class="flex items-center gap-3.5">
                                    <div class="font-serif font-bold text-[14px] text-brand-muted w-4">#<?= $index + 1 ?></div>
                                    <img src="<?= htmlspecialchars($image) ?>" alt="" class="w-10 h-10 object-cover rounded bg-brand-darker border border-brand-border">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-[12.5px] font-semibold text-brand-text truncate"><?= htmlspecialchars($item['name']) ?></h4>
                                        <p class="text-[10.5px] text-brand-muted mt-0.5"><?= $item['total_qty'] ?> units sold</p>
                                    </div>
                                    <div class="text-[12.5px] font-semibold text-brand-text">$<?= number_format($item['total_sales'], 2) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sales by Category -->
                <div class="bg-brand-bg border border-brand-border rounded-brand p-6">
                    <h3 class="font-serif text-[1.2rem] font-semibold text-brand-text mb-6">Sales by Category</h3>
                    <div class="flex flex-col gap-4.5">
                        <?php if (empty($categorySales)): ?>
                            <p class="text-brand-muted text-[12px] text-center py-6">No sales recorded.</p>
                        <?php else: 
                            $maxSales = array_sum(array_column($categorySales, 'total_sales'));
                            foreach ($categorySales as $cat): 
                                $percent = $maxSales > 0 ? ($cat['total_sales'] / $maxSales) * 100 : 0;
                            ?>
                                <div>
                                    <div class="flex justify-between items-center text-[12px] mb-1.5">
                                        <span class="font-semibold text-brand-text"><?= htmlspecialchars($cat['category_name']) ?></span>
                                        <span class="text-brand-muted font-medium">$<?= number_format($cat['total_sales'], 2) ?> (<?= round($percent) ?>%)</span>
                                    </div>
                                    <div class="w-full bg-brand-darker h-2 rounded-full overflow-hidden">
                                        <div class="bg-brand-accent h-full rounded-full transition-all duration-500" style="width: <?= $percent ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Customers -->
                <div class="bg-brand-bg border border-brand-border rounded-brand p-6">
                    <h3 class="font-serif text-[1.2rem] font-semibold text-brand-text mb-6">Recent Customer Signups</h3>
                    <div class="flex flex-col gap-4">
                        <?php if (empty($recentCustomers)): ?>
                            <p class="text-brand-muted text-[12px] text-center py-6">No customer accounts yet.</p>
                        <?php else: ?>
                            <?php foreach ($recentCustomers as $c): 
                                $initials = strtoupper(substr($c['name'], 0, 2));
                            ?>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-brand-accentLight text-brand-accent flex items-center justify-center font-bold text-[11px] uppercase tracking-wider shrink-0">
                                        <?= $initials ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-[12.5px] font-semibold text-brand-text truncate leading-tight"><?= htmlspecialchars($c['name']) ?></h4>
                                        <p class="text-[10.5px] text-brand-muted mt-0.5 truncate leading-none"><?= htmlspecialchars($c['email']) ?></p>
                                    </div>
                                    <div class="text-[10px] text-brand-muted shrink-0 text-right">
                                        <?= date('M d', strtotime($c['created_at'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Workflow: Recent Orders -->
            <div class="bg-brand-bg border border-brand-border rounded-brand p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-serif text-[1.25rem] font-semibold text-brand-text">Recent Orders Workflow</h3>
                    <a href="/admin/orders" class="text-[11px] font-semibold text-brand-accent hover:underline">View All Orders &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-brand-border text-brand-muted text-[10px] uppercase tracking-wider">
                                <th class="py-3 px-3 font-semibold">Order ID</th>
                                <th class="py-3 px-3 font-semibold">Customer</th>
                                <th class="py-3 px-3 font-semibold">Date</th>
                                <th class="py-3 px-3 font-semibold">Total</th>
                                <th class="py-3 px-3 font-semibold">Status</th>
                                <th class="py-3 px-3 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-border">
                            <?php if (empty($recentOrders)): ?>
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-brand-muted text-[12px]">No orders processed yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentOrders as $o): 
                                    $statusColor = 'bg-brand-muted/10 text-brand-muted';
                                    if ($o['status'] === 'confirmed' || $o['status'] === 'processing') {
                                        $statusColor = 'bg-brand-accentLight text-brand-accent';
                                    } elseif ($o['status'] === 'shipped' || $o['status'] === 'delivered') {
                                        $statusColor = 'bg-brand-successBg text-brand-success';
                                    } elseif ($o['status'] === 'cancelled') {
                                        $statusColor = 'bg-brand-errorBg text-brand-error';
                                    }
                                ?>
                                    <tr class="text-[12.5px] text-brand-text hover:bg-brand-darker/40 transition-colors">
                                        <td class="py-3.5 px-3 font-semibold">#<?= $o['id'] ?></td>
                                        <td class="py-3.5 px-3"><?= htmlspecialchars($o['customer_name'] ?? 'Guest') ?></td>
                                        <td class="py-3.5 px-3 text-brand-muted"><?= date('M d, Y H:i', strtotime($o['created_at'])) ?></td>
                                        <td class="py-3.5 px-3 font-semibold">$<?= number_format($o['total'], 2) ?></td>
                                        <td class="py-3.5 px-3">
                                            <span class="inline-block px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded <?= $statusColor ?>">
                                                <?= htmlspecialchars($o['status']) ?>
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3 text-right">
                                            <a href="/orders/<?= $o['id'] ?>" class="text-[10.5px] font-bold uppercase tracking-wider text-brand-text hover:text-brand-accent">Manage</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    function initDashboardChart() {
        if (typeof Chart === 'undefined') {
            setTimeout(initDashboardChart, 50);
            return;
        }
        var ctx = document.getElementById('salesTrendChart');
        if (!ctx) return;

        var labels = <?= json_encode($chartLabels) ?>;
        var data = <?= json_encode($chartData) ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Daily Revenue ($)',
                    data: data,
                    borderColor: '#A35C49',
                    backgroundColor: 'rgba(163, 92, 73, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#A35C49',
                    pointBorderColor: '#FAFAF8',
                    pointHoverRadius: 6,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#1A1A1A',
                        titleColor: '#FAFAF8',
                        bodyColor: '#FAFAF8',
                        displayColors: false,
                        font: {
                            family: 'Inter'
                        }
                    }
                },
                scales: {
                    y: {
                        grid: {
                            color: '#E6E6E2',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6A6A65',
                            font: {
                                family: 'Inter',
                                size: 11
                            },
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6A6A65',
                            font: {
                                family: 'Inter',
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        initDashboardChart();
    } else {
        document.addEventListener('DOMContentLoaded', initDashboardChart);
    }
})();
</script>

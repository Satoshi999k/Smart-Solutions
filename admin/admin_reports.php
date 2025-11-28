<?php
session_start();

// Check if admin is logged in (new system uses is_admin flag)
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    // Also check for old admin session for backwards compatibility
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: ../user/register.php");
        exit();
    }
}

$conn = new mysqli("localhost", "root", "", "smartsolutions");

// Get monthly revenue
$monthly_revenue = $conn->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_price) as revenue 
    FROM orders 
    GROUP BY month 
    ORDER BY month DESC 
    LIMIT 6
");

// Get top products (from orders)
$conn2 = new mysqli("localhost", "root", "", "smartsolutions");
$all_orders = $conn2->query("SELECT order_details FROM orders");
$product_sales = [];

if ($all_orders && $all_orders->num_rows > 0) {
    while ($order = $all_orders->fetch_assoc()) {
        $order_details = json_decode($order['order_details'], true);
        if (is_array($order_details) && isset($order_details['items'])) {
            foreach ($order_details['items'] as $item) {
                $name = $item['product_name'] ?? 'Unknown';
                if (!isset($product_sales[$name])) {
                    $product_sales[$name] = ['quantity' => 0, 'revenue' => 0];
                }
                $product_sales[$name]['quantity'] += $item['quantity'] ?? 1;
                $product_sales[$name]['revenue'] += $item['subtotal'] ?? (($item['price'] ?? 0) * ($item['quantity'] ?? 1));
            }
        }
    }
}

// Sort by quantity
arsort($product_sales);
$top_products = array_slice($product_sales, 0, 10, true);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin</title>
    <link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid #34495e;
            text-align: center;
        }
        
        .sidebar-header img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        
        .sidebar-header h2 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .sidebar-header p {
            font-size: 12px;
            color: #bdc3c7;
        }
        
        .sidebar-menu {
            margin-top: 20px;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #34495e;
        }
        
        .sidebar-menu a i {
            margin-right: 10px;
            width: 20px;
            display: inline-block;
            font-size: 20px;
            vertical-align: middle;
        }
        
        .material-icons {
            font-family: 'Material Icons';
            font-weight: normal;
            font-style: normal;
            font-size: 20px;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
        }
        
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 20px;
        }
        
        .top-bar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .top-bar h1 {
            color: #2c3e50;
            font-size: 24px;
        }
        
        .logout-btn {
            padding: 10px 20px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .report-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .report-card h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th {
            background: #f8f9fa;
            padding: 10px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e0e0e0;
            font-size: 13px;
        }
        
        table td {
            padding: 10px;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
            font-size: 13px;
        }
        
        .chart-placeholder {
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .reports-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="../image/smartsolutionslogo.jpg" alt="Admin" onerror="this.src='../image/login-icon.png'">
                <h2>Admin Panel</h2>
                <p><?php echo htmlspecialchars($_SESSION['first_name']); ?></p>
            </div>
            
            <div class="sidebar-menu">
                <a href="/ITP122/admin/admin_dashboard.php"><i class="material-icons">dashboard</i> Dashboard</a>
                <a href="/ITP122/admin/admin_orders.php"><i class="material-icons">shopping_cart</i> Orders</a>
                <a href="/ITP122/admin/admin_users.php"><i class="material-icons">people</i> Users</a>
                <a href="/ITP122/admin/admin_products.php"><i class="material-icons">inventory_2</i> Products</a>
                <a href="/ITP122/admin/admin_reports.php" class="active"><i class="material-icons">trending_up</i> Reports</a>
                <a href="/ITP122/admin/admin_settings.php"><i class="material-icons">settings</i> Settings</a>
            </div>
        </div>
        
        <div class="main-content">
            <div class="top-bar">
                <h1>Sales Reports & Analytics</h1>
                <a href="/ITP122/admin/admin_logout.php" class="logout-btn">Logout</a>
            </div>
            
            <div class="reports-grid">
                <div class="report-card">
                    <h3><i class="material-icons" style="vertical-align: middle; margin-right: 5px;">show_chart</i> Monthly Revenue</h3>
                    <?php if ($monthly_revenue && $monthly_revenue->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $monthly_revenue->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('F Y', strtotime($row['month'] . '-01')); ?></td>
                                <td>₱<?php echo number_format($row['revenue'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p style="color: #999; text-align: center; padding: 20px;">No revenue data available</p>
                    <?php endif; ?>
                </div>
                
                <div class="report-card">
                    <h3><i class="material-icons" style="vertical-align: middle; margin-right: 5px;">star</i> Top Selling Products</h3>
                    <?php if (count($top_products) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $count = 0;
                            foreach ($top_products as $name => $data): 
                                if ($count++ >= 5) break;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars(substr($name, 0, 30)); ?></td>
                                <td><?php echo $data['quantity']; ?></td>
                                <td>₱<?php echo number_format($data['revenue'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p style="color: #999; text-align: center; padding: 20px;">No sales data available</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="report-card">
                <h3><i class="material-icons" style="vertical-align: middle; margin-right: 5px;">trending_up</i> Monthly Revenue Chart</h3>
                <canvas id="revenueChart" style="max-height: 400px; margin-top: 20px;"></canvas>
            </div>
            
            <div class="report-card">
                <h3><i class="material-icons" style="vertical-align: middle; margin-right: 5px;">analytics</i> Top Products Chart</h3>
                <canvas id="productsChart" style="max-height: 400px; margin-top: 20px;"></canvas>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Revenue Chart Data
        <?php 
        $revenue_query = $conn->query("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_price) as revenue 
            FROM orders 
            GROUP BY month 
            ORDER BY month ASC
        ");
        
        $months = [];
        $revenues = [];
        
        if ($revenue_query && $revenue_query->num_rows > 0) {
            while ($row = $revenue_query->fetch_assoc()) {
                $months[] = date('M Y', strtotime($row['month'] . '-01'));
                $revenues[] = floatval($row['revenue']);
            }
        }
        ?>
        
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($months); ?>,
                    datasets: [{
                        label: 'Monthly Revenue (₱)',
                        data: <?php echo json_encode($revenues); ?>,
                        backgroundColor: 'rgba(102, 126, 234, 0.2)',
                        borderColor: 'rgba(102, 126, 234, 1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(102, 126, 234, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += '₱' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Products Chart Data
        <?php 
        $product_names = [];
        $product_quantities = [];
        $count = 0;
        foreach ($top_products as $name => $data) {
            if ($count++ >= 5) break;
            $product_names[] = substr($name, 0, 25);
            $product_quantities[] = intval($data['quantity']);
        }
        ?>
        
        // Products Chart
        const productsCtx = document.getElementById('productsChart');
        if (productsCtx) {
            new Chart(productsCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($product_names); ?>,
                    datasets: [{
                        label: 'Units Sold',
                        data: <?php echo json_encode($product_quantities); ?>,
                        backgroundColor: [
                            'rgba(76, 175, 80, 0.8)',
                            'rgba(33, 150, 243, 0.8)',
                            'rgba(255, 152, 0, 0.8)',
                            'rgba(156, 39, 176, 0.8)',
                            'rgba(244, 67, 54, 0.8)'
                        ],
                        borderColor: [
                            'rgba(76, 175, 80, 1)',
                            'rgba(33, 150, 243, 1)',
                            'rgba(255, 152, 0, 1)',
                            'rgba(156, 39, 176, 1)',
                            'rgba(244, 67, 54, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Sold: ' + context.parsed.y + ' units';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
<?php 
$conn->close(); 
$conn2->close();
?>

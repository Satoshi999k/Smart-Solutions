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

// Get all orders
$orders_result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Admin</title>
    <link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
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
        
        .content-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e0e0e0;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
        }
        
        table tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge.success {
            background: #d4edda;
            color: #155724;
        }
        
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            background: #2196f3;
            color: white;
        }
        
        .action-btn:hover {
            opacity: 0.8;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
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
                <a href="/ITP122/admin/admin_dashboard.php"><i>📊</i> Dashboard</a>
                <a href="/ITP122/admin/admin_orders.php" class="active"><i>🛒</i> Orders</a>
                <a href="/ITP122/admin/admin_users.php"><i>👥</i> Users</a>
                <a href="/ITP122/admin/admin_products.php"><i>📦</i> Products</a>
                <a href="/ITP122/admin/admin_reports.php"><i>📈</i> Reports</a>
                <a href="/ITP122/admin/admin_settings.php"><i>⚙️</i> Settings</a>
            </div>
        </div>
        
        <div class="main-content">
            <div class="top-bar">
                <h1>Orders Management</h1>
                <a href="/ITP122/admin/admin_logout.php" class="logout-btn">Logout</a>
            </div>
            
            <div class="content-section">
                <h2>All Orders</h2>
                <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $orders_result->fetch_assoc()): 
                            $customer = json_decode($order['customer_details'], true);
                            $order_details = json_decode($order['order_details'], true);
                            $item_count = isset($order_details['items']) ? count($order_details['items']) : 0;
                            $customer_name = (isset($customer['first_name']) && isset($customer['last_name'])) 
                                ? $customer['first_name'] . ' ' . $customer['last_name'] 
                                : ($customer['name'] ?? 'N/A');
                        ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($customer_name); ?></td>
                            <td>₱<?php echo number_format($order['total_price'], 2); ?></td>
                            <td><?php echo $item_count; ?> item(s)</td>
                            <td><span class="badge success">Completed</span></td>
                            <td>
                                <button class="action-btn" onclick="viewOrder(<?php echo $order['id']; ?>)">View Details</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="no-data">No orders found</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        function viewOrder(id) {
            window.location.href = 'admin_order_details.php?id=' + id;
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>

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

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_price) as total FROM orders")->fetch_assoc()['total'] ?? 0;

// Get logged-in user's profile picture
$user_profile_picture = "../image/login-icon.png";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $profile_query = "SELECT profile_picture FROM users WHERE id = ?";
    $profile_stmt = $conn->prepare($profile_query);
    $profile_stmt->bind_param("i", $user_id);
    $profile_stmt->execute();
    $profile_result = $profile_stmt->get_result();
    if ($profile_row = $profile_result->fetch_assoc()) {
        if (!empty($profile_row['profile_picture'])) {
            $user_profile_picture = '/ITP122/' . $profile_row['profile_picture'];
        }
    }
    $profile_stmt->close();
}

// Recent orders
$recent_orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");

// Get users
$users_result = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 10");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Smart Solutions</title>
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
        
        /* Sidebar */
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
        
        /* Main Content */
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
        
        .logout-btn:hover {
            background: #c0392b;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }
        
        .stat-icon .material-icons {
            font-size: 32px;
        }
        
        .stat-icon.blue {
            background: #e3f2fd;
            color: #2196f3;
        }
        
        .stat-icon.green {
            background: #e8f5e9;
            color: #4caf50;
        }
        
        .stat-icon.orange {
            background: #fff3e0;
            color: #ff9800;
        }
        
        .stat-info h3 {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .stat-info p {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        /* Tables */
        .content-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .content-section h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
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
        
        .badge.warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge.info {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .badge.danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin-right: 5px;
        }
        
        .action-btn.view {
            background: #2196f3;
            color: white;
        }
        
        .action-btn.edit {
            background: #ff9800;
            color: white;
        }
        
        .action-btn.delete {
            background: #f44336;
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
            
            .top-bar {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            table {
                font-size: 12px;
            }
            
            table th,
            table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="../image/smartsolutionslogo.jpg" alt="Admin" onerror="this.src='../image/login-icon.png'">
                <h2>Admin Panel</h2>
                <p><?php echo htmlspecialchars(isset($_SESSION['first_name']) ? $_SESSION['first_name'] : (isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin')); ?></p>
            </div>
            
            <div class="sidebar-menu">
                <a href="/ITP122/admin/admin_dashboard.php" class="active"><i class="material-icons">dashboard</i> Dashboard</a>
                <a href="/ITP122/admin/admin_orders.php"><i class="material-icons">shopping_cart</i> Orders</a>
                <a href="/ITP122/admin/admin_users.php"><i class="material-icons">people</i> Users</a>
                <a href="/ITP122/admin/admin_products.php"><i class="material-icons">inventory_2</i> Products</a>
                <a href="/ITP122/admin/admin_reports.php"><i class="material-icons">trending_up</i> Reports</a>
                <a href="/ITP122/admin/admin_settings.php"><i class="material-icons">settings</i> Settings</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <h1>Dashboard Overview</h1>
                <a href="/ITP122/admin/admin_logout.php" class="logout-btn">Logout</a>
            </div>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="material-icons">group</i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_users; ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="material-icons">shopping_bag</i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_orders; ?></h3>
                        <p>Total Orders</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="material-icons">attach_money</i>
                    </div>
                    <div class="stat-info">
                        <h3>₱<?php echo number_format($total_revenue, 2); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="content-section">
                <h2>Recent Orders</h2>
                <?php if ($recent_orders && $recent_orders->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $recent_orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td>₱<?php echo number_format($order['total_price'], 2); ?></td>
                            <td>
                                <?php
                                    $status = $order['status'] ?? 'Pending';
                                    $badge_class = 'badge success';
                                    
                                    if (strtolower($status) === 'completed') {
                                        $badge_class = 'badge success';
                                    } elseif (strtolower($status) === 'shipped') {
                                        $badge_class = 'badge info';
                                    } elseif (strtolower($status) === 'processing') {
                                        $badge_class = 'badge warning';
                                    } elseif (strtolower($status) === 'pending') {
                                        $badge_class = 'badge warning';
                                    } elseif (strtolower($status) === 'cancelled') {
                                        $badge_class = 'badge danger';
                                    }
                                ?>
                                <span class="<?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                            </td>
                            <td>
                                <button class="action-btn view" onclick="viewOrder(<?php echo $order['id']; ?>)">View</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="no-data">No orders found</div>
                <?php endif; ?>
            </div>
            
            <!-- Recent Users -->
            <div class="content-section">
                <h2>Recent Users</h2>
                <?php if ($users_result && $users_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Email/Username</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <?php 
                                $profile_pic = "../image/login-icon.png";
                                if (!empty($user['profile_picture'])) {
                                    $profile_pic = '/ITP122/' . $user['profile_picture'];
                                }
                                ?>
                                <img src="<?php echo $profile_pic; ?>" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            </td>
                            <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <button class="action-btn view" onclick="viewUser(<?php echo $user['id']; ?>)">View</button>
                                <button class="action-btn edit" onclick="editUser(<?php echo $user['id']; ?>)">Edit</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="no-data">No users found</div>
                <?php endif; ?>
            </div>
            
            <!-- Charts Section -->
            <div class="content-section">
                <h2>Sales Overview</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; margin-top: 20px;">
                    <div>
                        <h3 style="color: #555; margin-bottom: 15px; font-size: 16px;">Monthly Revenue Trend</h3>
                        <canvas id="miniRevenueChart" style="max-height: 250px;"></canvas>
                    </div>
                    <div>
                        <h3 style="color: #555; margin-bottom: 15px; font-size: 16px;">Order Status Distribution</h3>
                        <canvas id="orderStatusChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function viewOrder(id) {
            window.location.href = 'admin_order_details.php?id=' + id;
        }
        
        function viewUser(id) {
            window.location.href = 'admin_view_user.php?id=' + id;
        }
        
        function editUser(id) {
            window.location.href = 'admin_user_details.php?id=' + id;
        }
        
        // Mini Revenue Chart
        <?php
        $revenue_data = $conn->query("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_price) as revenue 
            FROM orders 
            GROUP BY month 
            ORDER BY month ASC 
            LIMIT 6
        ");
        $months = [];
        $revenues = [];
        if ($revenue_data) {
            while ($row = $revenue_data->fetch_assoc()) {
                $months[] = date('M Y', strtotime($row['month'] . '-01'));
                $revenues[] = $row['revenue'];
            }
        }
        ?>
        
        const miniRevenueCtx = document.getElementById('miniRevenueChart').getContext('2d');
        new Chart(miniRevenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: <?php echo json_encode($revenues); ?>,
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(102, 126, 234, 1)',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
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
        
        // Order Status Chart (Doughnut)
        const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Pending', 'Processing'],
                datasets: [{
                    data: [<?php echo $total_orders; ?>, 0, 0],
                    backgroundColor: [
                        'rgba(76, 175, 80, 0.8)',
                        'rgba(255, 152, 0, 0.8)',
                        'rgba(33, 150, 243, 0.8)'
                    ],
                    borderColor: [
                        'rgba(76, 175, 80, 1)',
                        'rgba(255, 152, 0, 1)',
                        'rgba(33, 150, 243, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>

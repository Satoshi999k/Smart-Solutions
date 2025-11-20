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

// Fetch products from database
$products_result = $conn->query("SELECT * FROM products ORDER BY id");
$products = [];

if ($products_result) {
    while ($row = $products_result->fetch_assoc()) {
        $products[] = $row;
    }
}

$total_products = count($products);
$total_stock = 0;
foreach ($products as $product) {
    $total_stock += ($product['stock'] ?? 0);
}

$conn->close();

// Check for success or error messages
$success_message = '';
$error_message = '';
if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management - Admin</title>
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
        
        .logout-btn:hover {
            background: #c0392b;
        }
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-box h3 {
            font-size: 32px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .stat-box p {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .content-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .add-btn {
            padding: 10px 20px;
            background: #4caf50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .add-btn:hover {
            background: #45a049;
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
        
        .badge.in-stock {
            background: #d4edda;
            color: #155724;
        }
        
        .badge.low-stock {
            background: #fff3cd;
            color: #856404;
        }
        
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin-right: 5px;
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
        
        .no-products {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #f44336;
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
            
            .section-header {
                flex-direction: column;
                gap: 15px;
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
                <p><?php echo htmlspecialchars($_SESSION['first_name'] ?? 'Admin'); ?></p>
            </div>
            
            <div class="sidebar-menu">
                <a href="/ITP122/admin/admin_dashboard.php"><i>📊</i> Dashboard</a>
                <a href="/ITP122/admin/admin_orders.php"><i>🛒</i> Orders</a>
                <a href="/ITP122/admin/admin_users.php"><i>👥</i> Users</a>
                <a href="/ITP122/admin/admin_products.php" class="active"><i>📦</i> Products</a>
                <a href="/ITP122/admin/admin_reports.php"><i>📈</i> Reports</a>
                <a href="/ITP122/admin/admin_settings.php"><i>⚙️</i> Settings</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <h1>Products Management</h1>
                <a href="/ITP122/admin/admin_logout.php" class="logout-btn">Logout</a>
            </div>
            
            <!-- Messages -->
            <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">✓ <?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
            <div class="alert alert-error">✗ <?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            
            <!-- Stats -->
            <div class="stats-row">
                <div class="stat-box">
                    <h3><?php echo $total_products; ?></h3>
                    <p>Total Products</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $total_stock; ?></h3>
                    <p>Total Stock</p>
                </div>
                <div class="stat-box">
                    <h3>8</h3>
                    <p>Categories</p>
                </div>
            </div>
            
            <!-- Products Table -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Product Inventory</h2>
                    <button class="add-btn" onclick="addProduct()">+ Add Product</button>
                </div>
                
                <?php if (!empty($products)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></td>
                            <td>₱<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo isset($product['stock']) ? $product['stock'] : 0; ?></td>
                            <td>
                                <span class="badge <?php echo (isset($product['stock']) ? $product['stock'] : 0) > 5 ? 'in-stock' : 'low-stock'; ?>">
                                    <?php echo (isset($product['stock']) ? $product['stock'] : 0) > 5 ? 'In Stock' : 'Low Stock'; ?>
                                </span>
                            </td>
                            <td>
                                <button class="action-btn edit" onclick="editProduct(<?php echo $product['id']; ?>)">Edit</button>
                                <button class="action-btn delete" onclick="deleteProduct(<?php echo $product['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="no-products">
                    <p>No products found. <a href="insert_products.php">Click here to import products.</a></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        function addProduct() {
            window.location.href = 'admin_add_product.php';
        }
        
        function editProduct(id) {
            window.location.href = 'admin_edit_product.php?id=' + id;
        }
        
        function deleteProduct(id) {
            window.location.href = 'admin_delete_product.php?id=' + id;
        }
    </script>
</body>
</html>

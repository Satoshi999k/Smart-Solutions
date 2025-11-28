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
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            display: inline-block;
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
            padding: 8px 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            margin-right: 5px;
            transition: all 0.3s ease;
            display: inline-block;
            min-width: 70px;
            text-align: center;
        }
        
        .action-btn.view {
            background: #2196f3;
            color: white;
        }
        
        .action-btn.view:hover {
            background: #1976d2;
            box-shadow: 0 2px 8px rgba(33, 150, 243, 0.3);
        }
        
        .action-btn.edit {
            background: #ff9800;
            color: white;
        }
        
        .action-btn.edit:hover {
            background: #f57c00;
            box-shadow: 0 2px 8px rgba(255, 152, 0, 0.3);
        }
        
        .action-btn.delete {
            background: #f44336;
            color: white;
        }
        
        .action-btn.delete:hover {
            background: #da190b;
            box-shadow: 0 2px 8px rgba(244, 67, 54, 0.3);
        }
        
        .actions-cell {
            white-space: nowrap;
            width: 280px;
        }
        
        .product-image-cell {
            text-align: center;
            padding: 8px !important;
        }
        
        .product-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .product-thumb:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
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
        
        /* Custom SweetAlert Styles */
        .custom-delete-btn {
            background: linear-gradient(135deg, #f44336 0%, #e53935 100%) !important;
            color: white !important;
            padding: 14px 24px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 15px rgba(244, 67, 54, 0.3) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }
        
        .custom-delete-btn:hover {
            background: linear-gradient(135deg, #e53935 0%, #d32f2f 100%) !important;
            box-shadow: 0 8px 25px rgba(244, 67, 54, 0.4) !important;
            transform: translateY(-2px) !important;
        }
        
        .custom-delete-btn:focus {
            outline: none !important;
        }
        
        .custom-cancel-btn {
            background: linear-gradient(135deg, #f5f5f5 0%, #eeeeee 100%) !important;
            color: #2c3e50 !important;
            padding: 14px 24px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }
        
        .custom-cancel-btn:hover {
            background: linear-gradient(135deg, #eeeeee 0%, #e0e0e0 100%) !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-2px) !important;
        }
        
        .custom-cancel-btn:focus {
            outline: none !important;
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
                <a href="/ITP122/admin/admin_dashboard.php"><i class="material-icons">dashboard</i> Dashboard</a>
                <a href="/ITP122/admin/admin_orders.php"><i class="material-icons">shopping_cart</i> Orders</a>
                <a href="/ITP122/admin/admin_users.php"><i class="material-icons">people</i> Users</a>
                <a href="/ITP122/admin/admin_products.php" class="active"><i class="material-icons">inventory_2</i> Products</a>
                <a href="/ITP122/admin/admin_reports.php"><i class="material-icons">trending_up</i> Reports</a>
                <a href="/ITP122/admin/admin_settings.php"><i class="material-icons">settings</i> Settings</a>
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
                            <th>Image</th>
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
                            <td class="product-image-cell">
                                <?php 
                                    $image_path = $product['image'] ?? 'image/default-product.png';
                                    if (!preg_match('/^(\/|http)/', $image_path)) {
                                        $image_path = '../' . $image_path;
                                    }
                                ?>
                                <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Product" class="product-thumb" onerror="this.src='../image/default-product.png'">
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></td>
                            <td>₱<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo isset($product['stock']) ? $product['stock'] : 0; ?></td>
                            <td>
                                <span class="badge <?php echo (isset($product['stock']) ? $product['stock'] : 0) > 5 ? 'in-stock' : 'low-stock'; ?>">
                                    <?php echo (isset($product['stock']) ? $product['stock'] : 0) > 5 ? 'In Stock' : 'Low Stock'; ?>
                                </span>
                            </td>
                            <td class="actions-cell">
                                <button class="action-btn view" onclick="viewProduct(<?php echo $product['id']; ?>)">View</button>
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
        
        function viewProduct(id) {
            // Fetch product details via AJAX
            $.ajax({
                url: 'get_product_details.php',
                method: 'GET',
                data: { id: id },
                dataType: 'json',
                success: function(product) {
                    let imagePath = product.image;
                    // Adjust image path for proper display
                    if (!imagePath.startsWith('/') && !imagePath.startsWith('http')) {
                        imagePath = '../' + imagePath;
                    }
                    
                    let stockClass = product.stock > 5 ? 'in-stock' : 'low-stock';
                    let stockText = product.stock > 5 ? 'In Stock' : 'Low Stock';
                    
                    let htmlContent = `
                        <div style="text-align: left; display: flex; gap: 30px;">
                            <div style="flex: 0 0 40%; text-align: center;">
                                <img src="${imagePath}" alt="${product.name}" style="max-width: 100%; height: auto; border-radius: 10px;">
                            </div>
                            <div style="flex: 1;">
                                <h3 style="color: #2c3e50; margin-bottom: 15px; font-size: 20px;">${product.name}</h3>
                                <p style="margin: 10px 0;"><strong>Price:</strong> <span style="color: #e74c3c; font-size: 18px; font-weight: bold;">₱${parseFloat(product.price).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span></p>
                                <p style="margin: 10px 0;"><strong>Category:</strong> ${product.category || 'N/A'}</p>
                                <p style="margin: 10px 0;"><strong>Stock:</strong> <span style="padding: 5px 10px; border-radius: 20px; font-weight: 600; background: ${stockClass === 'in-stock' ? '#d4edda' : '#fff3cd'}; color: ${stockClass === 'in-stock' ? '#155724' : '#856404'}">${product.stock} units</span></p>
                                <p style="margin: 10px 0;"><strong>Product ID:</strong> #${product.id}</p>
                                ${product.description ? `<div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;"><strong>Description:</strong><br>${product.description}</div>` : ''}
                            </div>
                        </div>
                    `;
                    
                    Swal.fire({
                        title: 'Product Details',
                        html: htmlContent,
                        icon: 'info',
                        width: 800,
                        showCancelButton: true,
                        confirmButtonText: 'Edit',
                        cancelButtonText: 'Close',
                        confirmButtonColor: '#ff9800',
                        cancelButtonColor: '#95a5a6'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'admin_edit_product.php?id=' + id;
                        }
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Failed to load product details', 'error');
                }
            });
        }
        
        function editProduct(id) {
            window.location.href = 'admin_edit_product.php?id=' + id;
        }
        
        function deleteProduct(id) {
            // Fetch product details
            fetch('get_product_details.php?id=' + id)
                .then(res => res.json())
                .then(product => {
                    if (!product) {
                        Swal.fire('Error', 'Product not found!', 'error');
                        return;
                    }
                    
                    Swal.fire({
                        title: 'Delete Product',
                        icon: 'warning',
                        html: `
                            <div style="text-align: left;">
                                <div style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 12px; padding: 25px; margin: 25px 0; border-left: 4px solid #f44336;">
                                    <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
                                        <span style="font-weight: 600; color: #2c3e50; display: flex; align-items: center; gap: 8px;">
                                            <span style="font-family: 'Material Icons'; font-size: 18px; color: #667eea;">fingerprint</span>
                                            Product ID
                                        </span>
                                        <span style="color: #34495e; font-weight: 500;">${product.id}</span>
                                    </div>
                                    <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
                                        <span style="font-weight: 600; color: #2c3e50; display: flex; align-items: center; gap: 8px;">
                                            <span style="font-family: 'Material Icons'; font-size: 18px; color: #667eea;">local_offer</span>
                                            Product Name
                                        </span>
                                        <span style="color: #34495e; font-weight: 500;">${product.name}</span>
                                    </div>
                                    <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
                                        <span style="font-weight: 600; color: #2c3e50; display: flex; align-items: center; gap: 8px;">
                                            <span style="font-family: 'Material Icons'; font-size: 18px; color: #667eea;">attach_money</span>
                                            Price
                                        </span>
                                        <span style="color: #34495e; font-weight: 500;">₱${parseFloat(product.price).toFixed(2)}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
                                        <span style="font-weight: 600; color: #2c3e50; display: flex; align-items: center; gap: 8px;">
                                            <span style="font-family: 'Material Icons'; font-size: 18px; color: #667eea;">inventory_2</span>
                                            Stock
                                        </span>
                                        <span style="color: #34495e; font-weight: 500;">${product.stock} units</span>
                                    </div>
                                </div>
                                <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 10px; padding: 15px; display: flex; gap: 12px; align-items: flex-start;">
                                    <span style="font-family: 'Material Icons'; font-size: 20px; color: #ff9800; flex-shrink: 0; margin-top: 2px;">warning_amber</span>
                                    <div style="text-align: left;">
                                        <div style="color: #856404; font-size: 13px; line-height: 1.6; font-weight: 500;">
                                            <strong>This action cannot be undone.</strong> This product will be permanently deleted from the database and cannot be recovered.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonColor: '#f44336',
                        cancelButtonColor: '#757575',
                        confirmButtonText: 'Delete',
                        cancelButtonText: 'Cancel',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'custom-delete-btn',
                            cancelButton: 'custom-cancel-btn'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteProductConfirmed(id);
                        }
                    });
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Failed to load product details', 'error');
                });
        }
        
        function deleteProductConfirmed(id) {
            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send delete request
            fetch('admin_delete_product.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + id + '&confirm_delete=1'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Product has been deleted successfully.',
                        icon: 'success',
                        confirmButtonColor: '#667eea'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to delete product', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'An error occurred while deleting the product', 'error');
            });
        }
    </script>
</body>
</html>

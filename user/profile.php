<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get profile picture
$profile_picture = '../image/login-icon.png'; // Default fallback
if (!empty($user['profile_picture'])) {
    // If the profile picture path doesn't start with /, add the /ITP122/ prefix
    if (strpos($user['profile_picture'], '/') === 0) {
        $profile_picture = $user['profile_picture'];
    } else {
        $profile_picture = '/ITP122/' . $user['profile_picture'];
    }
}

// Fetch user's orders 
$orders = [];
$user_id = $_SESSION['user_id'];

// Query orders table for user's orders
$orders_query = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 50";
$orders_stmt = $conn->prepare($orders_query);

if ($orders_stmt) {
    $orders_stmt->bind_param("i", $user_id);
    $orders_stmt->execute();
    $orders_result = $orders_stmt->get_result();
    $orders = $orders_result->fetch_all(MYSQLI_ASSOC);
    $orders_stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/design.css" />
    <link rel="stylesheet" href="../css/animations.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <meta charset="UTF-8">
    <title>My Profile - SMARTSOLUTIONS</title>
    <style>
        body { background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%) !important; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .breadcrumb { padding: 18px 32px; font-size: 14px; color: #666; background: transparent; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .breadcrumb a { color: #0062F6; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 6px; }
        .breadcrumb a:hover { color: #0052D4; transform: translateX(4px); }
        .breadcrumb .material-icons { font-size: 20px; vertical-align: middle; }
        .breadcrumb span:not(.material-icons) { display: inline-flex; align-items: center; }
        @keyframes slideDownMenu {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #main-menu {
            animation: slideDownMenu 0.6s ease-out 0.3s both;
        }
        
        .profile-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .profile-header {
            background: linear-gradient(135deg, #0062F6 0%, #0052D4 100%);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 98, 246, 0.25);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 40px;
            animation: slideInDown 0.6s ease-out;
        }
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .profile-pic-large {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 6px solid white;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }
        .profile-pic-large:hover {
            transform: scale(1.05);
        }
        .profile-info-header h2 {
            margin: 0 0 15px 0;
            color: white;
            font-size: 28px;
            font-weight: 700;
        }
        .profile-info-header p {
            margin: 8px 0;
            color: rgba(255, 255, 255, 0.9);
            font-size: 15px;
        }
        .profile-edit-btn {
            background-color: white;
            color: #0062F6;
            border: none;
            padding: 12px 28px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .profile-edit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }
        .profile-content {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 30px;
        }
        .profile-sidebar {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            height: fit-content;
            position: sticky;
            top: 100px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li {
            margin-bottom: 8px;
        }
        .sidebar-menu a {
            display: block;
            padding: 14px 16px;
            text-decoration: none;
            color: #333;
            border-radius: 8px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-weight: 500;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: linear-gradient(135deg, #f0f7ff 0%, #e6f2ff 100%);
            border-left-color: #0062F6;
            color: #0062F6;
        }
        .profile-main {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.6s ease-out 0.2s both;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 25px;
            padding-bottom: 16px;
            border-bottom: 3px solid #0062F6;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 30px;
        }
        .info-item {
            padding: 20px;
            background: linear-gradient(135deg, #f5f9ff 0%, #f0f5ff 100%);
            border-radius: 10px;
            border-left: 4px solid #0062F6;
            transition: all 0.3s ease;
        }
        .info-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 98, 246, 0.12);
        }
        .info-label {
            font-weight: 600;
            color: #0062F6;
            font-size: 13px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-value {
            color: #333;
            font-size: 16px;
            font-weight: 500;
        }
        .orders-section {
            margin-top: 40px;
            animation: fadeInUp 0.8s ease-out 0.3s both;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .order-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 16px;
            border-left: 5px solid #0062F6;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .order-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 98, 246, 0.1));
            transition: right 0.6s ease;
            pointer-events: none;
        }
        .order-card:hover {
            box-shadow: 0 12px 32px rgba(0, 98, 246, 0.15);
            transform: translateY(-4px);
        }
        .order-card:hover::before {
            right: 100%;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 2px solid #e8e8e8;
        }
        .order-id {
            font-weight: 700;
            color: #0062F6;
            font-size: 16px;
            letter-spacing: 0.5px;
        }
        .order-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            backdrop-filter: blur(10px);
        }
        .status-completed {
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.2) 0%, rgba(16, 185, 129, 0.2) 100%);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .status-processing {
            background: linear-gradient(135deg, rgba(96, 165, 250, 0.2) 0%, rgba(59, 130, 246, 0.2) 100%);
            color: #2563eb;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        .status-shipped {
            background: linear-gradient(135deg, rgba(34, 197, 228, 0.2) 0%, rgba(6, 182, 212, 0.2) 100%);
            color: #0891b2;
            border: 1px solid rgba(6, 182, 212, 0.3);
        }
        .status-pending {
            background: linear-gradient(135deg, rgba(250, 204, 21, 0.2) 0%, rgba(217, 119, 6, 0.2) 100%);
            color: #b45309;
            border: 1px solid rgba(217, 119, 6, 0.3);
        }
        .status-cancelled {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(220, 38, 38, 0.2) 100%);
            color: #dc2626;
            border: 1px solid rgba(220, 38, 38, 0.3);
        }
        .order-details {
            margin-top: 16px;
        }
        .order-details p {
            margin: 10px 0;
            color: #555;
            font-size: 15px;
        }
        .no-orders {
            text-align: center;
            padding: 60px 40px;
            color: #999;
            background: linear-gradient(135deg, #f5f9ff 0%, #f0f5ff 100%);
            border-radius: 12px;
            border: 2px dashed #d0d0d0;
            font-size: 16px;
        }
        .order-item-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }
        .order-item-row:last-child {
            border-bottom: none;
        }
        .order-item-details {
            flex: 1;
        }
        .order-item-details strong {
            font-size: 13px;
            margin-bottom: 2px;
            display: block;
        }
        .order-item-details small {
            font-size: 11px;
            color: #666;
        }
        .order-item-price {
            text-align: right;
            font-weight: bold;
            color: #007BFF;
            min-width: 80px;
            white-space: nowrap;
        }
        /* Hide orders beyond the first 3 */
        .order-card:nth-child(n+4) {
            display: none;
        }
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
        }
        footer.footer {
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .search-bar {
                order: 3;
                width: 100%;
                margin: 10px 0;
            }
            
            .search-bar input {
                width: 100%;
                max-width: 100%;
                padding: 8px 35px 8px 15px;
            }
            
            .search-icon {
                right: 15px !important;
            }
            .profile-content {
                grid-template-columns: 1fr;
            }
            .info-grid {
                grid-template-columns: 1fr;
            }
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header>
    <div class="ssheader">
        <div class="logo">
            <img src="../image/logo.png" alt="Smart Solutions Logo">
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search">
            <img class="search-icon" src="../image/search-icon.png" alt="Search" style="width: 20px; height: 20px; cursor: pointer;">
        </div>
        <a href="../pages/location.php">
            <div class="location">
                <img class="location" src="../image/location-icon.png" alt="location-icon">
            </div>
        </a>
        <div class="track">
            <a href="../track.php"><img class="track" src="../image/track-icon.png" alt="track-icon"></a>
        </div>
        <a href="../cart.php">
            <div class="cart">
                <img class="cart" src="../image/cart-icon.png" alt="cart-icon" style="width: 35px; height: auto;">
                <span class="cart-counter">
                    <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                </span>
            </div>
        </a>

        <style>
            .cart {
                position: relative;
                display: inline-block;
            }

            .cart-counter {
                position: absolute;
                top: 20px; /* Adjust as needed */
                right: -10px; /* Adjust as needed */
                background-color: #6c757d;
                color: white;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                font-size: 14px;
                font-weight: bold;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
            }
        </style>

       <div class="login profile-dropdown">
            <a href="javascript:void(0)" onclick="toggleDropdown()">
                <!-- Check if user is logged in, if yes show profile picture, else show login icon -->
                <img class="login" 
                    src="<?php echo isset($_SESSION['user_id']) ? $profile_picture : '../image/login-icon.png'; ?>" 
                    alt="login-icon" 
                    style="border-radius: <?php echo isset($_SESSION['user_id']) ? '50%' : '0'; ?>; 
                            width: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>; 
                            height: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>;">
            </a>
            <div id="dropdown-menu" class="dropdown-content">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">View Profile</a>
                    <a href="edit-profile.php">Edit Profile</a>
                    <a href="logout.php">Log Out</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="login-text">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php"></a>
                <?php else: ?>
                    <a href="register.php"><p>Login/<br>Sign In</p></a>
                <?php endif; ?>
            </div>
    </div>
    </header>

    <div class="menu" id="main-menu">
            <a href="../index.php">HOME</a>
            <a href="../pages/product.php">PRODUCTS</a>
            <a href="../products/desktop.php">DESKTOP</a>
            <a href="../products/laptop.php">LAPTOP</a>
            <a href="../pages/brands.php">BRANDS</a>
    </div>

    <div class="breadcrumb">
        <a href="../index.php"><span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px;">home</span>Home</a> > <span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px; color: #0062F6;">account_circle</span><a>My Profile</a>
    </div>

    <div class="main-content">
        <div class="profile-container">
            <!-- Profile Header -->
            <div class="profile-header">
                <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" class="profile-pic-large">
                <div class="profile-info-header">
                    <h2><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Member Since:</strong> <?php echo date('F Y', strtotime($user['created_at'] ?? 'now')); ?></p>
                    <a href="edit-profile.php" class="profile-edit-btn">Edit Profile</a>
                </div>
            </div>

            <!-- Profile Content -->
            <div class="profile-content">
                <!-- Sidebar -->
                <div class="profile-sidebar">
                    <ul class="sidebar-menu">
                        <li><a href="#personal-info" class="active">Personal Information</a></li>
                        <li><a href="#orders">My Orders</a></li>
                        <li><a href="edit-profile.php">Account Settings</a></li>
                    </ul>
                </div>

                <!-- Main Content -->
                <div class="profile-main">
                    <!-- Personal Information Section -->
                    <div id="personal-info">
                        <h3 class="section-title">Personal Information</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">First Name</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['first_name']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Last Name</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['last_name']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email Address</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Phone Number</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['phone_number'] ?? 'Not provided'); ?></div>
                            </div>
                        </div>

                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="info-label">Home Address</div>
                            <div class="info-value"><?php echo htmlspecialchars($user['address'] ?? 'Not provided'); ?></div>
                        </div>

                        <div class="info-grid" style="margin-top: 20px;">
                            <div class="info-item">
                                <div class="info-label">Postal Code</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['postal_code'] ?? 'Not provided'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Section -->
                    <div id="orders" class="orders-section">
                        <h3 class="section-title">My Orders</h3>
                        <?php if (!empty($orders)): ?>
                            <div style="margin-top: 5px;">
                            <?php 
                                $order_count = 0;
                                $total_orders = count($orders);
                                foreach ($orders as $order): 
                                    if ($order_count >= 1) break; // Only display first 1 order
                            ?>
                                <?php 
                                    // Parse JSON data from order
                                    $order_details = isset($order['order_details']) ? json_decode($order['order_details'], true) : [];
                                    $customer_details = isset($order['customer_details']) ? json_decode($order['customer_details'], true) : [];
                                    $order_items = isset($order_details['items']) ? $order_details['items'] : [];
                                    
                                    // Get order info
                                    $order_id = $order['id'] ?? $order['order_id'] ?? 'N/A';
                                    $total_amount = $order['total_price'] ?? $order['total'] ?? 0;
                                    $status = $order['status'] ?? 'processing'; // Default to processing if not set
                                    
                                    // Get date
                                    $date_key = isset($order['order_date']) ? 'order_date' : (isset($order['created_at']) ? 'created_at' : 'date');
                                    $order_date = isset($order[$date_key]) ? $order[$date_key] : 'N/A';
                                    
                                    $order_count++;
                                ?>
                                <div class="order-card">
                                    <div class="order-header">
                                        <div>
                                            <span class="order-id">Order #<?php echo htmlspecialchars($order_id); ?></span>
                                            <p style="margin: 5px 0 0 0; font-size: 14px; color: #999;">
                                                <?php echo $order_date !== 'N/A' ? date('F d, Y', strtotime($order_date)) : 'Date N/A'; ?>
                                            </p>
                                        </div>
                                        <span class="order-status status-<?php echo strtolower($status); ?>">
                                            <?php echo htmlspecialchars(ucfirst($status)); ?>
                                        </span>
                                    </div>
                                    <div class="order-details">
                                        <p><strong>Total Amount:</strong> ₱<?php echo number_format($total_amount, 2); ?></p>
                                        
                                        <?php if (!empty($order_items)): ?>
                                            <p style="margin-top: 15px; margin-bottom: 10px;"><strong>Items Ordered:</strong></p>
                                            <div style="background-color: #f5f5f5; padding: 10px; border-radius: 4px; margin-bottom: 10px;">
                                                <?php foreach ($order_items as $item): ?>
                                                    <div style="padding: 8px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
                                                        <span>
                                                            <strong><?php echo htmlspecialchars($item['product_name'] ?? 'Product'); ?></strong>
                                                            <br>
                                                            <small style="color: #666;">Qty: <?php echo intval($item['quantity'] ?? 1); ?> × ₱<?php echo number_format($item['price'] ?? 0, 2); ?></small>
                                                        </span>
                                                        <span style="font-weight: bold; color: #007BFF;">
                                                            ₱<?php echo number_format($item['subtotal'] ?? 0, 2); ?>
                                                        </span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <p><strong>Delivery Address:</strong> 
                                            <?php 
                                                if (!empty($customer_details)) {
                                                    $address = ($customer_details['address'] ?? '') . ' ' . 
                                                               ($customer_details['apartment'] ?? '') . ', ' .
                                                               ($customer_details['city'] ?? '') . ', ' .
                                                               ($customer_details['region'] ?? '') . ', ' .
                                                               ($customer_details['country'] ?? '');
                                                    echo htmlspecialchars(trim($address));
                                                } else {
                                                    echo htmlspecialchars($order['delivery_address'] ?? $order['address'] ?? 'N/A');
                                                }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            </div>
                            
                            <?php if ($total_orders > 1): ?>
                                <div style="text-align: center; margin-top: 10px; padding: 10px;">
                                    <a href="orders_view.php" class="profile-edit-btn" style="display: inline-block; padding: 12px 30px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px; cursor: pointer; transition: 0.3s; font-weight: bold; border: none;">
                                        View All Orders (<?php echo $total_orders; ?> total)
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="no-orders">
                                <p>📦 You haven't placed any orders yet.</p>
                                <a href="../product.php" class="profile-edit-btn" style="display: inline-block; margin-top: 15px;">Start Shopping</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
    <div class="footer-col">
        <h3>Customer Service</h3>
        <ul>
            <li><a href="../pages/paymentfaq.php">Payment FAQs</a></li>
            <li><a href="../pages/ret&ref.php">Return and Refunds</a></li>
        </ul>
    </div>
    <div class="footer-col">
        <h3>Company</h3>
        <ul>
            <li><a href="../pages/about_us.php">About Us</a></li>
            <li><a href="../pages/contact_us.php">Contact Us</a></li>
        </ul>
    </div>
    <div class="footer-col">
        <h3>Links</h3>
        <ul>
            <li><a href="../pages/corporate.php">SmartSolutions Corporate</a></li>
            <li><a href="https://web.facebook.com/groups/1581265395842396" target="_blank">SmartSolutions Community</a></li>
        </ul>
    </div>
    <div class="footer-col footer-logo">
        <h4>Follow Us</h4>
        <img src="../image/logo.png" alt="Company Logo">
        <div class="social-media">
            <a href="https://web.facebook.com/smartsolutionscomputershop.ph" target="_blank">
                <img src="../image/facebook.png" alt="Facebook">
            </a>
            <a href="https://www.instagram.com/smartsolutions.ph/" target="_blank">
                <img src="../image/instagram.png" alt="Instagram">
            </a>
            <a href="https://www.tiktok.com/@smrtsolutioncomputershop" target="_blank">
                <img src="../image/tiktok.png" alt="Tiktok">
            </a>
            <a href="https://www.linkedin.com/in/smart-solutions-40b512339/" target="_blank">
                <img src="../image/linkedin.png" alt="LinkedIn">
            </a>
            <a href="https://www.youtube.com/@SmartSolutions-o4j/" target="_blank">
                <img src="../image/youtube.png" alt="YouTube">
            </a>
        </div>
    </div>
</footer>
    <div class="copyright">
        &copy; 2024 SmartSolutions. All rights reserved.
    </div>

    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById("dropdown-menu");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        window.onclick = function(event) {
            if (!event.target.matches('.login')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    dropdowns[i].style.display = "none";
                }
            }
        }
    </script>
    </script>
<script src="js/search.js"></script>
</body>
</html>

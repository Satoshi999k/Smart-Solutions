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
    <meta charset="UTF-8">
    <title>My Profile - SMARTSOLUTIONS</title>
    <style>
        .profile-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .profile-header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 30px;
        }
        .profile-pic-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #007BFF;
        }
        .profile-info-header h2 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .profile-info-header p {
            margin: 5px 0;
            color: #666;
        }
        .profile-edit-btn {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        .profile-edit-btn:hover {
            background-color: #0056b3;
        }
        .profile-content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
        }
        .profile-sidebar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: fit-content;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li {
            margin-bottom: 10px;
        }
        .sidebar-menu a {
            display: block;
            padding: 12px 15px;
            text-decoration: none;
            color: #333;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: #f0f0f0;
        }
        .profile-main {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007BFF;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-item {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .info-value {
            color: #333;
            font-size: 16px;
        }
        .orders-section {
            margin-top: 30px;
            animation: fadeInUp 0.6s ease-out;
        }
        .order-card {
            background: linear-gradient(135deg, #ffffff 0%, #f9f9f9 100%);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #007BFF;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .order-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .order-id {
            font-weight: bold;
            color: #007BFF;
        }
        .order-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .order-details {
            margin-top: 10px;
        }
        .order-details p {
            margin: 5px 0;
            color: #666;
        }
        .no-orders {
            text-align: center;
            padding: 40px;
            color: #999;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 2px dashed #ddd;
        }
        .order-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .order-item-row:last-child {
            border-bottom: none;
        }
        .order-item-details {
            flex: 1;
        }
        .order-item-price {
            text-align: right;
            font-weight: bold;
            color: #007BFF;
            min-width: 100px;
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
            <div class="search-icon"></div>
        </div>
        <a href="../pages/location.php"><div class="location">
            <img class="location" src="../image/location-icon.png" alt="location-icon">
        </a>
        </div>
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
                top: 20px;
                right: -10px;
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
            <img class="login" 
                 src="<?php echo $profile_picture; ?>" 
                 alt="profile-icon" 
                 style="border-radius: 50%; width: 40px; height: 40px; object-fit: cover;">
        </a>
        <div id="dropdown-menu" class="dropdown-content">
            <a href="profile.php">View Profile</a>
            <a href="edit-profile.php">Edit Profile</a>
            <a href="logout.php">Log Out</a>
        </div>  
        </div>
        <div class="login-text">
            <a href="profile.php"></a>
        </div>
    </div>
    </header>

    <div class="menu">
        <a href="../index.php">HOME</a>
        <a href="../pages/product.php">PRODUCTS</a>
        <a href="../products/desktop.php">DESKTOP</a>
        <a href="../products/laptop.php">LAPTOP</a>
        <a href="../pages/brands.php">BRANDS</a>
    </div>

    <div class="breadcrumb">
        <a href="../index.php">Home</a> >
        <a>My Profile</a>
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
                            <div style="margin-top: 20px;">
                            <?php foreach ($orders as $order): ?>
                                <?php 
                                    // Parse JSON data from order
                                    $order_details = isset($order['order_details']) ? json_decode($order['order_details'], true) : [];
                                    $customer_details = isset($order['customer_details']) ? json_decode($order['customer_details'], true) : [];
                                    $order_items = isset($order_details['items']) ? $order_details['items'] : [];
                                    
                                    // Get order info
                                    $order_id = $order['id'] ?? $order['order_id'] ?? 'N/A';
                                    $total_amount = $order['total_price'] ?? $order['total'] ?? 0;
                                    $status = $order['status'] ?? $order['order_status'] ?? 'Pending';
                                    
                                    // Get date
                                    $date_key = isset($order['order_date']) ? 'order_date' : (isset($order['created_at']) ? 'created_at' : 'date');
                                    $order_date = isset($order[$date_key]) ? $order[$date_key] : 'N/A';
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
                                            <?php echo htmlspecialchars($status); ?>
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
<script src="js/search.js"></script>
</body>
</html>

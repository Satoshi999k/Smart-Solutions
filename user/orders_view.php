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
    if (strpos($user['profile_picture'], '/') === 0) {
        $profile_picture = $user['profile_picture'];
    } else {
        $profile_picture = '/ITP122/' . $user['profile_picture'];
    }
}

// Fetch all user's orders 
$orders = [];
$orders_query = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 500";
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
    <title>All Orders - SMARTSOLUTIONS</title>
    <style>
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
        
        .orders-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .orders-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 20px;
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

        .orders-header h2 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            color: #333;
            letter-spacing: -0.5px;
        }

        .back-btn {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: white;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .order-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            padding: 28px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 5px solid #0062F6;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out both;
        }

        .order-card:nth-child(1) { animation-delay: 0.1s; }
        .order-card:nth-child(2) { animation-delay: 0.2s; }
        .order-card:nth-child(3) { animation-delay: 0.3s; }
        .order-card:nth-child(n+4) { animation-delay: 0.4s; }

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
            flex-wrap: wrap;
            gap: 15px;
        }

        .order-id {
            font-weight: 700;
            color: #0062F6;
            font-size: 18px;
            letter-spacing: 0.5px;
        }

        .order-status {
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 11px;
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
            line-height: 1.6;
        }

        .no-orders {
            text-align: center;
            padding: 80px 40px;
            color: #999;
            background: linear-gradient(135deg, #f5f9ff 0%, #f0f5ff 100%);
            border-radius: 12px;
            border: 2px dashed #d0d0d0;
            font-size: 18px;
            animation: fadeIn 0.6s ease-out;
        }

        .order-items-box {
            background: linear-gradient(135deg, #f9f9f9 0%, #f5f5f5 100%);
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            border: 1px solid #e8e8e8;
        }

        .order-item {
            padding: 12px 0;
            border-bottom: 1px solid #e8e8e8;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 15px;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-details {
            flex: 1;
            min-width: 200px;
        }

        .item-details strong {
            display: block;
            font-size: 15px;
            margin-bottom: 4px;
            color: #333;
            font-weight: 600;
        }

        .item-details small {
            color: #666;
            display: block;
            font-size: 13px;
            line-height: 1.5;
        }

        .item-price {
            font-weight: 700;
            color: #0062F6;
            min-width: 100px;
            text-align: right;
            white-space: nowrap;
            font-size: 16px;
        }

        .order-count {
            color: #999;
            font-size: 15px;
            margin-bottom: 0;
            margin-top: 8px;
            font-weight: 500;
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
            .orders-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .order-header {
                flex-direction: column;
            }

            .order-item {
                flex-direction: column;
            }

            .item-price {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="ssheader">
            <div class="logo">
                <a href="../index.php"><img src="../image/logo.png" alt="Logo"></a>
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
                <a href="../pages/track.php"><img class="track" src="../image/track-icon.png" alt="track-icon"></a>
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
                <a href="javascript:void(0)" onclick="toggleDropdown(event)">
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
                        <a href="orders_view.php" style="background-color: #f0f0f0;">All Orders</a>
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

    <div class="main-content">
        <div class="orders-container">
            <div class="orders-header">
                <div>
                    <h2>All Orders</h2>
                    <p class="order-count">Total Orders: <?php echo count($orders); ?></p>
                </div>
                <a href="profile.php" class="back-btn">← Back to Profile</a>
            </div>

            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): 
                    $order_details = isset($order['order_details']) ? json_decode($order['order_details'], true) : [];
                    $customer_details = isset($order['customer_details']) ? json_decode($order['customer_details'], true) : [];
                    $order_items = isset($order_details['items']) ? $order_details['items'] : [];
                    
                    $order_id = $order['id'] ?? $order['order_id'] ?? 'N/A';
                    $total_amount = $order['total_price'] ?? $order['total'] ?? 0;
                    $status = $order['status'] ?? 'processing'; // Default to processing if not set
                    
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
                            <?php echo htmlspecialchars(ucfirst($status)); ?>
                        </span>
                    </div>
                    <div class="order-details">
                        <p><strong>Total Amount:</strong> ₱<?php echo number_format($total_amount, 2); ?></p>
                        
                        <?php if (!empty($order_items)): ?>
                            <p style="margin-top: 15px; margin-bottom: 10px;"><strong>Items Ordered:</strong></p>
                            <div class="order-items-box">
                                <?php foreach ($order_items as $item): ?>
                                    <div class="order-item">
                                        <div class="item-details">
                                            <strong><?php echo htmlspecialchars($item['product_name'] ?? 'Product'); ?></strong>
                                            <small>Qty: <?php echo intval($item['quantity'] ?? 1); ?> × ₱<?php echo number_format($item['price'] ?? 0, 2); ?></small>
                                        </div>
                                        <span class="item-price">
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
            <?php else: ?>
                <div class="no-orders">
                    <p>📦 You haven't placed any orders yet.</p>
                    <a href="../product.php" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">Start Shopping</a>
                </div>
            <?php endif; ?>
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
                <a href="https://www.instagram.com/smartsolutionscomputershop.ph/" target="_blank">
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
</body>
</html>

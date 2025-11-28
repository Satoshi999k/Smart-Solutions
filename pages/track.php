<?php
// Start session to check if the user is logged in
session_start();

// Function to calculate total cart quantity
function getCartTotalQuantity() {
    if (!isset($_SESSION['cart'])) {
        return 0;
    }
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += isset($item['quantity']) ? $item['quantity'] : 1;
    }
    return $total;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

// Check if logged in
$profile_picture = "../image/login-icon.png";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Query to get user's profile picture
    $query = "SELECT profile_picture FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if (!empty($row['profile_picture'])) {
            if (strpos($row['profile_picture'], 'http') === 0) {
                $profile_picture = $row['profile_picture']; // Google/external URL
            } else {
                $profile_picture = "../" . $row['profile_picture']; // Local path with prefix
            }
        }
    }
    $stmt->close();
}

// Handle order tracking
$order_data = null;
$tracking_error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_number = trim($_POST['order_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if (!empty($order_number) && !empty($email)) {
        // Query to get order details
        $query = "SELECT * FROM orders WHERE id = ? OR order_id = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $order_number, $order_number);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $order_data = $result->fetch_assoc();
            // Optional: verify email matches
            $customer_details = json_decode($order_data['customer_details'], true);
            if ($customer_details['email'] !== $email && $order_data['email'] !== $email) {
                $tracking_error = "Email does not match this order.";
                $order_data = null;
            }
        } else {
            $tracking_error = "Order not found. Please check your order number.";
        }
        $stmt->close();
    } else {
        $tracking_error = "Please enter both order number and email.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
<link rel="stylesheet" href="../css/design.css" />
<link rel="stylesheet" href="../css/animations.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRACK YOUR ORDER - SMARTSOLUTIONS</title>
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

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Modern Dropdown Styling */
        .profile-dropdown { position: relative; display: inline-block; }
        .dropdown-content { display: none; position: absolute; top: 110%; right: 0; background: white; border-radius: 8px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); border: 1px solid #e0e0e0; min-width: 200px; z-index: 1000; }
        .dropdown-content a { display: flex; align-items: center; padding: 12px 16px; color: #333; font-size: 14px; font-weight: 500; text-decoration: none; transition: all 0.2s ease; border-left: 3px solid transparent; }
        .dropdown-content a:hover { background: #f5f5f5; color: #0062F6; border-left-color: #0062F6; }
        .profile-dropdown.active .dropdown-content { display: block; }

        .processor-section h2 {
            font-size: 48px;
            font-weight: 800;
            text-align: center;
            margin: 50px 0 20px;
            color: #1a1a1a;
            letter-spacing: -1.5px;
            animation: slideInDown 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .track-order-wrapper {
            max-width: 1000px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .track-order {
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            padding: 50px;
            border-radius: 16px;
            box-shadow: 0 15px 50px rgba(0, 98, 246, 0.15);
            animation: fadeInUp 0.7s ease-out 0.2s both;
        }

        .track-order h2 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 40px;
            letter-spacing: -0.5px;
        }

        .track-order form {
            display: grid;
            gap: 28px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: #0062F6;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input {
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: inherit;
            background: white;
        }

        .form-group input:focus {
            outline: none;
            border-color: #0062F6;
            box-shadow: 0 0 0 4px rgba(0, 98, 246, 0.1);
        }

        .track-button {
            padding: 16px 40px;
            background: linear-gradient(135deg, #0062F6 0%, #0052D4 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 98, 246, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 20px;
        }

        .track-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 98, 246, 0.4);
        }

        .track-button:active {
            transform: translateY(0);
        }

        /* Order Details Section */
        .order-details-section {
            margin-top: 50px;
        }

        .order-header {
            background: linear-gradient(135deg, #0062F6 0%, #0052D4 100%);
            padding: 30px;
            border-radius: 12px;
            color: white;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 98, 246, 0.2);
        }

        .order-header h3 {
            margin: 0 0 15px 0;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .order-header p {
            margin: 8px 0;
            font-size: 15px;
            opacity: 0.95;
        }

        .status-timeline {
            display: flex;
            justify-content: space-between;
            margin: 40px 0;
            position: relative;
        }

        .status-timeline::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #0062F6 0%, #0062F6 50%, #ddd 50%, #ddd 100%);
            z-index: 0;
        }

        .status-item {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .status-dot {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 3px solid #ddd;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: all 0.3s ease;
        }

        .status-item.completed .status-dot {
            background: linear-gradient(135deg, #0062F6 0%, #0052D4 100%);
            border-color: #0062F6;
            color: white;
        }

        .status-label {
            font-weight: 600;
            font-size: 13px;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .order-items {
            background: linear-gradient(135deg, #f5f9ff 0%, #f0f5ff 100%);
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            border-left: 5px solid #0062F6;
        }

        .order-items h4 {
            font-size: 18px;
            font-weight: 700;
            color: #0062F6;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-name {
            font-weight: 600;
            color: #333;
        }

        .item-qty {
            color: #666;
            font-size: 14px;
        }

        .item-total {
            font-weight: 700;
            color: #0062F6;
        }

        .error-message {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%);
            color: #dc2626;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #dc2626;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .error-message i {
            font-size: 24px;
        }

        @media (max-width: 768px) {
            .processor-section h2 {
                font-size: 32px;
                margin: 40px 0 20px;
            }

            .track-order {
                padding: 30px 20px;
            }

            .status-timeline {
                flex-wrap: wrap;
            }

            .status-timeline::before {
                display: none;
            }

            .status-item {
                flex: 0 0 25%;
                margin-bottom: 20px;
            }

            .order-items {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .processor-section h2 {
                font-size: 26px;
            }

            .track-order {
                padding: 20px;
            }

            .order-header {
                padding: 20px;
            }

            .status-item {
                flex: 0 0 50%;
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
        <a href="location.php">
            <div class="location">
                <img class="location" src="../image/location-icon.png" alt="location-icon">
            </div>
        </a>
        <div class="track">
            <a href="../pages/track.php"><img class="track" src="../image/track-icon.png" alt="track-icon">
        </a>
        </div>
        <a href="../cart.php">
            <div class="cart">
                <img class="cart" src="../image/cart-icon.png" alt="cart-icon" style="width: 35px; height: auto;">
                <span class="cart-counter">
                    <?php echo getCartTotalQuantity(); ?>
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
            <a href="javascript:void(0)" onclick="toggleDropdown(event)">
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
                    <a href="../user/profile.php">
                        <i class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 8px; display: inline-block;">person</i>
                        View Profile
                    </a>
                    <a href="../user/edit-profile.php">
                        <i class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 8px; display: inline-block;">edit</i>
                        Edit Profile
                    </a>
                    <a href="../user/logout.php">
                        <i class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 8px; display: inline-block;">logout</i>
                        Log Out
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="login-text">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="../user/profile.php"></a>
                <?php else: ?>
                    <a href="../user/register.php"><p>Login/<br>Sign In</p></a>
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
            <a href="../index.php"><span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px;">home</span>Home</a> > <span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px; color: #0062F6;">local_shipping</span><a>Track your Order</a>
    </div>

    <div class="processor-section">
    <h2>TRACK YOUR ORDER</h2>
    </div>
    
    <div class="track-order-wrapper">
        <div class="track-order">
            <h2>Enter Your Order Details</h2>
            
            <?php if ($tracking_error): ?>
                <div class="error-message">
                    <i class="material-icons">error_outline</i>
                    <span><?php echo htmlspecialchars($tracking_error); ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="order-number"><i class="material-icons" style="font-size: 16px; vertical-align: middle;">receipt</i>Order Number</label>
                    <input type="text" id="order-number" name="order_number" placeholder="e.g., 12345" required>
                </div>

                <div class="form-group">
                    <label for="email"><i class="material-icons" style="font-size: 16px; vertical-align: middle;">email</i>Email Address</label>
                    <input type="email" id="email" name="email" placeholder="your@email.com" required>
                </div>

                <button type="submit" class="track-button">
                    <i class="material-icons" style="font-size: 18px;">search</i>
                    Track Order
                </button>
            </form>

            <?php if ($order_data): ?>
                <div class="order-details-section">
                    <div class="order-header">
                        <h3>
                            <i class="material-icons">local_shipping</i>
                            Order #<?php echo htmlspecialchars($order_data['id'] ?? $order_data['order_id'] ?? 'N/A'); ?>
                        </h3>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($order_data['status'] ?? 'Processing')); ?></p>
                        <p><strong>Order Date:</strong> <?php echo date('F d, Y', strtotime($order_data['order_date'] ?? $order_data['created_at'])); ?></p>
                        <p><strong>Total Amount:</strong> ₱<?php echo number_format($order_data['total_price'] ?? 0, 2); ?></p>
                    </div>

                    <!-- Timeline Status -->
                    <div class="status-timeline">
                        <div class="status-item <?php echo in_array($order_data['status'] ?? '', ['processing', 'shipped', 'delivered']) ? 'completed' : ''; ?>">
                            <div class="status-dot"><i class="material-icons">check</i></div>
                            <div class="status-label">Order Confirmed</div>
                        </div>
                        <div class="status-item <?php echo in_array($order_data['status'] ?? '', ['shipped', 'delivered']) ? 'completed' : ''; ?>">
                            <div class="status-dot"><i class="material-icons">local_shipping</i></div>
                            <div class="status-label">Shipped</div>
                        </div>
                        <div class="status-item <?php echo ($order_data['status'] ?? '') === 'delivered' ? 'completed' : ''; ?>">
                            <div class="status-dot"><i class="material-icons">check_circle</i></div>
                            <div class="status-label">Delivered</div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <?php 
                    $order_details = json_decode($order_data['order_details'] ?? '{}', true);
                    $order_items = $order_details['items'] ?? [];
                    
                    if (!empty($order_items)): 
                    ?>
                    <div class="order-items">
                        <h4>
                            <i class="material-icons">shopping_cart</i>
                            Order Items
                        </h4>
                        <?php foreach ($order_items as $item): ?>
                            <div class="item-row">
                                <div>
                                    <div class="item-name"><?php echo htmlspecialchars($item['product_name'] ?? 'Product'); ?></div>
                                    <div class="item-qty">Qty: <?php echo intval($item['quantity'] ?? 1); ?> × ₱<?php echo number_format($item['price'] ?? 0, 2); ?></div>
                                </div>
                                <div class="item-total">₱<?php echo number_format($item['subtotal'] ?? 0, 2); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Delivery Address -->
                    <div class="order-items">
                        <h4>
                            <i class="material-icons">location_on</i>
                            Delivery Address
                        </h4>
                        <p style="margin: 0; color: #333; line-height: 1.8;">
                            <?php 
                            $customer_details = json_decode($order_data['customer_details'] ?? '{}', true);
                            if (!empty($customer_details)) {
                                $address = trim(($customer_details['address'] ?? '') . ' ' . ($customer_details['apartment'] ?? ''));
                                echo htmlspecialchars($address) . '<br>';
                                echo htmlspecialchars(($customer_details['city'] ?? '') . ', ' . ($customer_details['region'] ?? '')) . '<br>';
                                echo htmlspecialchars($customer_details['country'] ?? 'Philippines');
                            } else {
                                echo 'Address information not available';
                            }
                            ?>
                        </p>
                    </div>
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
    // Modern dropdown toggle with smooth transitions
    function toggleDropdown(event) {
        event.preventDefault();
        event.stopPropagation();
        const profileDropdown = document.querySelector('.profile-dropdown');
        profileDropdown.classList.toggle('active');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const profileDropdown = document.querySelector('.profile-dropdown');
        if (profileDropdown && !profileDropdown.contains(event.target)) {
            profileDropdown.classList.remove('active');
        }
    });

    // Close dropdown on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const profileDropdown = document.querySelector('.profile-dropdown');
            if (profileDropdown) {
                profileDropdown.classList.remove('active');
            }
        }
    });
</script>
<script src="js/search.js"></script>
<script src="../js/jquery-animations.js"></script>
<script src="../js/header-animation.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="image/x-icon">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/design.css" />
<link rel="stylesheet" href="../css/animations.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
<title>RETURN & REFUNDS - SMART SOLUTIONS</title>
</head>
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

// Database connection (replace with your credentials)
$conn = new mysqli("localhost", "root", "", "smartsolutions");

// Check if logged in
$profile_picture = "../image/login-icon.png"; // Default login icon
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
            // Check if it's a full URL (Google image, etc.) or relative path
            if (strpos($row['profile_picture'], 'http') === 0) {
                $profile_picture = $row['profile_picture']; // Use URL as-is
            } else {
                // Add ../ prefix for relative paths since we're in pages/ subfolder
                $profile_picture = '../' . $row['profile_picture']; // Use user's profile picture
            }
        }
    }
    $stmt->close();
}
$conn->close();
// Handle add to cart
if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $product_id = $_GET['product_id'];
    $product_name = $_GET['product_name'];
    $product_price = $_GET['product_price'];

    // Add product to session cart
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $cart[] = [
        'id' => $product_id,
        'name' => $product_name,
        'price' => $product_price
    ];
    $_SESSION['cart'] = $cart;

    // Trigger JavaScript alert
    echo "<script>alert('Item added to cart!');</script>";
}
?>
<body class="brands">
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
            <a href="track.php"><img class="track" src="../image/track-icon.png" alt="track-icon">
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

            .profile-dropdown {
                position: relative;
                display: inline-block;
            }

            .dropdown-content {
                display: none;
                position: absolute;
                top: 110%;
                right: 0;
                background: white;
                border-radius: 8px;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
                border: 1px solid #e0e0e0;
                min-width: 200px;
                z-index: 1000;
            }

            .dropdown-content a {
                display: flex;
                align-items: center;
                padding: 12px 16px;
                color: #333;
                font-size: 14px;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.2s ease;
                border-left: 3px solid transparent;
            }

            .dropdown-content a:hover {
                background: #f5f5f5;
                color: #0062F6;
                border-left-color: #0062F6;
            }

            .profile-dropdown.active .dropdown-content {
                display: block;
            }

            .material-icons {
                font-family: 'Material Icons';
                font-weight: normal;
                font-style: normal;
                font-size: 24px;
                display: inline-block;
                line-height: 1;
                text-transform: none;
                letter-spacing: normal;
                word-wrap: normal;
                white-space: nowrap;
                direction: ltr;
            }
        </style>
        <style>
            .breadcrumb1 { padding: 16px 24px; font-size: 14px; color: #555; background: transparent; }
            .breadcrumb1 a { color: #0062F6; text-decoration: none; font-weight: 500; transition: color 0.3s ease; }
            .breadcrumb1 a:hover { color: #0052D4; }

            .processor-section1 {
                background: transparent;
                color: #333;
                text-align: center;
                padding: 20px 40px;
                margin: 20px;
                border-radius: 12px;
            }

            .processor-section1 h2 {
                font-size: 32px;
                font-weight: 700;
                margin: 0;
                letter-spacing: 1px;
                color: #0062F6;
            }

            .processor-sectionret {
                max-width: 1000px;
                margin: 40px auto;
                padding: 40px;
                background: linear-gradient(135deg, #f8f9ff 0%, #f0f5ff 100%);
                border-radius: 12px;
                border-left: 6px solid #0062F6;
                box-shadow: 0 4px 20px rgba(0, 98, 246, 0.08);
                transition: all 0.3s ease;
            }

            .processor-sectionret:hover {
                box-shadow: 0 8px 30px rgba(0, 98, 246, 0.15);
                transform: translateY(-2px);
            }

            .processor-sectionret h3 {
                color: #0062F6;
                font-size: 20px;
                font-weight: 600;
                margin: 24px 0 12px 0;
                transition: all 0.3s ease;
            }

            .processor-sectionret h3:hover {
                color: #004FCC;
                transform: translateX(4px);
            }

            .processor-sectionret p {
                color: #333;
                font-size: 15px;
                line-height: 1.8;
                font-weight: 500;
                margin: 12px 0;
                transition: all 0.3s ease;
            }

            .processor-sectionret p:hover {
                color: #0062F6;
                transform: translateX(6px);
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

    <div class="menu">
            <a href="../index.php">HOME</a>
            <a href="product.php">PRODUCTS</a>
            <a href="../products/desktop.php">DESKTOP</a>
            <a href="../products/laptop.php">LAPTOP</a>
            <a href="brands.php">BRANDS</a>
    </div>

    <div class="breadcrumb1">
            <a href="../index.php"><span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px;">home</span>Home</a> > <span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px; color: #0062F6;">assignment_return</span><a>Return and Refunds</a>
    </div>

    <div class="processor-section1">
    <h2>RETURN AND REFUNDS</h2>
    </div>
    <div class="processor-sectionret">

    <h3>How do I request a refund?</h3>
    <br>
    <p>To request refund, send an email to smartsolutionscomputershop@gmail.com with the following subject and details</p>
    <br>
    <p>SUBJECT              :              Refund Request</p>
    <br>
    <p>BODY                    :              Order Number & Refund Amount</p>
    <br>
    <p>An Easypc Online Sales Representative will call you within 24 hours upon receipt of the email.</p>
    <br>
    <h3>How to return an item?</h3>
    <br>
    <p>Before you return an item, please coordinate first with us thru our Smart Solutions Computer Shop page or email us  at smart solutionscomputershop@gmail.com and we will provide instruction on how to process item return.</p>
    <br>
    </div>

     <footer class="footer">
        <div class="footer-col">
        <h3>Customer Service</h3>
        <ul>
            <li><a href="paymentfaq.php">Payment FAQs</a></li>
            <li><a href="ret&ref.php">Return and Refunds</a></li>
        </ul>
    </div>
    <div class="footer-col">
        <h3>Company</h3>
        <ul>
            <li><a href="about_us.php">About Us</a></li>
            <li><a href="contact_us.php">Contact Us</a></li>
        </ul>
    </div>
    <div class="footer-col">
        <h3>Links</h3>
        <ul>
            <li><a href="corporate.php">SmartSolutions Corporate</a></li>
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
<script src="../js/search.js"></script>
<script src="../js/jquery-animations.js"></script>
<script src="../js/header-animation.js"></script>
</body>
</html>
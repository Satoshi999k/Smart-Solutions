<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
<link rel="stylesheet" href="../css/design.css" />
<link rel="stylesheet" href="../css/animations.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
<meta charset="UTF-8">
    <title>CORPORATE- SMARTSOLUTIONS</title>
</head>
<body>
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
            // Check if it's a full URL (Google image, etc.) or relative path
            if (strpos($row['profile_picture'], 'http') === 0) {
                $profile_picture = $row['profile_picture']; // Use URL as-is
            } else {
                // Add ../ prefix for relative paths since we're in pages/ subfolder
                $profile_picture = "../" . $row['profile_picture'];
            }
        }
    }
    $stmt->close();
}
$conn->close();
?>
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

            .breadcrumb { padding: 16px 24px; font-size: 14px; color: #555; background: transparent; }
            .breadcrumb a { color: #0062F6; text-decoration: none; font-weight: 500; transition: color 0.3s ease; }
            .breadcrumb a:hover { color: #0052D4; }

            .corporate-section {
                max-width: 1000px;
                margin: 40px auto;
                padding: 40px;
                background: linear-gradient(135deg, #f8f9ff 0%, #f0f5ff 100%);
                border-radius: 12px;
                border-left: 6px solid #0062F6;
                box-shadow: 0 4px 20px rgba(0, 98, 246, 0.08);
                transition: all 0.3s ease;
            }

            .corporate-section:hover {
                box-shadow: 0 8px 30px rgba(0, 98, 246, 0.15);
                transform: translateY(-2px);
            }

            .corporate-section h1 {
                color: #0062F6;
                font-size: 36px;
                font-weight: 700;
                margin: 0 0 20px 0;
                letter-spacing: 1px;
                transition: all 0.3s ease;
            }

            .corporate-section h1:hover {
                color: #004FCC;
                transform: translateX(4px);
            }

            .corporate-section p {
                color: #333;
                font-size: 15px;
                line-height: 1.8;
                font-weight: 500;
                margin: 16px 0;
                transition: all 0.3s ease;
            }

            .corporate-section p:hover {
                color: #0062F6;
                transform: translateX(6px);
            }

            .corporate-section a {
                color: #0062F6;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s ease;
                border-bottom: 2px solid transparent;
            }

            .corporate-section a:hover {
                color: #004FCC;
                border-bottom-color: #0062F6;
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
                <a href="profile.php"></a>
            <?php else: ?>
                <a href="../user/register.php"><p>Login/<br>Sign In</p></a>
            <?php endif; ?>
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
            <a href="../index.php"><span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px;">home</span>Home</a> > <span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px; color: #0062F6;">business</span><a>Corporate</a>
    </div>

    <div class="corporate-section">
    <h1>Corporate</h1>
    <p>The grind doesn't stop for Essential Businesses, and neither do we. If you need someone to supply technologies for your company, we are here to help!</p>
    <p>For corporate clients, please send your inquiries to 
        <a href="mailto:sales@smartsolutionscomputershop@gmail.com">smartsolutionscomputershop@gmail.com</a>
    </p>
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
    // Add JavaScript to toggle dropdown visibility
    function toggleDropdown(event) {
        event.preventDefault();
        event.stopPropagation();
        const profileDropdown = document.querySelector('.profile-dropdown');
        profileDropdown.classList.toggle('active');
    }

    document.addEventListener('click', function(event) {
        const profileDropdown = document.querySelector('.profile-dropdown');
        if (!profileDropdown.contains(event.target)) {
            profileDropdown.classList.remove('active');
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelector('.profile-dropdown').classList.remove('active');
        }
    });
</script>
<script src="js/search.js"></script>
<script src="../js/jquery-animations.js"></script>
<script src="../js/header-animation.js"></script>
</body>
</html>

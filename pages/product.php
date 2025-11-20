<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/design.css" />
<link rel="stylesheet" href="../css/animations.css" />
<title>PRODUCTS - SMARTSOLUTIONS</title>
</head>
<body>
<?php
// Start session to check if the user is logged in
session_start();

// Initialize cart from database
require_once '../includes/init_cart.php';

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
            // Add ../ prefix to profile picture path since we're in pages/ subfolder
            $profile_picture = "../" . $row['profile_picture']; 
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
            <div class="search-icon">
                <img src="../image/search-icon.png" alt="Search Icon">
            </div>
        </div>
        <a href="location.php">
            <div class="location">
                <img class="location" src="../image/location-icon.png" alt="location-icon">
            </div>
        </a>
        <div class="track">
            <a href="track.php"><img class="track" src="../image/track-icon.png" alt="track-icon"></a>
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
                    <a href="../user/register.php"><p>Login/<br>Sign In</p></a>
                <?php endif; ?>
            </div>
    </div>
    </header>
    <div class="menu">
            <a href="../index.php">HOME</a>
            <a href="../pages/product.php" style="font-weight: bold;">PRODUCTS</a>
            <a href="../products/desktop.php">DESKTOP</a>
            <a href="../products/laptop.php">LAPTOP</a>
            <a href="../pages/brands.php">BRANDS</a>
    </div>

    <div class="breadcrumb">
            <a href="../index.php">Home</a> > 
            <a>Product Category</a>
    </div>
    <div class="product-category">
        <h1>PRODUCT CATEGORY</h1>
        <div class="product-grid">
      
            <div class="product-item">
                <a href="../products/processor.php">
                    <img src="../image/processor.png" alt="Processor">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/motherboard.php">
                    <img src="../image/motherboard.png" alt="Motherboard">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/graphicscard.php">
                    <img src="../image/graphicscard.png" alt="Graphics Card">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/memory.php">
                    <img src="../image/memory.png" alt="Memory">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/ssd.php">
                    <img src="../image/ssd.png" alt="Solid State Drive">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/powersupply.php">
                    <img src="../image/powersupply.png" alt="Power Supply">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/pccase.php">
                    <img src="../image/pccase.png" alt="PC Case">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/laptop.php">
                    <img src="../image/laptop.png" alt="Laptop">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/monitor.php">
                    <img src="../image/monitor.png" alt="Monitor">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/keyboard.php">
                    <img src="../image/keyboard.png" alt="Keyboard">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/mouse.php">
                    <img src="../image/mouse.png" alt="Mouse">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/headset.php">
                    <img src="../image/headset.png" alt="Headset">
                </a>
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
        // Add JavaScript to toggle dropdown
        function toggleDropdown() {
            var profileDropdown = document.querySelector(".profile-dropdown");
            profileDropdown.classList.toggle("active");
        }

        // Close the dropdown if the user clicks anywhere outside of it
        window.onclick = function(event) {
            if (!event.target.matches('.profile-dropdown, .profile-dropdown *')) {
                var dropdowns = document.querySelectorAll('.dropdown-content');
                for (var i = 0; i < dropdowns.length; i++) {
                    dropdowns[i].style.display = 'none';
                }
            }
        };
</script>
<script src="../js/search.js"></script>
<script src="../js/header-animation.js"></script>
</body>
</html>
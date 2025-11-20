<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="image/smartsolutionslogo.jpg" type="image/x-icon">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="design.css" />
<link rel="stylesheet" href="animations.css" />
<title>SMART SOLUTIONS COMPUTER SHOP</title>
</head>
<body>
<?php
// Start session to check if the user is logged in
session_start();

// Initialize cart from database
require_once 'init_cart.php';

// Database connection (replace with your credentials)
$conn = new mysqli("localhost", "root", "", "smartsolutions");

// Check if logged in
$profile_picture = "image/login-icon.png"; // Default login icon
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
            $profile_picture = $row['profile_picture']; 
        }
    }
    $stmt->close();
}
$conn->close();
?>
<header>
    <div class="ssheader">
        <div class="logo">
            <img src="image/logo.png" alt="Smart Solutions Logo">
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search">
            <div class="search-icon">
                <img src="image/search-icon.png" alt="Search Icon">
            </div>
        </div>
        <a href="location.html">
            <div class="location">
                <img class="location" src="image/location-icon.png" alt="location-icon">
            </div>
        </a>
        <div class="track">
            <a href="track.html"><img class="track" src="image/track-icon.png" alt="track-icon"></a>
        </div>
        <a href="cart.php">
            <div class="cart">
                <img class="cart" src="image/cart-icon.png" alt="cart-icon" style="width: 35px; height: auto;">
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
        <div class="login profile-dropdown" style="position: relative; display: inline-block;">
            <a href="javascript:void(0)" onclick="toggleDropdown()">
                <!-- Check if user is logged in, if yes show profile picture, else show login icon -->
                <img class="login" 
                     src="<?php echo isset($_SESSION['user_id']) ? $profile_picture : 'image/login-icon.png'; ?>" 
                     alt="login-icon" 
                     style="border-radius: <?php echo isset($_SESSION['user_id']) ? '50%' : '0'; ?>; 
                            width: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>; 
                            height: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>;">
            </a>
            <div id="dropdown-menu" class="dropdown-content" style="display: none; position: absolute; background-color: #f9f9f9; min-width: 160px; box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2); z-index: 1; border-radius: 5px;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    
                    <a href="logout.php" style="color: black; padding: 12px 16px; text-decoration: none; display: block;">Log Out</a>
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
    <div class="menu">
            <a href="index.php">HOME</a>
            <a href="product.php">PRODUCTS</a>
            <a href="products/desktop.php"style="font-weight: bold;">DESKTOP</a>
            <a href="products/laptop.php">LAPTOP</a>
            <a href="brands.php">BRANDS</a>
    </div>

    <div class="breadcrumb">
            <a href="index.html">Home</a> >
            <a>Processor</a>
    </div>

    <div class="processor-section">
    <h2>DESKTOP</h2>
    </div>

    <div class="prebuilds-section">
    <h2>Prebuilds</h2>
    <div class="prebuilds-container">
        
        <div class="prebuilt-card">
            <img src="image/Core_i7.png" alt="Intel Core i7-12700 PC">
            <h3>Core i7 12700 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W</h3>
            <p class="desktop-price">₱20,950.00</p>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'thankyou.html' : 'register.php'; ?>">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <div class="prebuilt-card">
            <img src="image/Core_i3.png" alt="Intel Core i3-12100 PC">
            <h3>Core i3 12100 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W</h3>
            <p class="desktop-price">₱14,795.00</p>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'thankyou.html' : 'register.php'; ?>">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <div class="prebuilt-card">
            <img src="image/Core_i5.png" alt="Intel Core i7-12700 PC">
            <h3>Core i7 12700 / H610 / 16GB DDR4 / 500GB SSD / 550W Power Supply/ PC Case M-ATX</h3>
            <p class="desktop-price">₱25,195.00</p>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'thankyou.html' : 'register.php'; ?>">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <div class="prebuilt-card">
            <img src="image/Ryzen_7.jpg" alt="Ryzen 7 5700G PC">
            <h3>Ryzen 7 5700G / B450M / 16GB DDR4 / 512GB SSD / 550W Power Supply / PC Case M-ATX</h3>
            <p class="desktop-price">₱21,250.00</p>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'thankyou.html' : 'register.php'; ?>">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>
        </div>
        </div>

    <div class="section-div">
    <div class="custom-section">
    <h2>Top Custom Builds</h2>
    <div class="custom-container">
        
        <div class="prebuilt-card">
            <img src="image/i5.png" alt="Intel Core i7-12700 PC">
            <h3>Stratus<br>Intel i5 12th gen | MSI H610 | Kingston 8gb Memory | 500gb | 700w</h3>
            <p class="desktop-price">₱20,950.00</p>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'thankyou.html' : 'register.php'; ?>">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <!-- Second Prebuilt PC Card -->
        <div class="custom-card">
            <img src="image/ryzn7.png" alt="Intel Core i3-12100 PC">
            <h3>Cirrus<br>AMD Ryzen 7 5700x | MSI B550 | Zotac Rtx-4060 | Team 16gb Memory | Kingston NVMe 2TB</h3>
            <p class="desktop-price">₱14,795.00</p>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'thankyou.html' : 'register.php'; ?>">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <div class="custom-card">
            <img src="image/ryzn5.png" alt="Intel Core i7-12700 PC">
            <h3>Cirrostratus<br>AMD Ryzen 5 5600G | Gigabyte B550 | Gigabyte Rx-6600 | Team 16gb Memory</h3>
            <p class="desktop-price">₱25,195.00</p>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'thankyou.html' : 'register.php'; ?>">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <div class="custom-card">
            <img src="image/ryzen5.png" alt="Ryzen 7 5700G PC">
            <h3>Cumulus<br>AMD Ryzen 5 Pro 4650G | Asrock A320 | Team Elite Plus 8gb Memory | Team 240gb</h3>
            <p class="desktop-price">₱21,250.00</p>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'thankyou.html' : 'register.php'; ?>">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>
        </div>
        </div>

        <div class="features2">
        <img class="features2" src="image/smartbuildbanner.png" alt="features">
        </div>
        
        <footer class="footer">
        <div class="footer-col">
        <h3>Customer Service</h3>
        <ul>
            <li><a href="paymentfaq.html">Payment FAQs</a></li>
            <li><a href="ret&ref.html">Return and Refunds</a></li>
        </ul>
    </div>
    <div class="footer-col">
        <h3>Company</h3>
        <ul>
            <li><a href="about_us.html">About Us</a></li>
            <li><a href="contact_us.html">Contact Us</a></li>
        </ul>
    </div>
    <div class="footer-col">
        <h3>Links</h3>
        <ul>
            <li><a href="corporate.html">SmartSolutions Corporate</a></li>
            <li><a href="https://web.facebook.com/groups/1581265395842396" target="_blank">SmartSolutions Community</a></li>
        </ul>
    </div>
    <div class="footer-col footer-logo">
        <h4>Follow Us</h4>
        <img src="image/logo.png" alt="Company Logo">
        <div class="social-media">
            <a href="https://web.facebook.com/smartsolutionscomputershop.ph" target="_blank">
                <img src="image/facebook.png" alt="Facebook">
            </a>
            <a href="https://www.instagram.com/smartsolutions.ph/" target="_blank">
                <img src="image/instagram.png" alt="Instagram">
            </a>
            <a href="https://www.tiktok.com/@smrtsolutioncomputershop" target="_blank">
                <img src="image/tiktok.png" alt="Tiktok">
            </a>
            <a href="https://www.linkedin.com/in/smart-solutions-40b512339/" target="_blank">
                <img src="image/linkedin.png" alt="LinkedIn">
            </a>
            <a href="https://www.youtube.com/@SmartSolutions-o4j/" target="_blank">
                <img src="image/youtube.png" alt="YouTube">
            </a>
        </div>
    </div>
</footer>
<div class="copyright">
    &copy; 2024 SmartSolutions. All rights reserved.
</div>
<script>
    // Add JavaScript to toggle dropdown visibility
    function toggleDropdown() {
        var dropdownMenu = document.getElementById("dropdown-menu");
        // Toggle the visibility of the dropdown menu
        dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    }

    // Close the dropdown if the user clicks anywhere outside of it
    window.onclick = function(event) {
        var dropdownMenu = document.getElementById("dropdown-menu");
        if (!event.target.matches('.profile-dropdown, .profile-dropdown *')) {
            dropdownMenu.style.display = 'none';  // Hide the dropdown menu if clicked outside
        }
    };
</script>
<script src="search.js"></script>
<script src="ajax-cart.js"></script>
</body>
</html>

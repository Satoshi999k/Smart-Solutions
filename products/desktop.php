<?php
// Start session to check if the user is logged in
session_start();

// Initialize cart from database
require_once 'init_cart.php';

// Database connection (replace with your credentials)
$conn = new mysqli("localhost", "root", "", "smartsolutions");

// Check if logged in
$profile_picture = "/ITP122/image/login-icon.png"; // Default login icon
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
            $profile_picture = "/ITP122/" . $row['profile_picture']; 
        }
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="/ITP122/image/smartsolutionslogo.jpg" type="/ITP122/image/x-icon">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="/ITP122/css/design.css" />
<link rel="stylesheet" href="/ITP122/css/animations.css" />
<meta charset="UTF-8">
</head>
<body>
<header>
    <div class="ssheader">
        <div class="logo">
            <img src="/ITP122/image/logo.png" alt="Smart Solutions Logo">
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search">
            <div class="search-icon">
                <img src="/ITP122/image/search-icon.png" alt="Search Icon">
            </div>
        </div>
        <a href="location.html">
            <div class="location">
                <img class="location" src="/ITP122/image/location-icon.png" alt="location-icon">
            </div>
        </a>
        <div class="track">
            <a href="track.html"><img class="track" src="/ITP122/image/track-icon.png" alt="track-icon"></a>
        </div>
        <a href="../cart.php">
            <div class="cart">
                <img class="cart" src="/ITP122/image/cart-icon.png" alt="cart-icon" style="width: 35px; height: auto;">
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
                    src="<?php echo isset($_SESSION['user_id']) ? $profile_picture : 'image/login-icon.png'; ?>" 
                    alt="login-icon" 
                    style="border-radius: <?php echo isset($_SESSION['user_id']) ? '50%' : '0'; ?>; 
                            width: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>; 
                            height: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>;">
            </a>
            <div id="dropdown-menu" class="dropdown-content">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="../user/profile.php">View Profile</a>
                    <a href="../user/edit-profile.php">Edit Profile</a>
                    <a href="../user/logout.php">Log Out</a>
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
            <a href="../pages/product.php">PRODUCTS</a>
            <a href="desktop.php"style="font-weight: bold;">DESKTOP</a>
            <a href="laptop.php">LAPTOP</a>
            <a href="../pages/brands.php">BRANDS</a>
    </div>

    <div class="breadcrumb">
            <a href="../index.php">Home</a> >
            <a>Desktop</a>
    </div>

    <div class="processor-section">
    <h2>DESKTOP</h2>
    </div>

    <div class="prebuilds-section">
    <h2>Prebuilds</h2>
    <div class="prebuilds-container">
        
        <div class="prebuilt-card">
            <img src="/ITP122/image/Core_i7.png" alt="Intel Core i7-12700 PC">
            <h3>Core i7 12700 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W</h3>
            <p class="desktop-price">₱20,950.00</p>
            <a href="#" class="buy-now-btn" data-id="prebuilt-1" data-name="Core i7 12700 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W" data-price="20950.00" data-image="/ITP122/image/Core_i7.png">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <div class="prebuilt-card">
            <img src="/ITP122/image/Core_i3.png" alt="Intel Core i3-12100 PC">
            <h3>Core i3 12100 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W</h3>
            <p class="desktop-price">₱14,795.00</p>
            <a href="#" class="buy-now-btn" data-id="prebuilt-2" data-name="Core i3 12100 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W" data-price="14795.00" data-image="/ITP122/image/Core_i3.png">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <div class="prebuilt-card">
            <img src="/ITP122/image/Core_i5.png" alt="Intel Core i7-12700 PC">
            <h3>Core i7 12700 / H610 / 16GB DDR4 / 500GB SSD / 550W Power Supply/ PC Case M-ATX</h3>
            <p class="desktop-price">₱25,195.00</p>
            <a href="#" class="buy-now-btn" data-id="prebuilt-3" data-name="Core i7 12700 / H610 / 16GB DDR4 / 500GB SSD / 550W Power Supply/ PC Case M-ATX" data-price="25195.00" data-image="/ITP122/image/Core_i5.png">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <div class="prebuilt-card">
            <img src="/ITP122/image/Ryzen_7.jpg" alt="Ryzen 7 5700G PC">
            <h3>Ryzen 7 5700G / B450M / 16GB DDR4 / 512GB SSD / 550W Power Supply / PC Case M-ATX</h3>
            <p class="desktop-price">₱21,250.00</p>
            <a href="#" class="buy-now-btn" data-id="prebuilt-4" data-name="Ryzen 7 5700G / B450M / 16GB DDR4 / 512GB SSD / 550W Power Supply / PC Case M-ATX" data-price="21250.00" data-image="/ITP122/image/Ryzen_7.jpg">
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
            <img src="/ITP122/image/i5.png" alt="Intel Core i7-12700 PC">
            <h3>Stratus<br>Intel i5 12th gen | MSI H610 | Kingston 8gb Memory | 500gb | 700w</h3>
            <p class="desktop-price">₱20,950.00</p>
            <a href="#" class="buy-now-btn" data-id="prebuilt-5" data-name="Stratus - Intel i5 12th gen | MSI H610 | Kingston 8gb Memory | 500gb | 700w" data-price="20950.00" data-image="/ITP122/image/i5.png">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <!-- Second Prebuilt PC Card -->
        <div class="custom-card">
            <img src="/ITP122/image/ryzn7.png" alt="Intel Core i3-12100 PC">
            <h3>Cirrus<br>AMD Ryzen 7 5700x | MSI B550 | Zotac Rtx-4060 | Team 16gb Memory | Kingston NVMe 2TB</h3>
            <p class="desktop-price">₱14,795.00</p>
            <a href="#" class="buy-now-btn" data-id="prebuilt-6" data-name="Cirrus - AMD Ryzen 7 5700x | MSI B550 | Zotac Rtx-4060 | Team 16gb Memory | Kingston NVMe 2TB" data-price="14795.00" data-image="/ITP122/image/ryzn7.png">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <div class="custom-card">
            <img src="/ITP122/image/ryzn5.png" alt="Intel Core i7-12700 PC">
            <h3>Cirrostratus<br>AMD Ryzen 5 5600G | Gigabyte B550 | Gigabyte Rx-6600 | Team 16gb Memory</h3>
            <p class="desktop-price">₱25,195.00</p>
            <a href="#" class="buy-now-btn" data-id="prebuilt-7" data-name="Cirrostratus - AMD Ryzen 5 5600G | Gigabyte B550 | Gigabyte Rx-6600 | Team 16gb Memory" data-price="25195.00" data-image="/ITP122/image/ryzn5.png">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <div class="custom-card">
            <img src="/ITP122/image/ryzen5.png" alt="Ryzen 7 5700G PC">
            <h3>Cumulus<br>AMD Ryzen 5 Pro 4650G | Asrock A320 | Team Elite Plus 8gb Memory | Team 240gb</h3>
            <p class="desktop-price">₱21,250.00</p>
            <a href="#" class="buy-now-btn" data-id="prebuilt-8" data-name="Cumulus - AMD Ryzen 5 Pro 4650G | Asrock A320 | Team Elite Plus 8gb Memory | Team 240gb" data-price="21250.00" data-image="/ITP122/image/ryzen5.png">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>
        </div>
        </div>

        <div class="features2">
        <img class="features2" src="/ITP122/image/smartbuildbanner.png" alt="features">
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
        <img src="/ITP122/image/logo.png" alt="Company Logo">
        <div class="social-media">
            <a href="https://web.facebook.com/smartsolutionscomputershop.ph" target="_blank">
                <img src="/ITP122/image/facebook.png" alt="Facebook">
            </a>
            <a href="https://www.instagram.com/smartsolutions.ph/" target="_blank">
                <img src="/ITP122/image/instagram.png" alt="Instagram">
            </a>
            <a href="https://www.tiktok.com/@smrtsolutioncomputershop" target="_blank">
                <img src="/ITP122/image/tiktok.png" alt="Tiktok">
            </a>
            <a href="https://www.linkedin.com/in/smart-solutions-40b512339/" target="_blank">
                <img src="/ITP122/image/linkedin.png" alt="LinkedIn">
            </a>
            <a href="https://www.youtube.com/@SmartSolutions-o4j/" target="_blank">
                <img src="/ITP122/image/youtube.png" alt="YouTube">
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
    
    // Handle buy now button
    document.addEventListener('click', function(e) {
        let target = e.target.closest('a.buy-now-btn');
        if (!target) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        const productId = target.getAttribute('data-id');
        const productName = target.getAttribute('data-name');
        const productPrice = target.getAttribute('data-price');
        const productImage = target.getAttribute('data-image');
        
        if (!productId) return;
        
        // Send product to session via fetch
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('product_name', productName);
        formData.append('product_price', productPrice);
        formData.append('product_image', productImage);
        formData.append('quantity', 1);
        formData.append('buy_now', '1');
        
        fetch('../set_buynow_product.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = '../pages/checkout.php';
            }
        })
        .catch(err => console.log(err));
    });
</script>
<script src="js/search.js"></script>
<script src="../js/jquery-animations.js"></script>
<script src="../js/header-animation.js"></script>
</body>
</html>
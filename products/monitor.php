<?php
// Start session to check if the user is logged in
session_start();

// Initialize cart from database
require_once '../includes/init_cart.php';

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

// Check if logged in
$profile_picture = "/ITP122/image/login-icon.png";
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
?><!DOCTYPE html>
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
        <a href="/ITP122/pages/location.php"><div class="location">
            <img class="location" src="/ITP122/image/location-icon.png" alt="location-icon">
        </a>
        </div>
        <div class="track">
            <a href="/ITP122/pages/track.php"><img class="track" src="/ITP122/image/track-icon.png" alt="track-icon"></a>
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
                    src="<?php echo isset($_SESSION['user_id']) ? $profile_picture : 'image/login-icon.png'; ?>" 
                    alt="login-icon" 
                    style="border-radius: <?php echo isset($_SESSION['user_id']) ? '50%' : '0'; ?>; 
                            width: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>; 
                            height: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>;">
            </a>
            <div id="dropdown-menu" class="dropdown-content">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="user/profile.php">View Profile</a>
                    <a href="user/edit-profile.php">Edit Profile</a>
                    <a href="user/logout.php">Log Out</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="login-text">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="user/profile.php"></a>
                <?php else: ?>
                    <a href="user/register.php"><p>Login/<br>Sign In</p></a>
                <?php endif; ?>
            </div>
    </div>
    </header>

<div class="menu">
    <a href="../index.php">HOME</a>
    <a href="../pages/product.php">PRODUCTS</a>
    <a href="desktop.php">DESKTOP</a>
    <a href="laptop.php">LAPTOP</a>
    <a href="../pages/brands.php">BRANDS</a>
</div>

<div class="breadcrumb">
    <a href="../index.php">Home</a> > <a>Monitor</a>
</div>

<div class="processor-section">
    <h2>MONITOR</h2>
</div>

<div class="product-grid">
    <?php
    $products = [
    ["id" => 103, "name" => "Nvision EG24S1 PRO 180HZ Flat IPS Panel 24 Gaming Monitor Black", "price" => 5250.00, "image" => "/ITP122/image/nvision1.png"],
  ["id" => 104, "name" => "Nvision N2455PRO-B 100Hz IPS Panel 23.8 Monitor Black", "price" => 4000.00, "image" => "/ITP122/image/nvision2.png"],
  ["id" => 105, "name" => "ViewPlus MG-27KI 27 165Hz 1MS 2K IPS Gaming Monitor Black", "price" => 7420.00, "image" => "/ITP122/image/ViewPlus.png"],
  ["id" => 106, "name" => "Gamdias Atlas HD24CII 24 180HZ FHD Curved Monitor", "price" => 6000.00, "image" => "/ITP122/image/Gamdias.png"],
  ["id" => 107, "name" => "MSI PRO MP251 24.5 FHD 100HZ IPS Monitor", "price" => 5520.00, "image" => "/ITP122/image/MSI_PRO_MP251.png"],
  ["id" => 108, "name" => "Viewplus MX-22 / ML-22 21.5 75Hz VA Monitor", "price" => 4485.00, "image" => "/ITP122/image/Viewplus1.png"],
  ["id" => 109, "name" => "YGT TN24FHD-GD 23.8 Display FHD LED Monitor", "price" => 2190.00, "image" => "/ITP122/image/YGT_TN24FHD-GD.png"],
  ["id" => 110, "name" => "HP Series 5 524SF 23.8 100HZ FHD IPS Monitor", "price" => 6000.00, "image" => "/ITP122/image/HP_Series_5.png"],
  ["id" => 111, "name" => "Viewplus MX-24CH 23.6? 165Hz Curved Monitor", "price" => 5290.00, "image" => "/ITP122/image/Viewplus_MX-24CH.png"],
  ["id" => 112, "name" => "Gigabyte G24F and G24F-2-TW 23.8 SS IPS 165Hz Gaming Monitor", "price" => 8900.00, "image" => "/ITP122/image/Gigabyte_G24F.png"],
  ["id" => 113, "name" => "MSI Optix G241V E2 23.8 75Hz 1ms IPS Freesync Monitor", "price" => 7995.00, "image" => "/ITP122/image/MSI_Optix_G241V.png"],
  ["id" => 114, "name" => "AOC 24G2SPE 24 165Hz IPS Gaming Monitor Black/Red", "price" => 9040.00, "image" => "/ITP122/image/AOC_24G2SPE.png"],
  ];
    foreach ($products as $product) {
        ?>
        <div class='product-card'>
            <img src='<?php echo $product['image']; ?>' alt='<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>'>
            <h4><?php echo $product['name']; ?></h4>
            <p>₱<?php echo number_format($product['price'], 2); ?></p>
            <div class='button-group'>
                <a href="#" class="buy-now-btn" data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>" data-price="<?php echo $product['price']; ?>" data-image="<?php echo $product['image']; ?>">
                    <button class='buy-now'>BUY NOW</button>
                </a>
                <a href="#" class="ajax-add" data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>" data-price="<?php echo $product['price']; ?>" data-image="<?php echo $product['image']; ?>">
                    <button class='add-to-cart'>
                        <img src='../image/add-to-cart.png' alt='Add to Cart'>
                    </button>
                </a>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<footer class="footer">
    <div class="footer-col">
        <h3>Customer Service</h3>
        <ul>
            <li><a href="/ITP122/pages/paymentfaq.php">Payment FAQs</a></li>
            <li><a href="ret&ref.php">Return and Refunds</a></li>
        </ul>
    </div>
    <div class="footer-col">
        <h3>Company</h3>
        <ul>
            <li><a href="/ITP122/pages/about_us.php">About Us</a></li>
            <li><a href="/ITP122/pages/contact_us.php">Contact Us</a></li>
        </ul>
    </div>
    <div class="footer-col">
        <h3>Links</h3>
        <ul>
            <li><a href="/ITP122/pages/corporate.php">SmartSolutions Corporate</a></li>
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
            dropdownMenu.style.display = 'none';  
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
<script src="../js/search.js"></script>
<script src="../js/jquery-animations.js"></script>
<script src="../js/ajax-cart.js?v=2024-11-18-9"></script>
</body>
</html>
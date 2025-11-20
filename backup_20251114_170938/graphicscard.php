<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="image/smartsolutionslogo.jpg" type="image/x-icon">
<link rel="stylesheet" href="design.css" />
<link rel="stylesheet" href="animations.css" />
<meta charset="UTF-8">
    <title>GRAPHICS CARD - SMARTSOLUTIONS</title>
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
            $profile_picture = $row['profile_picture']; // Use user's profile picture
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
        <a href="location.php"><div class="location">
            <img class="location" src="image/location-icon.png" alt="location-icon">
        </a>
        </div>
        <div class="track">
            <a href="track.php"><img class="track" src="image/track-icon.png" alt="track-icon"></a>
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
    <a href="products/desktop.php">DESKTOP</a>
    <a href="products/laptop.php">LAPTOP</a>
    <a href="brands.php">BRANDS</a>
</div>

<div class="breadcrumb">
    <a href="index.php">Home</a> > <a>Graphics Card</a>
</div>

<div class="processor-section">
    <h2>GRAPHICS CARD</h2>
</div>

<div class="product-grid">
    <?php
    $products = [
    ["id" => 35, "name" => "MSI NVIDIA® GeForce RTX 3060 Ventus 2X OC 12gb 192bit GDdr6 Gaming Videocard LHR", "price" => 31595.00, "image" => "image/MSI_RTX_3060_Ventus.png"],
    ["id" => 36, "name" => "Asrock RX 6600 8G CHALLENGER D 8gb 128bit GDdr6 Dual Fan Gaming Videocard", "price" => 13100.00, "image" => "image/Asrock_RX_6600_8G.png"],
    ["id" => 37, "name" => "ASUS Dual Radeon RX 6600 DUAL-RX6600-8G-V3 8GB 128-bit GDDR6 Videocard", "price" => 14295.00, "image" => "image/ASUS_Dual_RX_6600.png"],
    ["id" => 38, "name" => "Galax RTX 4060 8GB 1-Click OC 2X V2 Dual Fan (46NSL8MD9NXV) 128-bit GDDR6 Videocard", "price" => 18995.00, "image" => "image/Galax_RTX_4060_8GB.png"],
    ["id" => 39, "name" => "MSI NVIDIA® GeForce GTX 1650 D6 Ventus XS OC/XC OC V3 4gb 128bit GDdr6 Gaming Videocard", "price" => 12430.00, "image" => "image/msi1650.png"],
    ["id" => 40, "name" => "Gigabyte NVIDIA® GeForce RTX 3060 Gaming OC LHR R2.0 192bit GDdr6 Gaming Videocard RGB", "price" => 33055.00, "image" => "image/GigabyteRtx3060Gaming.png"],
    ["id" => 41, "name" => "Gigabyte Rx 6600 Eagle GV-R66EAGLE-8GD 8gb 128bit GDdr6, WINDFORCE 3X Cooling System Gaming Videocard", "price" => 24999.00, "image" => "image/Gigabyte-Rx6600-Eagle.png"],
    ["id" => 42, "name" => "Gigabyte NVIDIA® GeForce RTX™ 4070 TI Super Gaming OC 16GB 256-Bit GDDR6X Videocard", "price" => 60260.00, "image" => "image/Gigabyte_RTX_4070.png"],
    ["id" => 43, "name" => "Galax NVIDIA® GeForce RTX 4070 EX-Gamer PINK 12GB 192 BIT GDDR6X 47NOM7MD7KWH Videocard", "price" => 39216.00, "image" => "image/Galax_GeForce_RTX_4070.png"],
    ["id" => 44, "name" => "Asus ROG Strix Rtx 4060 Ti ROG-STRIX-RTX4060TI-O8G-GAMING 8gb 128bit GDdr6 Gaming Videocard", "price" => 32095.00, "image" => "image/AsusROGStrixRtx4060.png"],
    ["id" => 45, "name" => "ASUS Nvidia GeForce TUF Gaming RTX 4070 Ti White OC Edition 12GB 192bit GDDR6X Videocardd", "price" => 51950.00, "image" => "image/ASUS_Nvidia_GeForce_TUF_Gaming_RTX_4070.png"],
    ["id" => 46, "name" => "ASUS Nvidia GeForce RTX 4070 Ti OC Edition (PROART-RTX4070-O12G) 12GB 192bit GDDR6X Gaming Videocard", "price" => 48675.00, "image" => "image/ASUSNvidiaGeForceRTX4070.png"],
    ];

    foreach ($products as $product) {
        ?>
        <div class='product-card'>
            <img src='<?php echo $product['image']; ?>' alt='<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>'>
            <h4><?php echo $product['name']; ?></h4>
            <p>₱<?php echo number_format($product['price'], 2); ?></p>
            <div class='button-group'>
                <a href='<?php echo (isset($_SESSION['user_id']) ? "checkout.php" : "register.php"); ?>'>
                    <button class='buy-now'>BUY NOW</button>
                </a>
                <a href="#" class="ajax-add" data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>" data-price="<?php echo $product['price']; ?>" data-image="<?php echo $product['image']; ?>">
                    <button class='add-to-cart'>
                        <img src='image/add-to-cart.png' alt='Add to Cart'>
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
        <div class="copyright">
            &copy; 2024 SmartSolutions. All rights reserved.
        </div>
    </footer>

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

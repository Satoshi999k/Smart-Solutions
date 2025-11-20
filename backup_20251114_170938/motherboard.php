<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="image/smartsolutionslogo.jpg" type="image/x-icon">
<link rel="stylesheet" href="design.css" />
<link rel="stylesheet" href="animations.css" />
<meta charset="UTF-8">
    <title>MOTHERBOARDS - SMARTSOLUTIONS</title>
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
    <a href="index.php">Home</a> > <a>Motherboard</a>
</div>

<div class="processor-section">
    <h2>MOTHERBOARD</h2>
</div>

<div class="product-grid">
    <?php
    $products = [
    ["id" => 23, "name" => "MSI A520m-A Pro AMD Am4 Ddr4 Micro-ATX PCB Gaming Motherboard", "price" => 3899.00, "image" => "image/MSI_A520m.png"],
    ["id" => 24, "name" => "Asrock B550M Pro4 Socket Am4 Ddr4 Motherboard", "price" => 6540.00, "image" => "image/Asrock_B550M.png"],
    ["id" => 25, "name" => "Asrock B450M Steel Legend Am4 Gaming Motherboard", "price" => 5950.00, "image" => "image/Asrock_B450M.png"],
    ["id" => 26, "name" => "Asus Prime A520M-K Socket AM4 Ddr4Gaming Motherboard", "price" => 4094.00, "image" => "image/Asus_Prime_A520M.png"],
    ["id" => 27, "name" => "MSI PRO H610M-E Socket LGA 1700 Ddr4 Lightning Gen 4 Motherboard", "price" => 5209.00, "image" => "image/MSI_PRO_H610M.png"],
    ["id" => 28, "name" => "Asus Prime B550M-A Wifi II Socket Am4 Ddr4 Gaming Motherboard", "price" => 6850.00, "image" => "image/Asus_Prime_B550M.png"],
    ["id" => 29, "name" => "Biostar A520MHP Socket Am4 DDR4 Motherboard", "price" => 3000.00, "image" => "image/Biostar_A520MHP.png"],
    ["id" => 30, "name" => "Asrock B550M Pro SE Socket Am4 Ddr4 Motherboard", "price" => 5750.00, "image" => "image/Asrock_B550M_Pro.png"],
    ["id" => 31, "name" => "MSI Mag B550m Pro-Vdh WIFI mATX AM4 Ddr4 Gaming Motherboard", "price" => 6835.00, "image" => "image/MSI_Mag_B550m.png"],
    ["id" => 32, "name" => "Asrock X570S Phantom Gaming Riptide Socket Am4 Ddr4 Motherboard", "price" => 10150.00, "image" => "image/Asrock_X570S.png"],
    ["id" => 33, "name" => "Asus ROG Strix B550-F Gaming Wifi II Socket Am4 Ddr4 Motherboard", "price" => 12850.00, "image" => "image/AsusROGStrixB550.png"],
    ["id" => 34, "name" => "MSI PRO X670-P WIFI ATX DDR5 AM5 2.5G LAN Motherboard", "price" => 19995.00, "image" => "image/MSIPROX670-PWIFIATX.png"],
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

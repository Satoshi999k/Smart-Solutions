<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="image/smartsolutionslogo.jpg" type="image/x-icon">
<link rel="stylesheet" href="design.css" />
<link rel="stylesheet" href="animations.css" />
<meta charset="UTF-8">
    <title>MONITOR - SMARTSOLUTIONS</title>
</head>
<body>
<?php
// Start session to check if the user is logged in
session_start();

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
    <a href="index.php">Home</a> > <a>Keyboard</a>
</div>

<div class="processor-section">
    <h2>KEYBOARD</h2>
</div>

<div class="product-grid">
    <?php
    $products = [
    ["id" => 115, "name" => "RAKK TALA 81 Keys White / Trimode / RGB / Universal Hotswap / Gasket Mount / MS Red Switch / Black", "price" => 1995.00, "image" => "image/RAKK_TALA_81.png"],
  ["id" => 116, "name" => "RAKK Hanan Ultra|Gasket Mount Aluminum CNC Case|81 Keys Trimode Mechanical Keyboard|White", "price" => 3495.00, "image" => "image/RAKK_Hanan_Ultra_Gasket.png"],
  ["id" => 117, "name" => "Fantech MK890 V2 Atom 96 Hot swappable 3pin Red switch Mechanical Keyboard Sky Blue", "price" => 1220.00, "image" => "image/Fantech_MK890_V2.png"],
  ["id" => 118, "name" => "RAKK PLUMA V2 67 Keys Trimode Outemu Red Switch Mechanical Gaming Keyboard RGB Black", "price" => 1895.00, "image" => "image/RAKK_PLUMA_V2_67_Keys.png"],
  ["id" => 119, "name" => "Inplay NK680-B Red switch Mechanical Gaming Keyboard Black", "price" => 630.00, "image" => "image/Inplay_NK680-B_Red_switch.png"],
  ["id" => 120, "name" => "Royal Kludge RK61 Trimode Red switch Mechanical Keyboard Black", "price" => 2200.00, "image" => "image/Royal_Kludge_RK61_Trimode_Red_switch.png"],
  ["id" => 121, "name" => "Aula F99 RGB Gaming Mechanical Hotswappable Keyboard Purple", "price" => 3240.00, "image" => "image/AulaF99RGBGamingMechanicalHotswappable.png"],
  ["id" => 122, "name" => "RAKK HARIBON Ergonomic Mechanical Gaming Keyboard", "price" => 7995.00, "image" => "image/RAKK_HARIBON_Ergonomic_Mechanical.png"],
  ["id" => 123, "name" => "Asus ROG Strix Flare II NX Blue Gaming Keyboard", "price" => 7955.00, "image" => "image/Asus_ROG_Strix_Flare_II_NX_Blue.png"],
  ["id" => 124, "name" => "Redragon K649 PRO Gasket Mechanical Hotswap Tri-mode keyboard Crystal...", "price" => 3240.00, "image" => "image/Redragon_K649_PRO_Gasket_Mechanical.png"],
  ["id" => 125, "name" => "ONIKUMA G30 Wired Mechanical 84 Key RGB Backlit Keyboard White", "price" => 1670.00, "image" => "image/ONIKUMA_G30_Wired_Mechanical.png"],
  ["id" => 126, "name" => "Royal Kludge RK-M75 Trimode Mechanical Keyboard Oled Screen...", "price" => 3460.00, "image" => "image/Royal_Kludge_RK-M75_Trimode_Mechanical_Keyboard.png"],
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

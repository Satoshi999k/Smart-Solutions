<?php
session_start();

// Initialize cart from database
require_once 'init_cart.php';

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

// Check if logged in
$profile_picture = "image/login-icon.png";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
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

// Get search query
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$searchResults = [];

// Define all products (from cart.php products array)
$allProducts = [
    ["id" => 1, "name" => "Core i7 12700 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W", "price" => 25195.00, "image" => "image/desktop1.png", "category" => "Desktop"],
    ["id" => 2, "name" => "Core i3 12100 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W", "price" => 14795.00, "image" => "image/desktop2.png", "category" => "Desktop"],
    ["id" => 3, "name" => "MSI Thin A15 B7UCX-084PH 15.6 / FHD 144Hz AMD RYZEN 5 7535HS/8GB/512GBSSD/RTX 2050 4GB/WIN11 Laptop", "price" => 38995.00, "image" => "image/laptop1.png", "category" => "Laptop"],
    ["id" => 4, "name" => "Lenovo V15 G4 IRU 15.6 / FHD Intel Core i5- 1335U/8GB DDR4/512GB M.2 SSD Laptop MN", "price" => 29495.00, "image" => "image/laptop2.png", "category" => "Laptop"],
    ["id" => 5, "name" => "Team Elite Vulcan TUF 16gb 2x8 3200mhz Ddr4 Gaming Memory", "price" => 1999.00, "image" => "image/deal1.png", "category" => "Memory"],
    ["id" => 6, "name" => "Team Elite Plus 8gb 1x8 3200Mhz Black Gold Ddr4 Memory", "price" => 1045.00, "image" => "image/deal2.png", "category" => "Memory"],
    ["id" => 7, "name" => "G.Skill Ripjaws V 16gb 2x8 3200mhz Ddr4 Memory Black", "price" => 2185.00, "image" => "image/deal3.png", "category" => "Memory"],
    ["id" => 8, "name" => "Team Elite 8gb 1x8 1600mhz Ddr3 with Heatspreader Memory", "price" => 1065.00, "image" => "image/deal4.png", "category" => "Memory"],
    ["id" => 9, "name" => "AMD Ryzen 5 Pro 4650G Socket Am4 3.7ghz with Radeon Vega 7 Processor with Wraith Stealth Cooler MPK", "price" => 5845.00, "image" => "image/deal5.png", "category" => "Processor"],
    ["id" => 10, "name" => "Team Elite TForce Delta TUF 16GB 2x8 3200mhz Ddr4 RGB Gaming Memory", "price" => 3155.00, "image" => "image/deal6.png", "category" => "Memory"],
    ["id" => 11, "name" => "AMD Ryzen 5 5600G Socket Am4 3.9GHz with Radeon Vega 7 Processor Wraith Stealth Cooler", "price" => 6895.00, "image" => "image/ryzen5600g.png", "category" => "Processor"],
    ["id" => 12, "name" => "AMD Ryzen 5 5600X Socket AM4 3.7GHz with Wraith Stealth Cooler VR Ready Premium Desktop Processor", "price" => 6395.00, "image" => "image/ryzen5600x.png", "category" => "Processor"],
    ["id" => 13, "name" => "AMD Ryzen 7 5700X Socket AM4 3.4GHz Processor with AMD Wraith Stealth cooler MPK", "price" => 9975.00, "image" => "image/ryzen5700x.png", "category" => "Processor"],
    ["id" => 14, "name" => "AMD Ryzen 5 5600 Socket Am4 3.5GHz Processor with Wraith stealth cooler MPK Processor", "price" => 6300.00, "image" => "image/ryzen5600.png", "category" => "Processor"],
    ["id" => 15, "name" => "AMD Ryzen 5 2400g Socket Am4 3.6ghz with Radeon RX Vega 11 Processor MPK", "price" => 3762.00, "image" => "image/ryzen2400g.png", "category" => "Processor"],
    ["id" => 16, "name" => "AMD Ryzen 3 3200g Socket Am4 3.6ghz with Radeon Vega 8 Processor", "price" => 4050.00, "image" => "image/ryzen3200g.png", "category" => "Processor"],
    ["id" => 17, "name" => "AMD Ryzen 5 Pro 4650G Socket Am4 3.7ghz with Radeon Vega 7 Processor with Wraith Stealth Cooler MPK", "price" => 8595.00, "image" => "image/ryzen52400g.png", "category" => "Processor"],
    ["id" => 18, "name" => "Intel Core i3-12100 Alder Lake Socket 1700 4.30GHz Processor MPK", "price" => 6250.00, "image" => "image/IntelCorei3-12100.png", "category" => "Processor"],
    ["id" => 19, "name" => "Intel Core I5-12400 Alder Lake Socket 1700 2.5GHz Processor MPK", "price" => 8350.00, "image" => "image/IntelCorei5-12400.png", "category" => "Processor"],
    ["id" => 20, "name" => "Intel Core i5-14600K Raptor Lake Socket LGA 1700 2.50GHz Processor", "price" => 25995.00, "image" => "image/IntelCorei5-14600K.png", "category" => "Processor"],
    ["id" => 21, "name" => "Intel Core I5-11400 Socket 1200 2.60GHz Intel UHD Graphics 730 Ttp Rocket Lake Processor", "price" => 10395.00, "image" => "image/IntelCorei5-11400.png", "category" => "Processor"],
    ["id" => 22, "name" => "AMD Ryzen 7 9700X 3.8GHz AM5 Socket DDR5 Processor", "price" => 25395.00, "image" => "image/ryzen9700x.png", "category" => "Processor"],
    ["id" => 23, "name" => "MSI A520m-A Pro AMD Am4 Ddr4 Micro-ATX PCB Gaming Motherboard", "price" => 3899.00, "image" => "image/MSI_A520m.png", "category" => "Motherboard"],
    ["id" => 24, "name" => "Asrock B550M Pro4 Socket Am4 Ddr4 Motherboard", "price" => 6540.00, "image" => "image/Asrock_B550M.png", "category" => "Motherboard"],
    ["id" => 25, "name" => "Asrock B450M Steel Legend Am4 Gaming Motherboard", "price" => 5950.00, "image" => "image/Asrock_B450M.png", "category" => "Motherboard"],
    ["id" => 26, "name" => "Asus Prime A520M-K Socket AM4 Ddr4Gaming Motherboard", "price" => 4094.00, "image" => "image/Asus_Prime_A520M.png", "category" => "Motherboard"],
    ["id" => 27, "name" => "MSI PRO H610M-E Socket LGA 1700 Ddr4 Lightning Gen 4 PCI-E User Friendly Design Gaming Motherboard", "price" => 5209.00, "image" => "image/MSI_PRO_H610M.png", "category" => "Motherboard"],
    ["id" => 28, "name" => "Asus Prime B550M-A Wifi II Socket Am4 Ddr4 Gaming Motherboard", "price" => 6850.00, "image" => "image/Asus_Prime_B550M.png", "category" => "Motherboard"],
    ["id" => 29, "name" => "Biostar A520MHP Socket Am4 DDR4 Motherboard", "price" => 3000.00, "image" => "image/Biostar_A520MHP.png", "category" => "Motherboard"],
    ["id" => 30, "name" => "Asrock B550M Pro SE Socket Am4 Ddr4 Motherboard", "price" => 5750.00, "image" => "image/Asrock_B550M_Pro.png", "category" => "Motherboard"],
    ["id" => 31, "name" => "MSI Mag B550m Pro-Vdh WIFI mATX AM4 Ddr4 Gaming Motherboard", "price" => 6835.00, "image" => "image/MSI_Mag_B550m.png", "category" => "Motherboard"],
    ["id" => 32, "name" => "Asrock X570S Phantom Gaming Riptide Socket Am4 Ddr4 Motherboard", "price" => 10150.00, "image" => "image/Asrock_X570S.png", "category" => "Motherboard"],
    ["id" => 33, "name" => "Asus ROG Strix B550-F Gaming Wifi II Socket Am4 Ddr4 Aura Sync RGB Lighting Best Gaming Audio Gaming Motherboard", "price" => 12850.00, "image" => "image/AsusROGStrixB550.png", "category" => "Motherboard"],
    ["id" => 34, "name" => "MSI PRO X670-P WIFI ATX DDR5 AM5 2.5G LAN with Wi-Fi 6E Solution Gaming Motherboard", "price" => 19995.00, "image" => "image/MSIPROX670-PWIFIATX.png", "category" => "Motherboard"],
    ["id" => 35, "name" => "MSI NVIDIA® GeForce RTX 3060 Ventus 2X OC 12gb 192bit GDdr6 Gaming Videocard LHR", "price" => 31595.00, "image" => "image/MSI_RTX_3060_Ventus.png", "category" => "Graphics Card"],
    ["id" => 36, "name" => "Asrock RX 6600 8G CHALLENGER D 8gb 128bit GDdr6 Dual Fan Gaming Videocard", "price" => 13100.00, "image" => "image/Asrock_RX_6600_8G.png", "category" => "Graphics Card"],
    ["id" => 37, "name" => "ASUS Dual Radeon RX 6600 DUAL-RX6600-8G-V3 8GB 128-bit GDDR6 Videocard", "price" => 14295.00, "image" => "image/ASUS_Dual_RX_6600.png", "category" => "Graphics Card"],
];

// Search through products
if (!empty($searchQuery)) {
    $queryLower = strtolower($searchQuery);
    foreach ($allProducts as $product) {
        $nameLower = strtolower($product['name']);
        $categoryLower = strtolower($product['category']);
        
        // Match by name or category
        if (strpos($nameLower, $queryLower) !== false || strpos($categoryLower, $queryLower) !== false) {
            $searchResults[] = $product;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="image/smartsolutionslogo.jpg" type="image/x-icon">
<link rel="stylesheet" href="design.css" />
<link rel="stylesheet" href="animations.css" />
<meta charset="UTF-8">
<title>Search Results - SMARTSOLUTIONS</title>
</head>
<body>

<header>
    <div class="ssheader">
        <div class="logo">
            <img src="image/logo.png" alt="Smart Solutions Logo">
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search" value="<?php echo htmlspecialchars($searchQuery); ?>">
            <div class="search-icon">
                <img src="image/search-icon.png" alt="Search Icon">
            </div>
        </div>
        <a href="location.php">
            <div class="location">
                <img class="location" src="image/location-icon.png" alt="location-icon">
            </div>
        </a>
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

       <div class="login profile-dropdown" style="position: relative; display: inline-block;">
            <a href="javascript:void(0)" onclick="toggleDropdown()">
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
    <a href="index.php">Home</a> > <a>Search Results</a>
</div>

<div style="padding: 30px 20px; max-width: 1200px; margin: 0 auto;">
    <h2 style="margin-bottom: 10px;">Search Results</h2>
    <p style="color: #666; margin-bottom: 30px;">
        <?php 
        if (!empty($searchQuery)) {
            echo "Found <strong>" . count($searchResults) . "</strong> product(s) matching \"<strong>" . htmlspecialchars($searchQuery) . "</strong>\"";
        } else {
            echo "Enter a search term to find products";
        }
        ?>
    </p>

    <?php if (!empty($searchResults)): ?>
        <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
            <?php foreach ($searchResults as $product): ?>
                <div class='product-card' style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; transition: all 0.3s ease;">
                    <img src='<?php echo $product['image']; ?>' alt='<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>' style="width: 100%; height: 200px; object-fit: cover; border-radius: 5px; margin-bottom: 10px;">
                    <h4 style="margin: 10px 0; font-size: 14px; min-height: 40px;"><?php echo $product['name']; ?></h4>
                    <p style="color: #007BFF; font-size: 16px; font-weight: bold; margin: 10px 0;">₱<?php echo number_format($product['price'], 2); ?></p>
                    <p style="color: #666; font-size: 12px; margin: 10px 0;"><?php echo $product['category']; ?></p>
                    <div class='button-group' style="display: flex; gap: 10px; margin-top: 15px;">
                        <a href='<?php echo (isset($_SESSION['user_id']) ? "checkout.php" : "register.php"); ?>' style="flex: 1;">
                            <button class='buy-now' style="width: 100%; padding: 8px; background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer;">BUY NOW</button>
                        </a>
                        <a href="#" class="ajax-add" data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>" data-price="<?php echo $product['price']; ?>" data-image="<?php echo $product['image']; ?>" style="flex: 1;">
                            <button class='add-to-cart' style="width: 100%; padding: 8px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                🛒 ADD TO CART
                            </button>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif (!empty($searchQuery)): ?>
        <div style="text-align: center; padding: 50px 20px; background-color: #f5f5f5; border-radius: 8px;">
            <h3 style="color: #999; margin-bottom: 10px;">No Products Found</h3>
            <p style="color: #666; margin-bottom: 20px;">Sorry, we couldn't find any products matching "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"</p>
            <p style="color: #666;">Try searching with different keywords or <a href="product.php" style="color: #007BFF; text-decoration: none;">browse all products</a></p>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 50px 20px; background-color: #f5f5f5; border-radius: 8px;">
            <h3 style="color: #999;">Enter a Search Term</h3>
            <p style="color: #666;">Use the search bar above to find products</p>
        </div>
    <?php endif; ?>
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
    function toggleDropdown() {
        var dropdownMenu = document.getElementById("dropdown-menu");
        dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    }

    window.onclick = function(event) {
        var dropdownMenu = document.getElementById("dropdown-menu");
        if (!event.target.matches('.profile-dropdown, .profile-dropdown *')) {
            dropdownMenu.style.display = 'none';
        }
    };
</script>
<script src="search-dynamic.js"></script>
<script src="ajax-cart.js"></script>
</body>
</html>

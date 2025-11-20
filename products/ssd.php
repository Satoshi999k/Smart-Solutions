<?php
// Start session to check if the user is logged in
session_start();

// Initialize cart from database
require_once 'init_cart.php';

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
}
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
    <a href="desktop.php">DESKTOP</a>
    <a href="laptop.php">LAPTOP</a>
    <a href="../pages/brands.php">BRANDS</a>
</div>

<div class="breadcrumb">
    <a href="../index.php">Home</a> > <a>SSD</a>
</div>

<div class="processor-section">
    <h2>SOLID STATE DRIVE</h2>
</div>

<div class="product-grid">
    <?php
    $products = [
    ["id" => 59, "name" => "Kingston NV1 PCIe M.2 3.0 NVME 1T Band NV2 PCIe 4.0 1TB", "price" => 2835.00, "image" => "/ITP122/image/Kingston_NV1_500GB.png"],
    ["id" => 60, "name" => "Team Group GX2 512GB Sata III 2.5 Solid State Drive", "price" => 3295.00, "image" => "/ITP122/image/Team_Group_GX2_512GB.png"],
    ["id" => 61, "name" => "Team Group MP33 256GB M.2 PCIe NVME Solid State Drive", "price" => 1425.00, "image" => "/ITP122/image/Team_Group_MP33_256GB.png"],
    ["id" => 62, "name" => "Crucial P3 Plus 500GB, 1TB PCIe 4.0 Gen4 NVMe M.2 Solid State Drive", "price" => 3075.00, "image" => "/ITP122/image/Crucial_P3_Plus_500GB_PCIe.png"],
    ["id" => 63, "name" => "Team Group GX2 256gb SATA 2.5 Faster Booting Solid State Drive", "price" => 1521.00, "image" => "/ITP122/image/Team_Group_GX2_256gb.png"],
    ["id" => 64, "name" => "Lexar NM610 Pro 1TB M.2 NVME Solid State Drive", "price" => 3525.00, "image" => "/ITP122/image/Lexar_NM610_Pro_1TB.png"],
    ["id" => 65, "name" => "Adata SU650 Solid State Drive 512GB SATA 2.5", "price" => 2095.00, "image" => "/ITP122/image/Adata_SU650_Solid_State_Drive_512GB_SATA.png"],
    ["id" => 66, "name" => "MSI Spatium M450 500GB PCIE NVME M.2 Solid State Drive", "price" => 3765.00, "image" => "/ITP122/image/MSI_Spatium_M450_500GB_PCIE.png"],
    ["id" => 67, "name" => "Apacer Panther AS350 256GB/512GB 2.5 SATA3 Optimized Solid State Drive", "price" => 1495.00, "image" => "/ITP122/image/Apacer_Panther.png"],
    ["id" => 68, "name" => "Samsung 970 EVO Plus 1TB NVME M.2 Solid State Drive", "price" => 7555.00, "image" => "/ITP122/image/Samsung_970_EVO_Plus_1TB.png"],
    ["id" => 69, "name" => "Gigabyte GP-GSTFS31100TNTD 1TB SATA 2.5 Solid State Drive", "price" => 4345.00, "image" => "/ITP122/image/Gigabyte_GP.png"],
    ["id" => 70, "name" => "Samsung 990 Pro 2TB NVME M.2 Solid State Drive", "price" => 11750.00, "image" => "/ITP122/image/Samsung_990_Pro_2TB.png"],
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
<script src="../js/ajax-cart-clean.js"></script>
</body>
</html>

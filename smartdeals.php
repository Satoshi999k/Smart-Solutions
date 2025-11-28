<?php
// Start session to check if the user is logged in
session_start();

// Initialize cart from database
require_once 'init_cart.php';

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

// Fetch discount products from database
$discountProducts = [];
$query = "SELECT id, name, price, image FROM products WHERE id IN (5, 157, 158, 7, 6, 9, 8, 10, 159, 160, 161, 162) ORDER BY id ASC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $discountProducts[] = $row;
    }
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
<link rel="shortcut icon" href="image/smartsolutionslogo.jpg" type="image/x-icon">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/design.css" />
<link rel="stylesheet" href="css/animations.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
<meta charset="UTF-8">
<title>SMART DEALS - SMARTSOLUTIONS</title>
<style>
@keyframes slideDownMenu {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp { 
    from { opacity: 0; transform: translateY(20px); } 
    to { opacity: 1; transform: translateY(0); } 
}

@keyframes slideInDown { 
    from { opacity: 0; transform: translateY(-30px); } 
    to { opacity: 1; transform: translateY(0); } 
}

#main-menu {
    animation: slideDownMenu 0.6s ease-out 0.3s both;
}

body { background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%) !important; }

.breadcrumb { padding: 18px 32px; font-size: 14px; color: #666; background: transparent; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.breadcrumb a { color: #0062F6; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 6px; }
.breadcrumb a:hover { color: #0052D4; transform: translateX(4px); }
.breadcrumb .material-icons { font-size: 20px; vertical-align: middle; }
.breadcrumb span:not(.material-icons) { display: inline-flex; align-items: center; }

.product-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; padding: 40px 24px; max-width: 1200px; margin: 0 auto; background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); }

.product-card { background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); border: 2px solid #e8f1ff; border-radius: 16px; padding: 20px; text-align: center; transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); box-shadow: 0 4px 16px rgba(0, 98, 246, 0.08); position: relative; overflow: hidden; animation: fadeInUp 0.6s ease-out forwards; opacity: 0; }

.product-card:nth-child(1) { animation-delay: 0.1s; } 
.product-card:nth-child(2) { animation-delay: 0.2s; } 
.product-card:nth-child(3) { animation-delay: 0.3s; } 
.product-card:nth-child(4) { animation-delay: 0.4s; } 
.product-card:nth-child(5) { animation-delay: 0.5s; } 
.product-card:nth-child(6) { animation-delay: 0.6s; } 
.product-card:nth-child(7) { animation-delay: 0.7s; } 
.product-card:nth-child(8) { animation-delay: 0.8s; }
.product-card:nth-child(9) { animation-delay: 0.9s; } 
.product-card:nth-child(10) { animation-delay: 1s; } 
.product-card:nth-child(11) { animation-delay: 1.1s; } 
.product-card:nth-child(12) { animation-delay: 1.2s; }

.product-card::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(0, 98, 246, 0.1), transparent); transition: left 0.6s ease; }

.product-card:hover::before { left: 100%; }

.product-card:hover { transform: translateY(-12px) scale(1.02); box-shadow: 0 12px 40px rgba(0, 98, 246, 0.2); border-color: #0062F6; background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%); }

.product-card img { max-width: 100%; height: 180px; object-fit: contain; transition: all 0.4s ease; filter: drop-shadow(0 2px 8px rgba(0, 98, 246, 0.15)); margin-bottom: 16px; }

.product-card:hover img { filter: drop-shadow(0 8px 20px rgba(0, 98, 246, 0.3)); transform: scale(1.1); }

.deal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 8px; }

.deal-badge { background: linear-gradient(135deg, #FF6B6B 0%, #FF5252 100%); color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }

.deal-brand { background: #f0f7ff; color: #0062F6; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

.product-card h4 { font-size: 14px; color: #333; margin-bottom: 12px; font-weight: 600; line-height: 1.4; }

.price-container { margin: 12px 0; }

.discounted-price { font-size: 20px; color: #0062F6; font-weight: 700; margin-bottom: 4px; }

.original-price { font-size: 13px; color: #999; text-decoration: line-through; }

.button-group { display: flex; gap: 12px; justify-content: center; margin-top: 16px; }

.button-group a { flex: 1; text-decoration: none; }

.buy-now, .add-to-cart { width: 100%; padding: 10px 16px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; font-size: 13px; }

.buy-now { background: linear-gradient(135deg, #0062F6 0%, #0052D4 100%); color: white; }

.buy-now:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0, 98, 246, 0.3); }

.add-to-cart { background: white; border: 2px solid #0062F6; padding: 8px 12px; display: flex; align-items: center; justify-content: center; }

.add-to-cart:hover { background: #f0f7ff; border-color: #0052D4; }

.add-to-cart img { max-width: 20px; height: 20px; object-fit: contain; filter: none; margin: 0; }

@media (max-width: 768px) { .product-grid { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; padding: 20px 16px; } .product-card { padding: 16px; } .product-card img { height: 140px; } }

@media (max-width: 480px) { .product-grid { grid-template-columns: 1fr; gap: 12px; padding: 16px; } .product-card { padding: 12px; } .product-card img { height: 120px; } .button-group { gap: 8px; } }
</style>
</head>
<body>
<header>
    <div class="ssheader">
        <div class="logo">
            <img src="image/logo.png" alt="Smart Solutions Logo">
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search">
            <img class="search-icon" src="image/search-icon.png" alt="Search" style="width: 20px; height: 20px; cursor: pointer;">
        </div>
        <a href="pages/location.php">
            <div class="location">
                <img class="location" src="image/location-icon.png" alt="location-icon">
            </div>
        </a>
        <div class="track">
            <a href="pages/track.php"><img class="track" src="image/track-icon.png" alt="track-icon"></a>
        </div>
        <a href="cart.php">
            <div class="cart">
                <img class="cart" src="image/cart-icon.png" alt="cart-icon" style="width: 35px; height: auto;">
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

        <!-- Modern Profile Dropdown CSS -->
        <style>
            .profile-dropdown { position: relative; display: inline-block; }
            
            .dropdown-content { display: none; position: absolute; top: 110%; right: 0; background: white; border-radius: 8px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); border: 1px solid #e0e0e0; min-width: 200px; z-index: 1000; }
            
            .dropdown-content a { display: flex; align-items: center; padding: 12px 16px; color: #333; font-size: 14px; font-weight: 500; text-decoration: none; transition: all 0.2s ease; border-left: 3px solid transparent; }
            
            .dropdown-content a:hover { background: #f5f5f5; color: #0062F6; border-left-color: #0062F6; }
            
            .dropdown-content a .material-icons { font-size: 18px; margin-right: 12px; display: flex; align-items: center; }
            
            .profile-dropdown.active .dropdown-content { display: block; animation: slideDown 0.25s ease-out; }
            
            @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        </style>

       <div class="login profile-dropdown">
            <a href="javascript:void(0)" onclick="toggleDropdown(event)">
                <!-- Check if user is logged in, if yes show profile picture, else show login icon -->
                <img class="login" 
                    src="<?php echo isset($_SESSION['user_id']) ? $profile_picture : 'image/login-icon.png'; ?>" 
                    alt="login-icon" 
                    style="border-radius: <?php echo isset($_SESSION['user_id']) ? '50%' : '0'; ?>; 
                            width: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>; 
                            height: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>;">
            </a>
            <div class="dropdown-content">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="user/profile.php"><span class="material-icons">person</span>View Profile</a>
                    <a href="user/edit-profile.php"><span class="material-icons">edit</span>Edit Profile</a>
                    <a href="user/logout.php"><span class="material-icons">logout</span>Log Out</a>
                <?php endif; ?>
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

<div class="menu" id="main-menu">
    <a href="index.php">HOME</a>
    <a href="pages/product.php">PRODUCTS</a>
    <a href="products/desktop.php">DESKTOP</a>
    <a href="products/laptop.php">LAPTOP</a>
    <a href="pages/brands.php">BRANDS</a>
</div>

<div class="breadcrumb">
    <a href="index.php"><span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px;">home</span>Home</a> > <span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px; color: #0062F6;">local_offer</span><a>Smart Deals</a>
</div>

<div class="processor-section">
    <h2 style="font-size: 48px; font-weight: 800; text-align: center; margin: 50px 0 20px; color: #0062F6; letter-spacing: -1.5px; animation: slideInDown 0.7s cubic-bezier(0.34, 1.56, 0.64, 1); text-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); transition: all 0.3s ease;">SMART DEALS</h2>
</div>

<div class="product-grid">
  <?php
    // Display products fetched from database
    foreach ($discountProducts as $product) {
        $prodId = $product['id'];
        // Calculate discount (assume 50% markup for original price)
        $originalPrice = $product['price'] * 1.5;
        $discount = $originalPrice - $product['price'];
        
        // Brand names mapping
        $brands = [
            5 => "TEAM ELITE",
            157 => "MSI",
            158 => "GIGABYTE",
            7 => "G.SKILL",
            6 => "TEAM ELITE",
            9 => "AMD",
            8 => "TEAM ELITE",
            10 => "TEAM ELITE",
            159 => "COOLERMASTER",
            160 => "EDIFIER",
            161 => "KINGSTON",
            162 => "AMD",
        ];
        
        $brand = $brands[$prodId] ?? "BRAND";
        ?>
        <div class='product-card'>
            <div class='deal-header'>
                <div class='deal-badge'>SAVE ₱<?php echo number_format($discount, 2); ?></div>
                <div class='deal-brand'><?php echo $brand; ?></div>
            </div>
            <img src='<?php echo $product['image']; ?>' alt='<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>'>
            <h4><?php echo $product['name']; ?></h4>
            <div class='price-container'>
                <div class='discounted-price'>₱<?php echo number_format($product['price'], 2); ?></div>
                <div class='original-price'>₱<?php echo number_format($originalPrice, 2); ?></div>
            </div>
             <div class='button-group'>
                <a href="#" class="buy-now-btn" data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>" data-price="<?php echo $product['price']; ?>" data-image="<?php echo $product['image']; ?>">
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
    // Modern dropdown toggle function
    function toggleDropdown(event) {
        event.preventDefault();
        event.stopPropagation();
        const profileDropdown = document.querySelector('.profile-dropdown');
        profileDropdown.classList.toggle('active');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const profileDropdown = document.querySelector('.profile-dropdown');
        if (profileDropdown && !profileDropdown.contains(event.target)) {
            profileDropdown.classList.remove('active');
        }
    });

    // Close dropdown on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const profileDropdown = document.querySelector('.profile-dropdown');
            if (profileDropdown) {
                profileDropdown.classList.remove('active');
            }
        }
    });
    
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
        
        fetch('set_buynow_product.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'checkout.php';
            }
        })
        .catch(err => console.log(err));
    });
</script>

<script src="js/ajax-cart-clean.js"></script>
<script src="js/search.js"></script>
<script src="js/jquery-animations.js"></script>
</body>
</html>


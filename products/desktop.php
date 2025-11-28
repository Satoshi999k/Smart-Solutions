<?php
// Start session to check if the user is logged in
session_start();

// Initialize cart from database
require_once 'init_cart.php';

// Database connection (replace with your credentials)
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from database
$result = $conn->query("SELECT id, name, price, image, stock FROM products WHERE LOWER(category) = 'desktop' ORDER BY id DESC");
$products = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Ensure image paths have proper prefix
        $image = $row['image'];
        if (!preg_match('/^(\/|http)/', $image)) {
            $image = '/ITP122/' . $image;
        }
        // Ensure stock has a default value
        $stock = isset($row['stock']) ? intval($row['stock']) : 10;
        $products[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'image' => $image,
            'stock' => $stock
        ];
    }
}

// Get user profile picture
$profile_picture = "/ITP122/image/login-icon.png"; 
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
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
<link rel="stylesheet" href="/ITP122/css/design.css?v=<?php echo time(); ?>" />
<link rel="stylesheet" href="/ITP122/css/animations.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
<meta charset="UTF-8">
<title>DESKTOP - SMARTSOLUTIONS</title>
<style>
    body { background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%) !important; }
    .breadcrumb { padding: 16px 24px; font-size: 14px; color: #555; background: transparent; }
    .breadcrumb a { color: #0062F6; text-decoration: none; font-weight: 500; transition: color 0.3s ease; }
    .breadcrumb a:hover { color: #0052D4; }
    .processor-section { background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); padding: 40px 24px; }
    .processor-section h2 { text-align: center; font-size: 36px; color: #0062F6; margin-bottom: 40px; font-weight: 700; animation: slideInDown 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); letter-spacing: 1px; }
    .product-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; padding: 40px 24px; max-width: 1200px; margin: 0 auto; background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); }
    .product-card { background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); border: 2px solid #e8f1ff; border-radius: 16px; padding: 20px; text-align: center; transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); box-shadow: 0 4px 16px rgba(0, 98, 246, 0.08); position: relative; overflow: hidden; animation: fadeInUp 0.6s ease-out forwards; opacity: 0; }
    .product-card:nth-child(1) { animation-delay: 0.1s; } .product-card:nth-child(2) { animation-delay: 0.2s; } .product-card:nth-child(3) { animation-delay: 0.3s; } .product-card:nth-child(4) { animation-delay: 0.4s; } .product-card:nth-child(5) { animation-delay: 0.5s; } .product-card:nth-child(6) { animation-delay: 0.6s; } .product-card:nth-child(7) { animation-delay: 0.7s; } .product-card:nth-child(8) { animation-delay: 0.8s; }
    .product-card::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(0, 98, 246, 0.1), transparent); transition: left 0.6s ease; }
    .product-card:hover::before { left: 100%; }
    .product-card:hover { transform: translateY(-12px) scale(1.02); box-shadow: 0 12px 40px rgba(0, 98, 246, 0.2); border-color: #0062F6; background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%); }
    .product-card img { max-width: 100%; height: 180px; object-fit: contain; transition: all 0.4s ease; filter: drop-shadow(0 2px 8px rgba(0, 98, 246, 0.15)); margin-bottom: 16px; }
    .product-card:hover img { filter: drop-shadow(0 8px 20px rgba(0, 98, 246, 0.3)); transform: scale(1.1); }
    .product-card h4 { font-size: 14px; color: #333; margin-bottom: 8px; font-weight: 600; }
    .product-card p { font-size: 13px; color: #666; margin: 4px 0; }
    .product-card p:first-of-type { font-size: 18px; color: #0062F6; font-weight: 700; margin-bottom: 12px; }
    .button-group { display: flex; gap: 12px; justify-content: center; margin-top: 16px; }
    .button-group a { flex: 1; text-decoration: none; }
    .buy-now, .add-to-cart { width: 100%; padding: 10px 16px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; font-size: 13px; }
    .buy-now { background: linear-gradient(135deg, #0062F6 0%, #0052D4 100%); color: white; }
    .buy-now:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0, 98, 246, 0.3); }
    .add-to-cart { background: white; border: 2px solid #0062F6; padding: 8px 12px; display: flex; align-items: center; justify-content: center; }
    .add-to-cart:hover { background: #f0f7ff; border-color: #0052D4; }
    .add-to-cart img { max-width: 20px; height: 20px; object-fit: contain; filter: none; margin: 0; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideInDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
    @media (max-width: 768px) { .processor-section h2 { font-size: 28px; margin-bottom: 30px; } .product-grid { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; padding: 20px 16px; } .product-card { padding: 16px; } .product-card img { height: 140px; } }
    @media (max-width: 480px) { .processor-section { padding: 24px 16px; } .processor-section h2 { font-size: 24px; margin-bottom: 24px; } .product-grid { grid-template-columns: 1fr; gap: 12px; padding: 16px; } .product-card { padding: 12px; } .product-card img { height: 120px; } .button-group { gap: 8px; } }
</style>
</head>
<body>
<header>
    <div class="ssheader">
        <div class="logo">
            <img src="/ITP122/image/logo.png" alt="Smart Solutions Logo">
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search">
            <img class="search-icon" src="/ITP122/image/search-icon.png" alt="Search" style="width: 20px; height: 20px; cursor: pointer;">
        </div>
        <a href="/ITP122/pages/location.php">
            <div class="location">
                <img class="location" src="/ITP122/image/location-icon.png" alt="location-icon">
            </div>
        </a>
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
                    src="<?php echo isset($_SESSION['user_id']) ? $profile_picture : '../image/login-icon.png'; ?>" 
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
            <a href="../products/desktop.php"style="font-weight: bold;">DESKTOP</a>
            <a href="../products/laptop.php">LAPTOP</a>
            <a href="../pages/brands.php">BRANDS</a>
    </div>

    <div class="breadcrumb">
        <a href="../index.php"><span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px;">home</span>Home</a> > <span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px; color: #0062F6;">computer</span><a>Desktop</a>
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
            <a href="#" class="buy-now-btn" data-id="1" data-name="Core i7 12700 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W" data-price="25195.00" data-image="/ITP122/image/Core_i7.png">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <div class="prebuilt-card">
            <img src="/ITP122/image/Core_i3.png" alt="Intel Core i3-12100 PC">
            <h3>Core i3 12100 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W</h3>
            <p class="desktop-price">₱14,795.00</p>
            <a href="#" class="buy-now-btn" data-id="2" data-name="Core i3 12100 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W" data-price="14795.00" data-image="/ITP122/image/Core_i3.png">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <div class="prebuilt-card">
            <img src="/ITP122/image/Core_i5.png" alt="Intel Core i7-12700 PC">
            <h3>Core i7 12700 / H610 / 16GB DDR4 / 500GB SSD / 550W Power Supply/ PC Case M-ATX</h3>
            <p class="desktop-price">₱25,195.00</p>
            <a href="#" class="buy-now-btn" data-id="1" data-name="Core i7 12700 / H610 / 16GB DDR4 / 500GB SSD / 550W Power Supply/ PC Case M-ATX" data-price="25195.00" data-image="/ITP122/image/Core_i5.png">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <div class="prebuilt-card">
            <img src="/ITP122/image/Ryzen_7.jpg" alt="Ryzen 7 5700G PC">
            <h3>Ryzen 7 5700G / B450M / 16GB DDR4 / 512GB SSD / 550W Power Supply / PC Case M-ATX</h3>
            <p class="desktop-price">₱21,250.00</p>
            <a href="#" class="buy-now-btn" data-id="2" data-name="Ryzen 7 5700G / B450M / 16GB DDR4 / 512GB SSD / 550W Power Supply / PC Case M-ATX" data-price="21250.00" data-image="/ITP122/image/Ryzen_7.jpg">
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
            <a href="#" class="buy-now-btn" data-id="1" data-name="Stratus - Intel i5 12th gen | MSI H610 | Kingston 8gb Memory | 500gb | 700w" data-price="20950.00" data-image="/ITP122/image/i5.png">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <!-- Second Prebuilt PC Card -->
        <div class="custom-card">
            <img src="/ITP122/image/ryzn7.png" alt="Intel Core i3-12100 PC">
            <h3>Cirrus<br>AMD Ryzen 7 5700x | MSI B550 | Zotac Rtx-4060 | Team 16gb Memory | Kingston NVMe 2TB</h3>
            <p class="desktop-price">₱14,795.00</p>
            <a href="#" class="buy-now-btn" data-id="2" data-name="Cirrus - AMD Ryzen 7 5700x | MSI B550 | Zotac Rtx-4060 | Team 16gb Memory | Kingston NVMe 2TB" data-price="14795.00" data-image="/ITP122/image/ryzn7.png">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <div class="custom-card">
            <img src="/ITP122/image/ryzn5.png" alt="Intel Core i7-12700 PC">
            <h3>Cirrostratus<br>AMD Ryzen 5 5600G | Gigabyte B550 | Gigabyte Rx-6600 | Team 16gb Memory</h3>
            <p class="desktop-price">₱25,195.00</p>
            <a href="#" class="buy-now-btn" data-id="1" data-name="Cirrostratus - AMD Ryzen 5 5600G | Gigabyte B550 | Gigabyte Rx-6600 | Team 16gb Memory" data-price="25195.00" data-image="/ITP122/image/ryzn5.png">
                <button class="buy-button2">BUY NOW</button>
            </a>
        </div>

        <div class="custom-card">
            <img src="/ITP122/image/ryzen5.png" alt="Ryzen 7 5700G PC">
            <h3>Cumulus<br>AMD Ryzen 5 Pro 4650G | Asrock A320 | Team Elite Plus 8gb Memory | Team 240gb</h3>
            <p class="desktop-price">₱21,250.00</p>
            <a href="#" class="buy-now-btn" data-id="2" data-name="Cumulus - AMD Ryzen 5 Pro 4650G | Asrock A320 | Team Elite Plus 8gb Memory | Team 240gb" data-price="21250.00" data-image="/ITP122/image/ryzen5.png">
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
                window.location.href = '../checkout.php';
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
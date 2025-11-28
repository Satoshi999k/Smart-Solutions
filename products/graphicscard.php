<?php
// Start session to check if the user is logged in
session_start();

// Initialize cart from database
require_once 'init_cart.php';

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from database
$result = $conn->query("SELECT id, name, price, image, stock FROM products WHERE LOWER(category) LIKE '%graphics%' OR LOWER(category) LIKE '%video%' ORDER BY id DESC");
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
            // Check if it's a full URL (Google image, etc.) or relative path
            if (strpos($row['profile_picture'], 'http') === 0) {
                $profile_picture = $row['profile_picture']; // Use URL as-is
            } else {
                // Use relative path
                $profile_picture = $row['profile_picture']; 
            }
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
<link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="image/x-icon">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/design.css" />
<link rel="stylesheet" href="../css/animations.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<meta charset="UTF-8">
<title>Graphics Card - SMART SOLUTIONS COMPUTER SHOP</title>
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
    
    /* Modern Dropdown Styling */
    .profile-dropdown { position: relative; display: inline-block; }
    .dropdown-content { display: none; position: absolute; top: 110%; right: 0; background: white; border-radius: 8px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); border: 1px solid #e0e0e0; min-width: 200px; z-index: 1000; }
    .dropdown-content a { display: flex; align-items: center; padding: 12px 16px; color: #333; font-size: 14px; font-weight: 500; text-decoration: none; transition: all 0.2s ease; border-left: 3px solid transparent; }
    .dropdown-content a:hover { background: #f5f5f5; color: #0062F6; border-left-color: #0062F6; }
    .profile-dropdown.active .dropdown-content { display: block; }
    
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideInDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
    @media (max-width: 768px) { .processor-section h2 { font-size: 28px; margin-bottom: 30px; } .product-grid { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; padding: 20px 16px; } .product-card { padding: 16px; } .product-card img { height: 140px; } }
    @media (max-width: 480px) { .processor-section { padding: 24px 16px; } .processor-section h2 { font-size: 24px; margin-bottom: 24px; } .product-grid { grid-template-columns: 1fr; gap: 12px; padding: 16px; } .product-card { padding: 12px; } .product-card img { height: 120px; } .button-group { gap: 8px; } }
</style>
<?php
// Display alert if one is set
if (isset($_SESSION['alert'])) {
    $alert = $_SESSION['alert'];
    echo "<script>
        window.addEventListener('load', function() {
            Swal.fire({
                title: '" . addslashes($alert['title']) . "',
                text: '" . addslashes($alert['message']) . "',
                icon: '" . $alert['type'] . "',
                confirmButtonColor: '#0062F6',
                confirmButtonText: 'OK'
            });
        });
    </script>";
    unset($_SESSION['alert']);
}
?>
</head>
<body>
<header>
    <div class="ssheader">
        <div class="logo">
            <img src="../image/logo.png" alt="Smart Solutions Logo">
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search">
            <img class="search-icon" src="../image/search-icon.png" alt="Search" style="width: 20px; height: 20px; cursor: pointer;">
        </div>
        <a href="../pages/location.php">
            <div class="location">
                <img class="location" src="../image/location-icon.png" alt="location-icon">
            </div>
        </a>
        <div class="track">
            <a href="../pages/track.php"><img class="track" src="../image/track-icon.png" alt="track-icon"></a>
        </div>
        <a href="../cart.php">
            <div class="cart">
                <img class="cart" src="../image/cart-icon.png" alt="cart-icon" style="width: 35px; height: auto;">
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
            <a href="javascript:void(0)" onclick="toggleDropdown(event)">
                <!-- Check if user is logged in, if yes show profile picture, else show login icon -->
                <img class="login" 
                    src="<?php echo isset($_SESSION['user_id']) ? $profile_picture : '/ITP122/image/login-icon.png'; ?>" 
                    alt="login-icon" 
                    style="border-radius: <?php echo isset($_SESSION['user_id']) ? '50%' : '0'; ?>; 
                            width: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>; 
                            height: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>;">
            </a>
            <div id="dropdown-menu" class="dropdown-content">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="../user/profile.php">
                        <i class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 8px; display: inline-block;">person</i>
                        View Profile
                    </a>
                    <a href="../user/edit-profile.php">
                        <i class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 8px; display: inline-block;">edit</i>
                        Edit Profile
                    </a>
                    <a href="../user/logout.php">
                        <i class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 8px; display: inline-block;">logout</i>
                        Log Out
                    </a>
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

    <div class="menu" id="main-menu">
            <a href="../index.php">HOME</a>
            <a href="../pages/product.php">PRODUCTS</a>
            <a href="desktop.php">DESKTOP</a>
            <a href="laptop.php">LAPTOP</a>
            <a href="../pages/brands.php">BRANDS</a>
    </div>

<div class="breadcrumb">
    <a href="../index.php"><i class="material-icons" style="font-size: 16px; vertical-align: middle; margin-right: 4px;">home</i>Home</a> > 
    <a><i class="material-icons" style="font-size: 16px; vertical-align: middle; margin-right: 4px;">videogame_asset</i>Graphics Card</a>
</div>

<div class="processor-section">
    <h2>GRAPHICS CARD</h2>
</div>

<div class="product-grid">
    <?php
    // Database query already loaded at top
    // $products variable populated from database query
    if (!isset($products) || count($products) === 0) {
        // Fallback if query failed
        $products = [
        ["id" => 35, "name" => "MSI NVIDIA® GeForce RTX 3060 Ventus 2X OC 12gb 192bit GDdr6 Gaming Videocard LHR", "price" => 31595.00, "image" => "/ITP122/image/MSI_RTX_3060_Ventus.png", "stock" => 0],
        ];
    }

    foreach ($products as $product) {
        ?>
        <div class='product-card'>
            <img src='<?php echo $product['image']; ?>' alt='<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>'>
            <h4><?php echo $product['name']; ?></h4>
            <p>₱<?php echo number_format($product['price'], 2); ?></p>
            <p style="font-size: 12px; color: <?php echo ($product['stock'] > 5) ? '#4caf50' : ($product['stock'] > 0 ? '#ff9800' : '#f44336'); ?>;">
                <?php echo ($product['stock'] > 0) ? 'In Stock: ' . $product['stock'] : 'Out of Stock'; ?>
            </p>
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
    // Modern dropdown toggle with smooth transitions
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
        
        if (!productId) return;
        
        // Send only product_id to set_buynow_product.php
        // The backend will fetch all product details from database
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', 1);
        
        fetch('../set_buynow_product.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = '../pages/checkout.php';
            } else {
                console.error('Error:', data.message);
                alert('Failed to process buy now request');
            }
        })
        .catch(err => console.log(err));
    });
</script>
<script src="js/search.js"></script>
<script src="../js/jquery-animations.js"></script>
<script src="../js/ajax-cart-clean.js"></script>
</body>
</html>

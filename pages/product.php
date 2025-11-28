<?php
// Start session to check if the user is logged in
session_start();

// Initialize cart from database
require_once '../includes/init_cart.php';

// Database connection 
$conn = new mysqli("localhost", "root", "", "smartsolutions");

// Check if logged in
$profile_picture = "../image/login-icon.png"; 
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
                // Add ../ prefix for relative paths since we're in pages/ subfolder
                $profile_picture = "../" . $row['profile_picture']; 
            }
        }
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/design.css" />
<link rel="stylesheet" href="../css/animations.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
<title>PRODUCTS - SMARTSOLUTIONS</title>
<style>
    body {
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%) !important;
    }

    .breadcrumb {
        padding: 16px 24px;
        font-size: 14px;
        color: #555;
        background: transparent;
    }

    .breadcrumb a {
        color: #0062F6;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .breadcrumb a:hover {
        color: #0052D4;
    }

    .product-category {
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        padding: 40px 24px;
        min-height: 100vh;
    }

    .product-category h1 {
        text-align: center;
        font-size: 36px;
        color: #0062F6;
        margin-bottom: 40px;
        font-weight: 700;
        animation: slideInDown 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        letter-spacing: 1px;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 24px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .product-item {
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        border: 2px solid #e8f1ff;
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 4px 16px rgba(0, 98, 246, 0.08);
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
    }

    .product-item:nth-child(1) { animation-delay: 0.1s; }
    .product-item:nth-child(2) { animation-delay: 0.2s; }
    .product-item:nth-child(3) { animation-delay: 0.3s; }
    .product-item:nth-child(4) { animation-delay: 0.4s; }
    .product-item:nth-child(5) { animation-delay: 0.5s; }
    .product-item:nth-child(6) { animation-delay: 0.6s; }
    .product-item:nth-child(7) { animation-delay: 0.7s; }
    .product-item:nth-child(8) { animation-delay: 0.8s; }
    .product-item:nth-child(9) { animation-delay: 0.9s; }
    .product-item:nth-child(10) { animation-delay: 1s; }
    .product-item:nth-child(11) { animation-delay: 1.1s; }
    .product-item:nth-child(12) { animation-delay: 1.2s; }

    .product-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(0, 98, 246, 0.1), transparent);
        transition: left 0.6s ease;
    }

    .product-item:hover::before {
        left: 100%;
    }

    .product-item:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 12px 40px rgba(0, 98, 246, 0.2);
        border-color: #0062F6;
        background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%);
    }

    .product-item img {
        max-width: 100%;
        height: 160px;
        object-fit: contain;
        transition: all 0.4s ease;
        filter: drop-shadow(0 2px 8px rgba(0, 98, 246, 0.15));
    }

    .product-item:hover img {
        filter: drop-shadow(0 8px 20px rgba(0, 98, 246, 0.3));
        transform: scale(1.1);
    }

    .product-item a {
        display: block;
        text-decoration: none;
        color: inherit;
    }

    @keyframes slideInDown {
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
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .product-category h1 {
            font-size: 28px;
            margin-bottom: 30px;
        }

        .product-grid {
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
        }

        .product-item {
            padding: 16px;
        }

        .product-item img {
            height: 120px;
        }
    }

    @media (max-width: 480px) {
        .product-category {
            padding: 24px 16px;
        }

        .product-category h1 {
            font-size: 24px;
            margin-bottom: 24px;
        }

        .product-grid {
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
        }

        .product-item {
            padding: 12px;
        }

        .product-item img {
            height: 100px;
        }
    }

    /* Modern Dropdown Styling */
    .profile-dropdown { position: relative; display: inline-block; }
    .dropdown-content { display: none; position: absolute; top: 110%; right: 0; background: white; border-radius: 8px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); border: 1px solid #e0e0e0; min-width: 200px; z-index: 1000; }
    .dropdown-content a { display: flex; align-items: center; padding: 12px 16px; color: #333; font-size: 14px; font-weight: 500; text-decoration: none; transition: all 0.2s ease; border-left: 3px solid transparent; }
    .dropdown-content a:hover { background: #f5f5f5; color: #0062F6; border-left-color: #0062F6; }
    .profile-dropdown.active .dropdown-content { display: block; }
</style>
</head>
<body>
<header>
    <div class="ssheader">
        <div class="logo">
            <img src="../image/logo.png" alt="Smart Solutions Logo">
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search">
            <div class="search-icon">
                <img src="../image/search-icon.png" alt="Search Icon">
            </div>
        </div>
        <a href="location.php">
            <div class="location">
                <img class="location" src="../image/location-icon.png" alt="location-icon">
            </div>
        </a>
        <div class="track">
            <a href="track.php"><img class="track" src="../image/track-icon.png" alt="track-icon"></a>
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
            <a href="javascript:void(0)" onclick="toggleDropdown(event)">
                <img class="login" 
                    src="<?php echo isset($_SESSION['user_id']) ? $profile_picture : '../image/login-icon.png'; ?>" 
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
                    <a href="profile.php"></a>
                <?php else: ?>
                    <a href="../user/register.php"><p>Login/<br>Sign In</p></a>
                <?php endif; ?>
            </div>
    </div>
    </header>
    <div class="menu">
            <a href="../index.php">HOME</a>
            <a href="../pages/product.php" style="font-weight: bold;">PRODUCTS</a>
            <a href="../products/desktop.php">DESKTOP</a>
            <a href="../products/laptop.php">LAPTOP</a>
            <a href="../pages/brands.php">BRANDS</a>
    </div>

    <div class="breadcrumb">
            <a href="../index.php"><i class="material-icons" style="font-size: 16px; vertical-align: middle; margin-right: 4px;">home</i>Home</a> > 
            <a><i class="material-icons" style="font-size: 16px; vertical-align: middle; margin-right: 4px;">category</i>Product Category</a>
    </div>
    <div class="product-category">
        <h1>PRODUCT CATEGORY</h1>
        <div class="product-grid">
      
            <div class="product-item">
                <a href="../products/processor.php">
                    <img src="../image/processor.png" alt="Processor">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/motherboard.php">
                    <img src="../image/motherboard.png" alt="Motherboard">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/graphicscard.php">
                    <img src="../image/graphicscard.png" alt="Graphics Card">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/memory.php">
                    <img src="../image/memory.png" alt="Memory">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/ssd.php">
                    <img src="../image/ssd.png" alt="Solid State Drive">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/powersupply.php">
                    <img src="../image/powersupply.png" alt="Power Supply">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/pccase.php">
                    <img src="../image/pccase.png" alt="PC Case">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/laptop.php">
                    <img src="../image/laptop.png" alt="Laptop">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/monitor.php">
                    <img src="../image/monitor.png" alt="Monitor">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/keyboard.php">
                    <img src="../image/keyboard.png" alt="Keyboard">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/mouse.php">
                    <img src="../image/mouse.png" alt="Mouse">
                </a>
            </div>
            <div class="product-item">
                <a href="../products/headset.php">
                    <img src="../image/headset.png" alt="Headset">
                </a>
            </div>
        </div>
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
            <img src="../image/logo.png" alt="Company Logo">
            <div class="social-media">
                <a href="https://web.facebook.com/smartsolutionscomputershop.ph" target="_blank">
                    <img src="../image/facebook.png" alt="Facebook">
                </a>
                <a href="https://www.instagram.com/smartsolutions.ph/" target="_blank">
                    <img src="../image/instagram.png" alt="Instagram">
                </a>
                <a href="https://www.tiktok.com/@smrtsolutioncomputershop" target="_blank">
                    <img src="../image/tiktok.png" alt="Tiktok">
                </a>
                <a href="https://www.linkedin.com/in/smart-solutions-40b512339/" target="_blank">
                    <img src="../image/linkedin.png" alt="LinkedIn">
                </a>
                <a href="https://www.youtube.com/@SmartSolutions-o4j/" target="_blank">
                    <img src="../image/youtube.png" alt="YouTube">
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
</script>
<script src="../js/search.js"></script>
<script src="../js/header-animation.js"></script>
</body>
</html>
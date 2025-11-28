<?php
// Start session to check if the user is logged in
session_start();

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
            $profile_picture = "../" . $row['profile_picture']; 
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
<link rel="stylesheet" href="../css/design.css" />
<link rel="stylesheet" href="../css/animations.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STORE LOCATION - SMARTSOLUTIONS</title>
    <style>
        body { background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%) !important; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .breadcrumb { padding: 18px 32px; font-size: 14px; color: #666; background: transparent; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .breadcrumb a { color: #0062F6; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 6px; }
        .breadcrumb a:hover { color: #0052D4; transform: translateX(4px); }
        .breadcrumb .material-icons { font-size: 20px; vertical-align: middle; }
        .breadcrumb span:not(.material-icons) { display: inline-flex; align-items: center; }
                .processor-section h2 {
            font-size: 48px;
            font-weight: 800;
            text-align: center;
            margin: 50px 0 20px;
            color: #1a1a1a;
            letter-spacing: -1.5px;
            animation: slideInDown 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .processor-section {
            margin-bottom: 20px;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .store-section {
            max-width: 950px;
            margin: 0 auto 50px;
            padding: 50px 40px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            border-radius: 16px;
            border-left: 6px solid #0062F6;
            box-shadow: 0 10px 40px rgba(0, 98, 246, 0.15);
            animation: fadeInUp 0.7s ease-out 0.2s both;
            position: relative;
            overflow: hidden;
        }

        .store-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(0, 98, 246, 0.05) 0%, transparent 70%);
            pointer-events: none;
        }

        .store-section p {
            margin: 18px 0;
            font-size: 17px;
            line-height: 1.9;
            color: #4a4a4a;
            margin-left: 0;
            margin-right: 0;
            font-weight: 500;
            letter-spacing: 0.3px;
            position: relative;
            z-index: 1;
        }

        .store-section p:first-child {
            font-size: 18px;
            color: #0062F6;
            font-weight: 600;
        }

        .location-details {
            max-width: 950px;
            margin: 50px auto;
            padding: 50px;
            background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%);
            border-radius: 16px;
            box-shadow: 0 15px 50px rgba(0, 98, 246, 0.2);
            text-align: center;
            animation: fadeInUp 0.7s ease-out 0.4s both;
            border-top: 6px solid #0062F6;
            position: relative;
            overflow: hidden;
        }

        .location-details::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -50%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(0, 98, 246, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .location-details h3 {
            font-size: 32px;
            font-weight: 800;
            color: #0062F6;
            margin-bottom: 24px;
            letter-spacing: 0.5px;
            position: relative;
            z-index: 2;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .location-details h3 i {
            font-size: 36px;
            color: #0062F6;
        }

        .location-details p {
            font-size: 17px;
            line-height: 2;
            color: #555;
            margin: 12px 0;
            margin-left: 0;
            margin-right: 0;
            font-weight: 500;
            position: relative;
            z-index: 2;
        }

        .location-address-box {
            background: linear-gradient(135deg, #f5f9ff 0%, #eef3ff 100%);
            padding: 24px 30px;
            border-radius: 12px;
            margin-top: 24px;
            border-left: 4px solid #0062F6;
            position: relative;
            z-index: 2;
        }

        .location-address-box p {
            margin: 8px 0;
            color: #333;
            font-weight: 600;
        }

        .map-container1 {
            width: 100%;
            max-width: 1000px;
            margin: 50px auto;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 98, 246, 0.2);
            animation: fadeInUp 0.7s ease-out 0.6s both;
            position: relative;
        }

        .map-container1::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 98, 246, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 10;
            border-radius: 16px;
        }

        .map-container1 iframe {
            width: 100%;
            height: 550px;
            border: none;
            display: block;
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .map-container1:hover iframe {
            box-shadow: inset 0 0 0 3px rgba(0, 98, 246, 0.2);
        }

        .store-info-grid {
            max-width: 950px;
            margin: 50px auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            animation: fadeInUp 0.7s ease-out 0.5s both;
        }

        .info-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            border-top: 4px solid #0062F6;
            position: relative;
            overflow: hidden;
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 98, 246, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .info-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 98, 246, 0.2);
        }

        .info-card:hover::before {
            left: 100%;
        }

        .info-card-icon {
            font-size: 48px;
            margin-bottom: 16px;
            display: block;
            color: #0062F6;
        }

        .info-card-icon i {
            font-size: 48px;
        }

        .info-card h4 {
            font-size: 20px;
            font-weight: 700;
            color: #0062F6;
            margin-bottom: 12px;
            letter-spacing: 0.3px;
        }

        .info-card p {
            font-size: 15px;
            color: #666;
            line-height: 1.7;
            margin: 0;
        }

        @media (max-width: 768px) {
            .processor-section h2 {
                font-size: 32px;
                margin: 40px 20px 20px;
            }

            .store-section {
                padding: 32px 20px;
                margin: 0 20px 40px;
            }

            .location-details {
                padding: 32px 20px;
                margin: 40px 20px;
            }

            .location-details h3 {
                font-size: 24px;
            }

            .map-container1 {
                margin: 40px 20px;
            }

            .map-container1 iframe {
                height: 380px;
            }

            .store-info-grid {
                margin: 40px 20px;
                gap: 16px;
            }

            .info-card {
                padding: 24px;
            }

            .info-card-icon {
                font-size: 36px;
            }

            .info-card h4 {
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {
            .processor-section h2 {
                font-size: 26px;
            }

            .store-section p {
                font-size: 15px;
            }

            .location-details p {
                font-size: 15px;
            }

            .map-container1 iframe {
                height: 300px;
            }
        }
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
            <img class="search-icon" src="../image/search-icon.png" alt="Search" style="width: 20px; height: 20px; cursor: pointer;">
        </div>
        <a href="location.php">
            <div class="location">
                <img class="location" src="../image/location-icon.png" alt="location-icon">
            </div>
        </a>
        <div class="track">
            <a href="track.php"><img class="track" src="../image/track-icon.png" alt="track-icon">
        </a>
        </div>
        <a href="../cart.php">
            <div class="cart">
                <img class="cart" src="../image/cart-icon.png" alt="cart-icon" style="width: 35px; height: auto;">
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
            <a href="../products/desktop.php">DESKTOP</a>
            <a href="../products/laptop.php">LAPTOP</a>
            <a href="../brands.php">BRANDS</a>
    </div>

    <div class="breadcrumb">
            <a href="../index.php"><span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px;">home</span>Home</a> > <span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px; color: #0062F6;">location_on</span><a>Store Location</a>
    </div>

    <div class="processor-section">
    <h2>STORE LOCATION</h2>
    </div>
        <div class="store-section">
            <p>Our store is conveniently located to serve you better. We aim to make it easy for you to find the perfect computer or electronics to meet your needs.
            </p>
            <p>Visit of our store location today and experience the Smart Solutions difference for yourself!
            </p>
        </div>

        <div class="location-details">
            <h3><i class="material-icons">location_on</i>SMART SOLUTIONS COMPUTER SHOP</h3>
            <div class="location-address-box">
                <p><i class="material-icons" style="font-size: 20px; vertical-align: middle; margin-right: 8px; color: #0062F6;">home</i>Phase 1 Martinez Subdivision<br>
                City of Mati<br>
                Davao Oriental, Mindanao, Philippines</p>
            </div>
        </div>

        <div class="store-info-grid">
            <div class="info-card">
                <span class="info-card-icon"><i class="material-icons">store</i></span>
                <h4>Visit Us</h4>
                <p>Come experience our wide selection of computers and electronics in our modern store.</p>
            </div>
            <div class="info-card">
                <span class="info-card-icon"><i class="material-icons">schedule</i></span>
                <h4>Store Hours</h4>
                <p>Monday - Sunday<br>Available for your convenient shopping experience.</p>
            </div>
            <div class="info-card">
                <span class="info-card-icon"><i class="material-icons">phone</i></span>
                <h4>Contact Us</h4>
                <p>Reach out to us for any inquiries or assistance with your tech needs.</p>
            </div>
        </div>

        <div class="map-container1">
        <iframe
            src="https://www.google.com/maps?q=6.942242,126.247245&hl=es;z=14&output=embed"
            width="80%"
            height="450"
            style="border:0;"
            allowfullscreen=""
            loading="lazy">
        </iframe>
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
</script>
<script src="js/search.js"></script>
<script src="../js/jquery-animations.js"></script>
<script src="../js/header-animation.js"></script>
</body>
</html>

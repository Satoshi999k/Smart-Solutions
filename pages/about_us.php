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

// Database connection (replace with your credentials)
$conn = new mysqli("localhost", "root", "", "smartsolutions");

// Check if logged in
$profile_picture = "../image/login-icon.png"; // Default login icon
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
            $profile_picture = "../" . $row['profile_picture']; // Use user's profile picture
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
<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
<link rel="stylesheet" href="../css/design.css" />
<link rel="stylesheet" href="../css/animations.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
<meta charset="UTF-8">
    <title>ABOUT US - SMARTSOLUTIONS</title>
    <style>
        * { box-sizing: border-box; }
        body { background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%) !important; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .breadcrumb { padding: 18px 32px; font-size: 14px; color: #666; background: transparent; }
        .breadcrumb a { color: #0062F6; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 6px; }
        .breadcrumb a:hover { color: #0052D4; transform: translateX(4px); }
        .about-intro { background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); color: #0062F6; padding: 100px 40px; text-align: center; animation: slideInDown 0.8s cubic-bezier(0.34, 1.56, 0.64, 1); position: relative; overflow: hidden; }
        .about-intro::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #0062F6 0%, #0052D4 50%, #00a8ff 100%); }
        .about-intro::after { content: ''; position: absolute; bottom: 0; right: -20%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(0, 98, 246, 0.06) 0%, transparent 70%); border-radius: 50%; pointer-events: none; }
        .about-intro h1 { font-size: 58px; font-weight: 900; margin-bottom: 24px; letter-spacing: 3px; color: #0062F6; background: linear-gradient(135deg, #0062F6 0%, #0052D4 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; position: relative; z-index: 1; }
        .about-intro p { font-size: 18px; line-height: 1.9; max-width: 950px; margin: 0 auto; color: #2c3e50; font-weight: 500; position: relative; z-index: 1; }
        .section { background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); padding: 70px 40px; margin: 0; text-align: center; position: relative; overflow: hidden; }
        .section::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent 0%, #0062F6 50%, transparent 100%); }
        .section::after { content: ''; position: absolute; top: 60px; left: 50%; transform: translateX(-50%); width: 80px; height: 5px; background: linear-gradient(90deg, #0062F6 0%, #0052D4 50%, #00a8ff 100%); border-radius: 20px; }
        .section h2 { font-size: 48px; color: #0062F6; margin-bottom: 48px; margin-top: 20px; font-weight: 900; animation: slideInDown 0.8s cubic-bezier(0.34, 1.56, 0.64, 1); letter-spacing: 2px; background: linear-gradient(135deg, #0062F6 0%, #0052D4 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .section p { font-size: 17px; color: #2c3e50; line-height: 1.95; margin: 0 auto 0px; max-width: 1050px; background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 251, 255, 0.9) 100%); padding: 40px 44px; border-radius: 18px; box-shadow: 0 12px 48px rgba(0, 98, 246, 0.12), 0 2px 8px rgba(0, 0, 0, 0.04); border: 2px solid rgba(0, 98, 246, 0.15); animation: fadeInUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; opacity: 0; transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); position: relative; overflow: hidden; font-weight: 500; }
        .section p::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #0062F6 0%, #0052D4 50%, #00a8ff 100%); transform: scaleX(0); transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .section p:hover::before { transform: scaleX(1); }
        .section p:hover { transform: translateY(-12px) scale(1.01); box-shadow: 0 24px 64px rgba(0, 98, 246, 0.22), 0 4px 16px rgba(0, 0, 0, 0.1); border-color: rgba(0, 98, 246, 0.4); background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%); }
        .section ul { list-style: none; padding: 0; max-width: 1050px; margin: 0 auto; display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
        .section ul li { font-size: 17px; color: #2c3e50; line-height: 1.8; padding: 32px 32px 32px 60px; background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); border-radius: 16px; box-shadow: 0 10px 36px rgba(0, 98, 246, 0.1), inset 0 1px 2px rgba(255, 255, 255, 0.6); border-left: 6px solid #0062F6; transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); position: relative; font-weight: 500; }
        .section ul li::before { content: ''; position: absolute; left: 16px; top: 50%; transform: translateY(-50%); width: 24px; height: 24px; background: linear-gradient(135deg, #0062F6 0%, #0052D4 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; color: white; }
        .section ul li::after { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(0, 98, 246, 0.03) 0%, transparent 100%); border-radius: 16px; pointer-events: none; }
        .section ul li:hover { transform: translateY(-12px) translateX(12px) scale(1.02); box-shadow: 0 20px 50px rgba(0, 98, 246, 0.25), inset 0 1px 2px rgba(255, 255, 255, 0.8); border-left-color: #0052D4; background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%); }
        .section ul li:nth-child(1) { animation: fadeInUp 0.8s ease-out 0.1s forwards; opacity: 0; }
        .section ul li:nth-child(2) { animation: fadeInUp 0.8s ease-out 0.2s forwards; opacity: 0; }
        .section ul li:nth-child(3) { animation: fadeInUp 0.8s ease-out 0.3s forwards; opacity: 0; }
        .section ul li:nth-child(4) { animation: fadeInUp 0.8s ease-out 0.4s forwards; opacity: 0; }
        .developers { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 36px; max-width: 1300px; margin: 70px auto 0; padding: 70px 40px; background: linear-gradient(135deg, rgba(0, 98, 246, 0.08) 0%, rgba(0, 98, 246, 0.03) 100%); border-radius: 28px; border: 2px solid rgba(0, 98, 246, 0.15); position: relative; overflow: hidden; }
        .developers::before { content: ''; position: absolute; top: -50%; right: -20%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(0, 98, 246, 0.1) 0%, transparent 70%); border-radius: 50%; pointer-events: none; }
        .developer { background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); border-radius: 24px; padding: 0; text-align: center; box-shadow: 0 16px 48px rgba(0, 98, 246, 0.14), 0 4px 12px rgba(0, 0, 0, 0.06); border: 2px solid rgba(0, 98, 246, 0.12); animation: fadeInUp 0.8s ease-out forwards; opacity: 0; transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); overflow: hidden; position: relative; z-index: 1; }
        .developer::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(90deg, #0062F6 0%, #0052D4 50%, #00a8ff 100%); }
        .developer:nth-child(1) { animation-delay: 0.15s; }
        .developer:nth-child(2) { animation-delay: 0.28s; }
        .developer:nth-child(3) { animation-delay: 0.41s; }
        .developer:hover { transform: translateY(-24px) scale(1.04); box-shadow: 0 32px 80px rgba(0, 98, 246, 0.3), 0 8px 20px rgba(0, 0, 0, 0.1); border-color: #0062F6; }
        .developer img { max-width: 100%; height: 220px; width: 220px; object-fit: cover; display: block; margin: 28px auto 24px; border-radius: 50%; border: 6px solid rgba(0, 98, 246, 0.25); filter: brightness(1.02) saturate(1.15) contrast(1.05); transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); box-shadow: 0 10px 30px rgba(0, 98, 246, 0.2); }
        .developer:hover img { filter: brightness(1.2) saturate(1.4) contrast(1.1); border-color: #0062F6; box-shadow: 0 0 50px rgba(0, 98, 246, 0.6); transform: scale(1.08); }
        .developer-info { padding: 16px 24px 28px 24px; position: relative; z-index: 1; min-height: 140px; display: flex; flex-direction: column; justify-content: space-between; }
        .developer p { color: #2c3e50; margin: 8px 0; font-weight: 700; }
        .developer p:first-of-type { font-size: 18px; color: #0062F6; margin-bottom: 6px; background: linear-gradient(135deg, #0062F6 0%, #0052D4 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; white-space: nowrap; }
        .developer p:last-of-type { font-size: 12px; color: #0052D4; text-transform: uppercase; letter-spacing: 2px; font-weight: 800; }
        .developer-social-media { display: flex; justify-content: center; gap: 20px; margin-top: 20px; }
        .developer-social-media a { display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; background: linear-gradient(135deg, #0062F6 0%, #0052D4 100%); border-radius: 50%; transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); box-shadow: 0 10px 24px rgba(0, 98, 246, 0.3); border: 3px solid rgba(0, 98, 246, 0.2); position: relative; overflow: hidden; }
        .developer-social-media a::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, transparent 100%); }
        .developer-social-media a:hover { transform: scale(1.3) rotate(12deg); box-shadow: 0 16px 40px rgba(0, 98, 246, 0.5); border-color: #00a8ff; }
        .developer-social-media a img { height: 26px; width: 26px; object-fit: contain; filter: brightness(0) invert(1); margin: 0; position: relative; z-index: 1; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideInDown { from { opacity: 0; transform: translateY(-40px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 768px) { .about-intro { padding: 60px 24px; } .about-intro h1 { font-size: 40px; letter-spacing: 1px; } .about-intro p { font-size: 16px; } .section { padding: 50px 20px; } .section h2 { font-size: 36px; margin-bottom: 36px; } .section p { font-size: 16px; padding: 30px 28px; } .section ul { grid-template-columns: 1fr; gap: 18px; } .section ul li { padding: 24px; font-size: 15px; } .developers { grid-template-columns: 1fr; gap: 30px; padding: 50px 24px; } .developer img { height: 220px; width: 220px; margin: 28px auto 24px; } .developer-info { padding: 8px 24px 28px 24px; } .developer p:first-of-type { font-size: 20px; } .developer-social-media { gap: 16px; margin-top: 16px; } .developer-social-media a { width: 48px; height: 48px; } .developer-social-media a img { height: 24px; width: 24px; } }
        @media (max-width: 480px) { .about-intro { padding: 40px 16px; } .about-intro h1 { font-size: 28px; } .about-intro p { font-size: 14px; } .section { padding: 40px 16px; } .section h2 { font-size: 26px; margin-bottom: 28px; } .section p { font-size: 15px; padding: 22px 20px; } .section ul { grid-template-columns: 1fr; gap: 14px; } .section ul li { padding: 20px; font-size: 13px; } .developers { grid-template-columns: 1fr; gap: 20px; padding: 30px 16px; border-radius: 20px; } .developer { border-radius: 16px; } .developer img { height: 200px; width: 200px; margin: 24px auto 20px; } .developer-info { padding: 8px 18px 24px 18px; } .developer p:first-of-type { font-size: 18px; } .developer-social-media { gap: 12px; margin-top: 14px; } .developer-social-media a { width: 44px; height: 44px; } .developer-social-media a img { height: 22px; width: 22px; } }
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
    </div>
    </header>

    <div class="menu">
            <a href="../index.php">HOME</a>
            <a href="../pages/product.php">PRODUCTS</a>
            <a href="../products/desktop.php">DESKTOP</a>
            <a href="../products/laptop.php">LAPTOP</a>
            <a href="../pages/brands.php">BRANDS</a>
    </div>

    <div class="breadcrumb">
        <a href="../index.php"><span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px;">home</span>Home</a> > <span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px; color: #0062F6;">info</span><a>About Us</a>
    </div>

    <div class="about-intro">
        <h1>ABOUT SMART SOLUTIONS</h1>
        <p>We are a leading provider of innovative technology solutions designed to make your life easier. Our focus is on delivering cutting-edge products that enhance performance, reliability, and usability.</p>
    </div>

    <div class="section">
        <h2>Our History</h2>
        <p>Smart Solutions was founded in 2015 with a vision to revolutionize technology accessibility for individuals and businesses. Starting as a small computer shop, we have grown into a leading tech solutions provider, catering to diverse needs with our innovative products and excellent service.</p>
    </div>

        <div class="section">
            <h2>Our Mission</h2>
            <p>At Smart Solutions, our mission is to empower our customers with high-quality tech products and excellent service. We aim to become a trusted partner by providing solutions that drive efficiency, productivity, and growth.</p>
        </div>

        <div class="section">
            <h2>What We Offer</h2>
            <p>We offer a wide range of products, from high-performance desktops and laptops to customizable PC builds and accessories. Our commitment to quality and customer satisfaction sets us apart in the industry.</p>
        </div>

        <div class="section">
            <h2>Why Choose Us?</h2>
            <ul>
                <li><span class="material-icons" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 24px; color: white;">check_circle</span>High-quality products at competitive prices</li>
                <li><span class="material-icons" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 24px; color: white;">check_circle</span>Exceptional customer support and service</li>
                <li><span class="material-icons" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 24px; color: white;">check_circle</span>Innovative solutions to meet diverse needs</li>
                <li><span class="material-icons" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 24px; color: white;">check_circle</span>Fast delivery and reliable warranty services</li>
            </ul>
        </div>

        <div class="section">
    <h2>Meet Our Developers</h2>
    <div class="developers">
        <div class="developer">
            <img src="../image/Marlo.jpg" alt="Marlo C. Bohol">
            <div class="developer-info">
                <p>Marlo C. Bohol</p>
                <p>CEO AND FOUNDER</p>
                <div class="developer-social-media">
                    <a href="https://web.facebook.com/olram.shouko" target="_blank">
                        <img src="../image/facebook-logo.png" alt="Facebook">
                    </a>
                    <a href="https://www.instagram.com/mrlocbnsbhl_/" target="_blank">
                        <img src="../image/instagram-logo.png" alt="Instagram">
                    </a>
                    <a href="https://www.linkedin.com/in/marlo-bohol-041989338/" target="_blank">
                        <img src="../image/linkedin-logo.png" alt="LinkedIn">
                    </a>
                </div>
            </div>
        </div>
        <div class="developer">
            <img src="../image/Earl.jpg" alt="Earl Andre V. Galacio">
            <div class="developer-info">
                <p>Earl Andre V. Galacio</p>
                <p>CO-FOUNDER</p>
                <div class="developer-social-media">
                    <a href="https://web.facebook.com/profile.php?id=100079903104092" target="_blank">
                        <img src="../image/facebook-logo.png" alt="Facebook">
                    </a>
                    <a href="https://www.instagram.com/galacioea?igsh=ajIzbzJsM2h6b2M1" target="_blank">
                        <img src="../image/instagram-logo.png" alt="Instagram">
                    </a>
                    <a href="https://www.linkedin.com/in/earlgalacio" target="_blank">
                        <img src="../image/linkedin-logo.png" alt="LinkedIn">
                    </a>
                </div>
            </div>
        </div>
        <div class="developer">
            <img src="../image/Kenneth.jpg" alt="Kenneth D. Semorlan">
            <div class="developer-info">
                <p>Kenneth D. Semorlan</p>
                <p>PROJECT MANAGER</p>
                <div class="developer-social-media">
                    <a href="https://web.facebook.com/kenneth.semorlan" target="_blank">
                        <img src="../image/facebook-logo.png" alt="Facebook">
                    </a>
                    <a href="https://www.instagram.com/zenpai_kenzi/?hl=en-gb" target="_blank">
                        <img src="../image/instagram-logo.png" alt="Instagram">
                        </a>
                    <a href="https://www.linkedin.com/in/kenneth-semorlan-6b7735337/" target="_blank">
                        <img src="../image/linkedin-logo.png" alt="LinkedIn">
                    </a>
                </div>
            </div>
        </div>
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
<script src="js/search.js"></script>
<script src="../js/jquery-animations.js"></script>
<script src="../js/header-animation.js"></script>
</body>
</html>
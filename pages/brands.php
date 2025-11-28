<?php
// Start session to check if the user is logged in
session_start();

// Database connection (replace with your credentials)
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
<html lang="en">
<head>
<link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/design.css" />
<link rel="stylesheet" href="../css/animations.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
<title>BRANDS - SMART SOLUTIONS</title>
<style>
    body.brands { background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%) !important; }
    .breadcrumb { padding: 16px 24px; font-size: 14px; color: #555; background: transparent; }
    .breadcrumb a { color: #0062F6; text-decoration: none; font-weight: 500; transition: color 0.3s ease; }
    .breadcrumb a:hover { color: #0052D4; }
    .brands-intro { background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); padding: 60px 40px; text-align: center; }
    .brands-intro h1 { font-size: 42px; color: #0062F6; margin-bottom: 60px; font-weight: 700; animation: slideInDown 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); letter-spacing: 2px; }
    .brands-intro p { font-size: 15px; color: #2c3e50; line-height: 1.9; margin: 0 auto 0px; max-width: 900px; background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); padding: 32px; border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 98, 246, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04); border: 1px solid rgba(0, 98, 246, 0.1); animation: fadeInUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; opacity: 0; transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); position: relative; overflow: hidden; }
    .brands-intro p::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #0062F6 0%, #0052D4 100%); transform: scaleX(0); transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
    .brands-intro p:hover::before { transform: scaleX(1); }
    .brands-intro p:hover { transform: translateY(-8px); box-shadow: 0 16px 48px rgba(0, 98, 246, 0.15), 0 4px 16px rgba(0, 0, 0, 0.08); border-color: rgba(0, 98, 246, 0.2); background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%); }
    .brands-intro p:nth-of-type(1) { animation-delay: 0.1s; }
    .brands-intro p:nth-of-type(2) { animation-delay: 0.2s; }
    .brands-intro p:nth-of-type(3) { animation-delay: 0.3s; }
    .brands-intro p:nth-of-type(4) { animation-delay: 0.4s; }
    .logo-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 20px; padding: 0px 30px 50px 30px; max-width: 1400px; margin: 0 auto; background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); }
    .logo-grid img { background: white; border: 2px solid #e8f1ff; border-radius: 12px; padding: 16px; height: auto; max-height: 100px; width: 100%; object-fit: contain; transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); box-shadow: 0 4px 16px rgba(0, 98, 246, 0.08); position: relative; overflow: visible; animation: fadeInUp 0.6s ease-out forwards; opacity: 0; }
    .logo-grid img:nth-child(1) { animation-delay: 0.1s; } .logo-grid img:nth-child(2) { animation-delay: 0.15s; } .logo-grid img:nth-child(3) { animation-delay: 0.2s; } .logo-grid img:nth-child(4) { animation-delay: 0.25s; } .logo-grid img:nth-child(5) { animation-delay: 0.3s; } .logo-grid img:nth-child(6) { animation-delay: 0.35s; } .logo-grid img:nth-child(7) { animation-delay: 0.4s; } .logo-grid img:nth-child(8) { animation-delay: 0.45s; } .logo-grid img:nth-child(9) { animation-delay: 0.5s; } .logo-grid img:nth-child(10) { animation-delay: 0.55s; } .logo-grid img:nth-child(11) { animation-delay: 0.6s; } .logo-grid img:nth-child(12) { animation-delay: 0.65s; } .logo-grid img:nth-child(13) { animation-delay: 0.7s; } .logo-grid img:nth-child(14) { animation-delay: 0.75s; } .logo-grid img:nth-child(15) { animation-delay: 0.8s; } .logo-grid img:nth-child(16) { animation-delay: 0.85s; } .logo-grid img:nth-child(17) { animation-delay: 0.9s; } .logo-grid img:nth-child(18) { animation-delay: 0.95s; } .logo-grid img:nth-child(19) { animation-delay: 1s; } .logo-grid img:nth-child(20) { animation-delay: 1.05s; } .logo-grid img:nth-child(21) { animation-delay: 1.1s; } .logo-grid img:nth-child(22) { animation-delay: 1.15s; } .logo-grid img:nth-child(23) { animation-delay: 1.2s; } .logo-grid img:nth-child(24) { animation-delay: 1.25s; } .logo-grid img:nth-child(25) { animation-delay: 1.3s; } .logo-grid img:nth-child(26) { animation-delay: 1.35s; } .logo-grid img:nth-child(27) { animation-delay: 1.4s; } .logo-grid img:nth-child(28) { animation-delay: 1.45s; }
    .logo-grid img:hover { transform: translateY(-12px) scale(1.1); box-shadow: 0 16px 48px rgba(0, 98, 246, 0.25); border-color: #0062F6; background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%); }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideInDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
    @media (max-width: 768px) { .brands-intro { padding: 40px 24px; } .brands-intro h1 { font-size: 32px; margin-bottom: 40px; } .brands-intro p { font-size: 14px; padding: 24px; } .logo-grid { grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 16px; padding: 30px 20px; } .logo-grid img { max-height: 80px; } }
    @media (max-width: 480px) { .brands-intro { padding: 30px 16px; } .brands-intro h1 { font-size: 24px; margin-bottom: 30px; } .brands-intro p { font-size: 13px; padding: 18px; } .logo-grid { grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 12px; padding: 20px 12px; } .logo-grid img { max-height: 70px; } }
</style>
</head>
<body class="brands">
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
    </div>
    </header>
    <div class="menu">
            <a href="../index.php">HOME</a>
            <a href="../pages/product.php">PRODUCTS</a>
            <a href="../products/desktop.php">DESKTOP</a>
            <a href="../products/laptop.php">LAPTOP</a>
            <a href="../pages/brands.php" style="font-weight: bold;">BRANDS</a>
    </div>

    <div class="breadcrumb">
        <a href="../index.php"><span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px;">home</span>Home</a> > <span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px; color: #0062F6;">store</span><a>Brands</a>
    </div>

    <div class="brands-intro">
    <h1>OUR PREMIUM BRANDS</h1>

    <p>As an online tech store, we pride ourselves on providing our customers with the best and most reliable components from top brands in the industry. We house a wide range of trusted brands including Intel, AMD, NVIDIA, ASUS, Gigabyte, RAKK, Corsair, Kingston, MSI, ACER  and many more. Each brand has been carefully selected by our team to ensure that we offer only the highest quality components.</p>
    <br>
    <p>Whether you're looking to build a gaming PC or a workstation, our computer store online in the Philippines has the right components for you. Our brand partners offer a wide range of CPUs, GPUs, motherboards, memory, storage, and more to fit your specific needs.</p>
    <br>
    <p>We understand that choosing the right components can be a daunting task, which is why we make it easy to filter and compare products by brand, price, and specifications. Our knowledgeable staff is always available to answer any questions you may have and help you find the perfect components for your build. You can also visit an Smart Solutions store near you so we can help you choose what’s right for your needs</p>
    <br>
    <p>At Smart Solutions, we're committed to providing our customers with the best products and the best service. See the wide range of top brands we carry:</p>
    <br>
    </div>
    <div class="logo-grid">
        <img src="../image/RAKK.png"  alt="RAKK Logo">
        <img src="../image/INTEL.png" alt="Intel Logo">
        <img src="../image/AMD.png" alt="AMD Logo">
        <img src="../image/NVIDIA.png" alt="NVIDIA Logo">
        <img src="../image/ASUS.png" alt="ASUS Logo">
        <img src="../image/ASROCK.png" alt="ASRock Logo">
        <img src="../image/MSI1.png" alt="MSI Logo">
        <img src="../image/KINSTON.png" alt="KINGSTON Logo">
        <img src="../image/GIGABYTE.png" alt="GIGABYTE Logo">
        <img src="../image/COOLERMASTER.png" alt="COOOLERMASTER Logo">
        <img src="../image/CORSAIR.png" alt="CORSAIR Logo">
        <img src="../image/GSKILL.png" alt="G.SKILL Logo">
        <img src="../image/HP.png" alt="HP Logo">
        <img src="../image/SEASONIC.png" alt="SEASONIC Logo">
        <img src="../image/NVISION.png" alt="NVISION Logo">
        <img src="../image/GAMDIAS1.png" alt="GAMDIAS Logo">
        <img src="../image/GAMDIAS1.png" alt="GAMDIAS Logo">
        <img src="../image/CRUCIAL.png" alt="CRUCIAL Logo">
        <img src="../image/INPLAY.png" alt="INPLAY Logo">
        <img src="../image/ACER.png" alt="ACER Logo">
        <img src="../image/LOGI.png" alt="LOGITECH Logo">
        <img src="../image/HYPERX1.png" alt="HYPERX Logo">
        <img src="../image/REDDRAGON.png" alt="REDDRAGON Logo">
        <img src="../image/RAZER.png" alt="RAZER Logo">
        <img src="../image/SAMSUNG.png" alt="SAMSUNG Logo">
        <img src="../image/STEELSERIES1.png" alt="STEELSERIES Logo">
        <img src="../image/EDIFIER1.png" alt="EDIFIER Logo">
        <img src="../image/ROYAL_KLUDGE.png" alt="ROYAL_KLUDGE Logo">
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

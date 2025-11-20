<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
<link rel="stylesheet" href="../css/design.css" />
<link rel="stylesheet" href="../css/animations.css" />
<meta charset="UTF-8">
    <title>ABOUT US - SMARTSOLUTIONS</title>
</head>
<body>
<?php
// Start session to check if the user is logged in
session_start();

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
        <a href="location.php"><div class="location">
            <img class="location" src="../image/location-icon.png" alt="location-icon">
        </a>
        </div>
        <div class="track">
            <a href="track.php"><img class="track" src="../image/track-icon.png" alt="track-icon">
        </a>
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
            <a href="../product.php">PRODUCTS</a>
            <a href="../products/desktop.php">DESKTOP</a>
            <a href="../products/laptop.php">LAPTOP</a>
            <a href="../brands.php">BRANDS</a>
    </div>

    <div class="breadcrumb">
            <a href="../index.php">Home</a> >
            <a>About Us</a>
    </div>

    <div class="about-us">
    <div class="about-uslogo">
        <img src="../image/smartsolutionslogo.jpg" alt="logo-icon">
    </div>
        <h1>About Smart Solutions</h1>
        <p>Welcome to Smart Solutions! We are a leading provider of innovative technology solutions designed to make your life easier. Our focus is on delivering cutting-edge products that enhance performance, reliability, and usability for individuals and businesses alike.</p>

        <div class="section">
        <div class="history">
            <img src="../image/history.jpg" alt="history-img">
            <h2>Our History</h2>
            <p>Smart Solutions was founded in 2015 with a vision to revolutionize technology accessibility for individuals and businesses. Starting as a small computer shop, we have grown into a leading tech solutions provider, catering to diverse needs with our innovative products and excellent service.</p>
            <p>Over the years, we have expanded our portfolio to include high-performance desktops, customizable PC builds, and premium laptops. Our journey has been fueled by a relentless commitment to quality, customer satisfaction, and innovation.</p>
        </div>
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
                <li>High-quality products at competitive prices</li>
                <li>Exceptional customer support and service</li>
                <li>Innovative solutions to meet diverse needs</li>
                <li>Fast delivery and reliable warranty services</li>
            </ul>
        </div>

        <div class="section">
    <h2>Meet Our Developers</h2>
    <div class="developers">
        <div class="developer">
            <img src="../image/Marlo.jpg" alt="Marlo C. Bohol">
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
        <div class="developer">
            <img src="../image/Earl.jpg" alt="Earl Andre V. Galacio">
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
        <div class="developer">
            <img src="../image/Kenneth.jpg" alt="Kenneth D. Semorlan">
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
            dropdownMenu.style.display = 'none';  // Hide the dropdown menu if clicked outside
        }
    };
</script>
<script src="js/search.js"></script>
<script src="../js/jquery-animations.js"></script>
<script src="../js/header-animation.js"></script>
</body>
</html>
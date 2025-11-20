<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="image/smartsolutionslogo.jpg" type="image/x-icon">
<link rel="stylesheet" href="design.css" />
<meta charset="UTF-8">
    <title>ADD TO CART - SMARTSOLUTIONS</title>
</head>
<body>
<?php
// Start session to check if the user is logged in
session_start();

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
$conn->close();
?>
    <header>
      <div class="ssheader">
        <div class="logo">
            <img src="image/logo.png" alt="Smart Solutions Logo">
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search">
        <div class="search-icon">
            <img src="image/search-icon.png" alt="Search Icon">
        </div>
        </div>
        <a href="location.php"><div class="location">
            <img class="location" src="image/location-icon.png" alt="location-icon">
        </a>
        </div>
        <div class="track">
            <a href="track.php"><img class="track" src="image/track-icon.png" alt="track-icon">
        </a>
        </div>
        <a href="cart.php"><div class="cart">
            <img class="cart" src="image/cart-icon.png" alt="cart-icon">
        </a>
        </div>
        <div class="login profile-dropdown">
        <a href="javascript:void(0)" onclick="toggleDropdown()">
            <!-- Check if user is logged in, if yes show profile picture, else show login icon -->
            <img class="login" 
                 src="<?php echo isset($_SESSION['user_id']) ? $profile_picture : 'image/login-icon.png'; ?>" 
                 alt="login-icon" 
                 style="border-radius: <?php echo isset($_SESSION['user_id']) ? '50%' : '0'; ?>; 
                        width: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>; 
                        height: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>;">
        </a>
        <div id="dropdown-menu" class="dropdown-content">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="edit-profile.php">Edit Profile</a>
                <a href="logout.php">Log Out</a>
                <?php endif; ?>
            </div>  
        </div>
        <div class="login-text">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php"></a>
            <?php else: ?>
                <a href="login.html"><p>Login/<br>Sign In</p></a>
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
            <a href="index.php">Home</a> >
            <a>Add to Cart</a>
    </div>

    <div class="add-notification">
    <div class="notification-message">
        <img src="image/success-icon.png" alt="Success Icon" class="success-icon">
        <span>Product successfully added to your Shopping Cart</span>
    </div>
    <div class="add-details">
        <div class="add-info">
            <img src="image/msi.png" alt="Product Image" class="add-image">
            <div class="cart-text">
                <h3>MSI Thin A15 B7UCX-084PH 15.6" FHD 144Hz AMD RYZEN 5 7535HS/8GB/512GBSSD/RTX 2050 4GB/WIN11 Laptop</h3>
                <p>Quantity: 1</p>
                <p>Cart Total: <strong>₱35,795.00</strong></p>
            </div>
        </div>
    </div>
    <div class="cart-buttons">
        <a href ="index.html"><button class="continue-shopping">Continue Shopping</button>
        </a>
        <a href ="checkout.html"><button class="proceed-checkout">Proceed to Checkout</button>
        </a>
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
        // Add JavaScript to toggle dropdown
        function toggleDropdown() {
            var profileDropdown = document.querySelector(".profile-dropdown");
            profileDropdown.classList.toggle("active");
        }

        // Close the dropdown if the user clicks anywhere outside of it
        window.onclick = function(event) {
            if (!event.target.matches('.profile-dropdown, .profile-dropdown *')) {
                var dropdowns = document.querySelectorAll('.dropdown-content');
                for (var i = 0; i < dropdowns.length; i++) {
                    dropdowns[i].style.display = 'none';
                }
            }
        };
</script>
</body>
</html>
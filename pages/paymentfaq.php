<!DOCTYPE html>
<html lang="en">
<head>
<link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/design.css" />
<link rel="stylesheet" href="../css/animations.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
<title>PAYMENT FAQ - SMART SOLUTIONS</title>
</head>
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
            if (strpos($row['profile_picture'], 'http') === 0) {
                $profile_picture = $row['profile_picture']; // Google/external URL
            } else {
                $profile_picture = "../" . $row['profile_picture']; // Local path with prefix
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

    // Trigger JavaScript alert
    echo "<script>alert('Item added to cart!');</script>";
}
?>

<body class="brands">
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
            <a href="track.php"><img class="track" src="../image/track-icon.png" alt="track-icon"></a>
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

            .profile-dropdown {
                position: relative;
                display: inline-block;
            }

            .dropdown-content {
                display: none;
                position: absolute;
                top: 110%;
                right: 0;
                background: white;
                border-radius: 8px;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
                border: 1px solid #e0e0e0;
                min-width: 200px;
                z-index: 1000;
            }

            .dropdown-content a {
                display: flex;
                align-items: center;
                padding: 12px 16px;
                color: #333;
                font-size: 14px;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.2s ease;
                border-left: 3px solid transparent;
            }

            .dropdown-content a:hover {
                background: #f5f5f5;
                color: #0062F6;
                border-left-color: #0062F6;
            }

            .profile-dropdown.active .dropdown-content {
                display: block;
            }

            .material-icons {
                font-family: 'Material Icons';
                font-weight: normal;
                font-style: normal;
                font-size: 24px;
                display: inline-block;
                line-height: 1;
                text-transform: none;
                letter-spacing: normal;
                word-wrap: normal;
                white-space: nowrap;
                direction: ltr;
            }

            .breadcrumb1 { padding: 16px 24px; font-size: 14px; color: #555; background: transparent; }
            .breadcrumb1 a { color: #0062F6; text-decoration: none; font-weight: 500; transition: color 0.3s ease; }
            .breadcrumb1 a:hover { color: #0052D4; }

            .product-category {
                text-align: center;
                padding: 30px 40px;
            }

            .product-category h1 {
                font-size: 36px;
                font-weight: 700;
                color: #0062F6;
                margin: 0;
                letter-spacing: 1px;
                transition: all 0.3s ease;
            }

            .product-category h1:hover {
                color: #004FCC;
                transform: translateY(-2px);
            }

            .processor-sectionpay {
                max-width: 1000px;
                margin: 40px auto;
                padding: 40px;
                background: linear-gradient(135deg, #f8f9ff 0%, #f0f5ff 100%);
                border-radius: 12px;
                border-left: 6px solid #0062F6;
                box-shadow: 0 4px 20px rgba(0, 98, 246, 0.08);
                transition: all 0.3s ease;
            }

            .processor-sectionpay:hover {
                box-shadow: 0 8px 30px rgba(0, 98, 246, 0.15);
                transform: translateY(-2px);
            }

            .processor-sectionpay h4 {
                color: #0062F6;
                font-size: 18px;
                font-weight: 600;
                margin: 24px 0 12px 0;
                transition: all 0.3s ease;
            }

            .processor-sectionpay h4:hover {
                color: #004FCC;
                transform: translateX(4px);
            }

            .processor-sectionpay p {
                color: #333;
                font-size: 15px;
                line-height: 1.8;
                font-weight: 500;
                margin: 12px 0;
                transition: all 0.3s ease;
            }

            .processor-sectionpay p:hover {
                color: #0062F6;
                transform: translateX(6px);
            }

            .processor-sectionpay i {
                color: #666;
                font-size: 14px;
                line-height: 1.8;
                display: block;
                margin: 8px 0;
                font-style: italic;
                transition: all 0.3s ease;
            }

            .processor-sectionpay i:hover {
                color: #0062F6;
                transform: translateX(8px);
            }

            .important-note {
                background: linear-gradient(135deg, rgba(0, 98, 246, 0.1) 0%, rgba(0, 98, 246, 0.05) 100%);
                border-left: 4px solid #0062F6;
                padding: 16px 20px;
                border-radius: 8px;
                margin: 24px 0;
                font-size: 14px;
                color: #333;
                transition: all 0.3s ease;
            }

            .important-note:hover {
                box-shadow: 0 4px 12px rgba(0, 98, 246, 0.15);
                transform: translateY(-2px);
            }

            .important-note strong {
                color: #0062F6;
                font-weight: 600;
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
                    <a href="../user/profile.php"></a>
                <?php else: ?>
                    <a href="../user/register.php"><p>Login/<br>Sign In</p></a>
                <?php endif; ?>
            </div>
    </div>
    </header>

    <div class="menu">
            <a href="../index.php">HOME</a>
            <a href="product.php">PRODUCTS</a>
            <a href="../products/desktop.php">DESKTOP</a>
            <a href="../products/laptop.php">LAPTOP</a>
            <a href="../brands.php">BRANDS</a>
    </div>

    <div class="breadcrumb1">
            <a href="../index.php"><span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px;">home</span>Home</a> > <span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px; color: #0062F6;">help</span><a>Payment FAQ</a>
    </div>

     <div class="product-category">
        <h1>PAYMENT FAQs</h1>
        </div>
    <div class="processor-sectionpay">

    <h4>GCash</h4>
    <p>Is there a need to sign-up to use GCash as a payment method?</p>
    <i> &#9679; No need to sign up anywhere. Payments will go directly to the checkout page or the payment link.</i>

    <h4>What information is needed to provide?</h4>
    <i> &#9679; Only the information needed by the payment channel. For example, debit card payments will need the card details.</i>

    <h4>Is there a verification step? What is the verification process?</h4>
    <i>&#9679; Verification step will depend on the payment channel. GCash will send an OTP that is needed before the payment can be processed.</i>

    <h4>Is there an additional fee to use GCash?</h4>
    <i> &#9679; The transaction fees are only charged for successful transactions and will be shouldered by the merchant. There are no additional fees.</i>

    <div class="important-note">
        <strong>Important Note:</strong><br>
        "For orders paid with credit or debit card, kindly present the card used to make the payment of your order and your valid Primary ID upon parcel delivery. Authorization letter from cardholder along with the purpose of the transaction is a must, in case the card used to make payment is not under the customer’s name. Failure to do so may result in order cancellation."
    </div>

    <h4>Cash on Delivery</h4>
    <p>No maximum order amounts.</p>
    
    <h4>What is the cutoff time for same day/express delivery?</h4>
    <i> &#9679; 3:00PM is the cutoff time for same day/express deliveries. Purchases after the cutoff time will be processed the following day.</i>

    <h4>What are the areas covered by SmartSolutions delivery?</h4>
    <i> &#9679; As of the moment, the areas available are within DAVAO REGION</i>

    <h4>DAVAO REGION</h4>
    <p>Davao del Sur</p>
    <p>Davao del Norte</p>
    <p>Davao de Oro</p>
    <p>Davao Occidental</p>
    <p>Davao Oriental</p>
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
            <li><a href="corporate.html">SmartSolutions Corporate</a></li>
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
    function toggleDropdown(event) {
        event.preventDefault();
        event.stopPropagation();
        const profileDropdown = document.querySelector('.profile-dropdown');
        profileDropdown.classList.toggle('active');
    }

    document.addEventListener('click', function(event) {
        const profileDropdown = document.querySelector('.profile-dropdown');
        if (!profileDropdown.contains(event.target)) {
            profileDropdown.classList.remove('active');
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelector('.profile-dropdown').classList.remove('active');
        }
    });
</script>
<script src="js/search.js"></script>
<script src="../js/jquery-animations.js"></script>
<script src="../js/header-animation.js"></script>
</body>
</html>

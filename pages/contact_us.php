<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
<link rel="stylesheet" href="../css/design.css" />
<link rel="stylesheet" href="../css/animations.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
<meta charset="UTF-8">
    <title>CONTACT US - SMARTSOLUTIONS</title>
</head>
<body>
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
?>
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
        <style>
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
        </style>
        <div class="login profile-dropdown">
            <a href="javascript:void(0)" onclick="toggleDropdown(event)">
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
            <a href="product.php">PRODUCTS</a>
            <a href="../products/desktop.php">DESKTOP</a>
            <a href="../products/laptop.php">LAPTOP</a>
            <a href="../brands.php">BRANDS</a>
    </div>

    <div class="breadcrumb">
        <a href="../index.php"><span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px;">home</span>Home</a> > <span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px; color: #0062F6;">mail</span><a>Contact Us</a>
    </div>

    <style>
        .breadcrumb { padding: 16px 24px; font-size: 14px; color: #555; background: transparent; }
        .breadcrumb a { color: #0062F6; text-decoration: none; font-weight: 500; transition: color 0.3s ease; }
        .breadcrumb a:hover { color: #0052D4; }
    </style>

    <style>
        .intro-paragraphs {
            max-width: 1000px;
            margin: 30px auto;
            padding: 40px;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f5ff 100%);
            border-radius: 12px;
            border-left: 6px solid #0062F6;
            box-shadow: 0 4px 20px rgba(0, 98, 246, 0.08);
        }

        .intro-paragraphs p {
            margin: 16px 0;
            line-height: 1.8;
            color: #333;
            font-size: 15px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .intro-paragraphs p:first-child {
            font-weight: 600;
            color: #0062F6;
            font-size: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(0, 98, 246, 0.2);
            margin-bottom: 20px;
        }
    </style>

    <div class="intro-paragraphs">
        <p>At Smart Solutions, we value your feedback and are always here to help. Whether you have a question about any of our products including gaming laptops, desktop packages, graphic cards, prebuild desktops or need assistance with an order, our customer service team and tech experts are ready to assist you.</p>
        <p>To get in touch with us, please use the contact form below or you can message us thru the messaging app integrated in our website. We will respond to your inquiry as soon as possible. If you would prefer to speak with someone directly, please call us at the number provided. Our customer service hours are Monday-Friday 9am-6pm PST.</p>
        <p>If you have a technical issue or need warranty support, please visit our support page, message us, email us at smartsolutionssupport@gmail.com.</p>
        <p>Thank you for choosing Smart Solutions as your trusted tech expert and online computer store in the Philippines.</p>
    </div>

    <div class="contact-container">
    <div class="contact-form">
        <form>
            <h2>Contact Us</h2>
            <label for="fname">Full Name:</label>
            <input type="text" id="fname" name="fname" placeholder="Enter your Full Name">
            <label for="phone">Phone Number:</label>
            <input type="text" id="phone" name="phone" placeholder="Enter your Phone Number">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your Email">
            <label for="message">Your Message:</label>
            <textarea id="message" name="message" placeholder="Enter your Message"></textarea>
            <input type="submit" value="Send">
        </form>
    </div>

    <div class="contact-info">
        <p><i class="material-icons" style="vertical-align: middle; font-size: 20px; margin-right: 10px;">location_on</i><strong>Location:</strong> Martinez Subdivision Phase 1, Barangay Dahican, City of Mati, Davao Oriental</p>
        <p><i class="material-icons" style="vertical-align: middle; font-size: 20px; margin-right: 10px;">phone</i><strong>Customer Service Hotline:</strong> 0951 813 0782</p>
        <p><i class="material-icons" style="vertical-align: middle; font-size: 20px; margin-right: 10px;">schedule</i><strong>Office Hours:</strong> 9:00 AM - 6:00 PM</p>
        <p><i class="material-icons" style="vertical-align: middle; font-size: 20px; margin-right: 10px;">email</i><strong>Email:</strong> smartsolutionscomputershop@gmail.com</p>
        <p><i class="material-icons" style="vertical-align: middle; font-size: 20px; margin-right: 10px;">people</i><strong>Facebook:</strong> Smart Solutions Computer Shop</p>
    <div class="map-container">
        <iframe
            src="https://www.google.com/maps?q=6.942242,126.247245&hl=es;z=14&output=embed"
            width="600"
            height="450"
            style="border:0;"
            allowfullscreen=""
            loading="lazy">
        </iframe>
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

    <style>
        .contact-form h2 {
            color: #0062F6;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .contact-form label {
            font-weight: 600;
            color: #333;
            font-size: 13px;
            display: block;
            margin-top: 12px;
            margin-bottom: 6px;
            transition: all 0.2s ease;
        }

        .contact-form input[type="text"],
        .contact-form input[type="email"],
        .contact-form textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: inherit;
            font-size: 13px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .contact-form input[type="text"]:hover,
        .contact-form input[type="email"]:hover,
        .contact-form textarea:hover {
            border-color: #0062F6;
            box-shadow: 0 2px 8px rgba(0, 98, 246, 0.1);
        }

        .contact-form input[type="text"]:focus,
        .contact-form input[type="email"]:focus,
        .contact-form textarea:focus {
            outline: none;
            border-color: #0062F6;
            box-shadow: 0 0 0 3px rgba(0, 98, 246, 0.1);
            background-color: #f8f9ff;
            transform: translateY(-2px);
        }

        .contact-form textarea {
            resize: vertical;
            min-height: 100px;
        }

        .contact-form input[type="submit"] {
            width: 100%;
            padding: 12px;
            margin-top: 30px;
            margin-bottom: 0;
            background: linear-gradient(135deg, #0062F6 0%, #004FCC 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 98, 246, 0.3);
            letter-spacing: 0.5px;
        }

        .contact-form input[type="submit"]:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 98, 246, 0.5);
            background: linear-gradient(135deg, #004FCC 0%, #003AA3 100%);
        }

        .contact-form input[type="submit"]:active {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0, 98, 246, 0.3);
        }

        .contact-info p {
            padding: 12px 15px;
            margin: 2px 0;
            background: #f8f9ff;
            border-left: 4px solid #0062F6;
            border-radius: 6px;
            color: #333;
            font-size: 14px;
            line-height: 1.5;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .contact-info p:hover {
            background: #f0f5ff;
            box-shadow: 0 2px 8px rgba(0, 98, 246, 0.15);
            transform: translateX(5px);
        }

        .contact-info p strong {
            color: #0062F6;
            font-weight: 600;
            margin-right: 5px;
        }

        .contact-info p i {
            color: #0062F6;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .contact-info p:hover i {
            transform: scale(1.1);
        }

        .map-container {
            margin-top: 15px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .map-container:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .intro-paragraphs {
            max-width: 1000px;
            margin: 30px auto;
            padding: 40px;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f5ff 100%);
            border-radius: 12px;
            border-left: 6px solid #0062F6;
            box-shadow: 0 4px 20px rgba(0, 98, 246, 0.08);
            transition: all 0.3s ease;
        }

        .intro-paragraphs:hover {
            box-shadow: 0 8px 30px rgba(0, 98, 246, 0.15);
            transform: translateY(-2px);
        }

        .intro-paragraphs p {
            margin: 16px 0;
            line-height: 1.8;
            color: #333;
            font-size: 15px;
            font-weight: 500;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
        }

        .intro-paragraphs p:first-child {
            font-weight: 600;
            color: #0062F6;
            font-size: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(0, 98, 246, 0.2);
            margin-bottom: 20px;
        }

        .contact-form {
            transition: all 0.3s ease;
        }

        .contact-form:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12) !important;
        }
    </style>
    <script>
    // Toggle dropdown with modern implementation
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
<script src="js/search.js"></script>
<script src="../js/jquery-animations.js"></script>
<script src="../js/header-animation.js"></script>
</body>
</html>

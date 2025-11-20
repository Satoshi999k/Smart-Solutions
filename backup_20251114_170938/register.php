<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "smartsolutions";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    // Get form data
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt password
    $first_name = $conn->real_escape_string($_POST['first-name']);
    $last_name = $conn->real_escape_string($_POST['last-name']);
    $address = $conn->real_escape_string($_POST['address']);
    $phone_number = $conn->real_escape_string($_POST['phone-number']);
    $postal_code = $conn->real_escape_string($_POST['postal-code']);

    // Handle file upload
    $profile_picture = "";
    if (isset($_FILES['profile-picture']) && $_FILES['profile-picture']['error'] == 0) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $profile_picture = $upload_dir . basename($_FILES['profile-picture']['name']);
        move_uploaded_file($_FILES['profile-picture']['tmp_name'], $profile_picture);
    }

    // Insert data into the database
    $sql = "INSERT INTO users (email, password, first_name, last_name, address, phone_number, postal_code, profile_picture) 
            VALUES ('$email', '$password', '$first_name', '$last_name', '$address', '$phone_number', '$postal_code', '$profile_picture')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Account created successfully! Please log in.');
                window.location.href = 'register.php';
              </script>";
        exit;
    } else {
        echo "<script>
                alert('Error: " . addslashes($conn->error) . "');
              </script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - SmartSolutions</title>
    <link rel="stylesheet" href="design.css" />
    <link rel="shortcut icon" href="image/smartsolutionslogo.jpg" type="image/x-icon">
</head>
<body>
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
        <a href="location.php">
            <div class="location">
                <img class="location" src="image/location-icon.png" alt="location-icon">
            </div>
        </a>
        <div class="track">
            <a href="track.php"><img class="track" src="image/track-icon.png" alt="track-icon"></a>
        </div>
        <a href="cart.php">
            <div class="cart">
                <img class="cart" src="image/cart-icon.png" alt="cart-icon">
            </div>
        </a>
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
                <a href="logout.php">Log Out</a>
                <?php endif; ?>
            </div>  
        </div>
        <div class="login-text">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php"></a>
            <?php else: ?>
                <a href="register.php"><p>Login/<br>Sign In</p></a>
            <?php endif; ?>
        </div>
    </div>
    </header>

    <div class="menu">
            <a href="index.php" style="font-weight: bold;">HOME</a>
            <a href="product.php">PRODUCTS</a>
            <a href="products/desktop.php">DESKTOP</a>
            <a href="products/laptop.php">LAPTOP</a>
            <a href="brands.php">BRANDS</a>
    </div>

    <div class="breadcrumb">
        <a href="index.html">Home</a> >
        <a>Log In</a>
    </div>

    <div class="login-form-wrapper">
        <!-- Login Form -->
        <div class="login-form">
            <h2>Login</h2>
            <p>Sign in to your account!</p>
            <form action="login.php" method="POST">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                
                <button type="submit" class="login-btn">Login</button>
            </form>
            <div class="additional-links">
                <a href="index.html">Return to Store</a>
                <a id="create-account-link" href="#">Create an Account</a>
                <a id="admin" href="https://drive.google.com/drive/folders/1JDnpJUlBRwPHYBa9fjg5pQaPRq3FUKv-?usp=sharing">Admin</a>
            </div>
        </div>

        <!-- Register Form -->
        <div class="formreg-container" id="create-account-form" style="display: none;">
            <h2>Create Account</h2>
            <p>Sign up to access your account</p>
            <form action="register.php" method="POST" enctype="multipart/form-data">
                <label for="new-email">Email Address *</label>
                <input type="email" id="new-email" name="email" placeholder="Enter your email" required>
                
                <label for="new-password">Password *</label>
                <input type="password" id="new-password" name="password" placeholder="Enter your password" required>
                
                <label for="confirm-password">Re-enter Password *</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Re-enter your password" required>
                
                <label for="profile-picture">Upload Profile Picture *</label>
                <input type="file" id="profile-picture" name="profile-picture" accept="image/*" required>
                
                <label for="first-name">First Name *</label>
                <input type="text" id="first-name" name="first-name" placeholder="Enter your first name" required>
                
                <label for="last-name">Last Name *</label>
                <input type="text" id="last-name" name="last-name" placeholder="Enter your last name" required>
                
                <label for="address">Address *</label>
                <input type="text" id="address" name="address" placeholder="Enter your address" required>
                
                <label for="phone-number">Phone Number *</label>
                <input type="tel" id="phone-number" name="phone-number" placeholder="Enter your phone number" required>
                
                <label for="postal-code">Postal Code *</label>
                <input type="text" id="postal-code" name="postal-code" placeholder="Enter your postal code" required>
                
                <button type="submit" class="register-btn">Register</button>
            </form>
            <div class="additional-links">
                <a href="#" id="back-to-login">Already have an account? Login</a>
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
        // JavaScript to toggle forms
        document.getElementById('create-account-link').addEventListener('click', function(event) {
            event.preventDefault();
            document.querySelector('.login-form').style.display = 'none';
            document.getElementById('create-account-form').style.display = 'block';
        });

        document.getElementById('back-to-login').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('create-account-form').style.display = 'none';
            document.querySelector('.login-form').style.display = 'block';
        });
    </script>
</body>
</html>


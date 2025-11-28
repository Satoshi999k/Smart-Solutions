<?php
// Start session to preserve cart
session_start();

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

// Initialize profile picture variable
$profile_picture = '';

// If user is logged in, fetch their profile picture from session or database
if (isset($_SESSION['user_id'])) {
    $profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'image/default-profile.png';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    // Get form data
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); 
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
        $_SESSION['alert'] = ['type' => 'success', 'title' => 'Success!', 'message' => 'Account created successfully! Redirecting to login...'];
        header('Location: register.php');
        exit;
    } else {
        // Parse database error and provide user-friendly message
        $error_msg = $conn->error;
        $user_msg = 'An error occurred while creating your account.';
        
        if (strpos($error_msg, 'Duplicate entry') !== false && strpos($error_msg, 'email') !== false) {
            $user_msg = 'This email address is already registered. Please use a different email or login.';
        } else if (strpos($error_msg, 'Duplicate entry') !== false) {
            $user_msg = 'This account already exists. Please use different information.';
        } else if (strpos($error_msg, 'constraint') !== false) {
            $user_msg = 'Invalid data provided. Please check your inputs.';
        }
        
        $_SESSION['alert'] = ['type' => 'error', 'title' => 'Registration Failed!', 'message' => $user_msg];
        header('Location: register.php');
        exit;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - SmartSolutions</title>
    <link rel="stylesheet" href="../css/design.css" />
    <link rel="stylesheet" href="../css/animations.css" />
    <link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
    <style>
        /* Enhanced SweetAlert2 Styling */
        .swal2-popup {
            animation: slideInUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
            border-radius: 25px !important;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25), 0 0 40px rgba(0, 98, 246, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            backdrop-filter: blur(10px) !important;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%) !important;
        }
        
        .swal2-modal.show {
            animation: fadeIn 0.4s ease-out !important;
        }
        
        .swal2-title {
            font-size: 32px !important;
            font-weight: 800 !important;
            color: #1a1a2e !important;
            margin-bottom: 20px !important;
            animation: slideDown 0.5s ease-out !important;
            background: linear-gradient(135deg, #0062F6, #0052cc) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
        }
        
        .swal2-html-container {
            font-size: 17px !important;
            color: #555 !important;
            line-height: 1.8 !important;
            animation: fadeInUp 0.6s ease-out !important;
            font-weight: 500 !important;
        }
        
        .swal2-icon {
            width: 100px !important;
            height: 100px !important;
            border-width: 0px !important;
            animation: zoomIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
            display: none !important;
        }
        
        .swal2-icon.swal2-success {
            border-color: #28a745 !important;
        }
        
        .swal2-icon.swal2-error {
            border-color: #dc3545 !important;
        }
        
        .swal2-confirm {
            background: linear-gradient(135deg, #0062F6 0%, #0052cc 100%) !important;
            border-radius: 15px !important;
            padding: 16px 45px !important;
            font-weight: 700 !important;
            font-size: 17px !important;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
            box-shadow: 0 8px 25px rgba(0, 98, 246, 0.4), 0 0 20px rgba(0, 98, 246, 0.2) !important;
            border: none !important;
            cursor: pointer !important;
            animation: slideUp 0.6s ease-out !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }
        
        .swal2-confirm:hover {
            background: linear-gradient(135deg, #0052cc 0%, #003fa3 100%) !important;
            transform: translateY(-5px) scale(1.08) !important;
            box-shadow: 0 15px 40px rgba(0, 98, 246, 0.6), 0 0 30px rgba(0, 98, 246, 0.3) !important;
        }
        
        .swal2-confirm:active {
            transform: translateY(-2px) scale(0.96) !important;
        }
        
        .swal2-cancel {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
            border-radius: 15px !important;
            padding: 16px 45px !important;
            font-weight: 700 !important;
            font-size: 17px !important;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
            border: none !important;
            cursor: pointer !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }
        
        .swal2-cancel:hover {
            background: linear-gradient(135deg, #5a6268 0%, #4a5157 100%) !important;
            transform: translateY(-5px) scale(1.08) !important;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25) !important;
        }
        
        .swal2-cancel:active {
            transform: translateY(-2px) scale(0.96) !important;
        }
        
        /* Backdrop styling */
        .swal2-backdrop {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.4), rgba(0, 98, 246, 0.1)) !important;
            backdrop-filter: blur(8px) !important;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(80px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
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
        
        @keyframes zoomIn {
            from {
                transform: scale(0.2) rotate(-180deg);
                opacity: 0;
            }
            50% {
                transform: scale(1.15);
            }
            to {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }
        
        /* Material Design Icons Enhanced Styling */
        .swal2-html-container i.mdi {
            display: inline-block;
            animation: iconBounce 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
        }
        
        @keyframes iconBounce {
            0% {
                transform: scale(0) rotate(-180deg);
                opacity: 0;
            }
            50% {
                transform: scale(1.2) rotate(10deg);
            }
            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }
        
        /* Pulse animation for icons */
        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(0, 98, 246, 0.7);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(0, 98, 246, 0);
            }
        }
        
        /* Success state styling */
        .swal2-popup:has(.mdi-check-circle) {
            border: 2px solid #28a745 !important;
            background: linear-gradient(135deg, #f0fff4 0%, #ffffff 100%) !important;
        }
        
        /* Error state styling */
        .swal2-popup:has(.mdi-alert-circle),
        .swal2-popup:has(.mdi-lock-alert),
        .swal2-popup:has(.mdi-email-off),
        .swal2-popup:has(.mdi-email-alert),
        .swal2-popup:has(.mdi-folder-alert) {
            border: 2px solid #dc3545 !important;
            background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%) !important;
        }
    </style>
    <?php
    // Display alert if one is set (from login.php or register.php)
    $alertData = null;
    $redirect_url = null;
    if (isset($_SESSION['alert'])) {
        $alertData = $_SESSION['alert'];
        // Save redirect before unsetting
        $redirect_url = isset($_SESSION['redirect']) ? $_SESSION['redirect'] : '../index.php';
        // Unset immediately so it doesn't show again on reload
        unset($_SESSION['alert']);
        unset($_SESSION['redirect']);
    }
    ?>
    <?php if ($alertData): ?>
    <?php
        $title = addslashes($alertData['title']);
        $message = addslashes($alertData['message']);
        $type = $alertData['type'];
        
        // Use null for icon since we're using Material Design Icons in HTML
        $icon = null;
        $htmlContent = '';
        if ($type === 'success') {
            $htmlContent = '<i class="mdi mdi-check-circle" style="font-size: 60px; color: #28a745;"></i><br><br>' . $message;
        } else {
            $htmlContent = '<i class="mdi mdi-alert-circle" style="font-size: 60px; color: #dc3545;"></i><br><br>' . $message;
        }
    ?>
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                Swal.fire({
                    title: '<?php echo $title; ?>',
                    html: '<?php echo str_replace("\n", "", $htmlContent); ?>',
                    icon: null,
                    confirmButtonColor: '#0062F6',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    backdrop: 'rgba(0,0,0,0.5)',
                    didOpen: (modal) => {
                        // Smooth entrance animation
                        modal.style.animation = 'slideInUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
                    },
                    didClose: (modal) => {
                        // Smooth exit animation
                        modal.style.animation = 'fadeOut 0.3s ease-in';
                    }
                }).then((result) => {
                    if ('<?php echo $type; ?>' === 'success') {
                        let redirect = '<?php echo $redirect_url; ?>';
                        window.location.href = redirect;
                    } else {
                        // For errors, smooth reload
                        window.location.href = 'register.php';
                    }
                });
            }, 200);
        });
    </script>
    <?php endif; ?>
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

        /* Enhanced Form Styling */
        .login-form-wrapper {
            max-width: 500px;
            margin: 40px auto;
            padding: 20px;
            animation: slideInUp 0.6s ease-out;
        }

        .login-form, .formreg-container {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-out;
        }

        .login-form h2, .formreg-container h2 {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        .login-form p, .formreg-container p {
            font-size: 14px;
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 500;
        }

        .login-form label, .formreg-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .login-form input, .formreg-container input {
            width: 100%;
            padding: 14px 16px;
            margin-bottom: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-sizing: border-box;
            background-color: #fff;
        }

        .login-form input:focus, .formreg-container input:focus {
            outline: none;
            border-color: #0062F6;
            box-shadow: 0 0 0 4px rgba(0, 98, 246, 0.1);
            background-color: #f8f9ff;
            transform: translateY(-2px);
        }

        .login-form input::placeholder, .formreg-container input::placeholder {
            color: #bbb;
            font-weight: 400;
        }

        .login-btn, .register-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #0062F6 0%, #0052cc 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 8px 20px rgba(0, 98, 246, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        .login-btn:hover, .register-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 30px rgba(0, 98, 246, 0.5);
        }

        .login-btn:active, .register-btn:active {
            transform: translateY(-1px) scale(0.98);
        }

        /* OAuth Buttons */
        .oauth-section {
            margin: 10px 0;
            padding: 10px 0;
        }

        .oauth-title {
            text-align: center;
            font-size: 13px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .oauth-buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 0;
        }

        .oauth-btn {
            flex: 1;
            padding: 14px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            background-color: #fff;
            color: #333;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }

        .oauth-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .oauth-btn.google {
            border-color: #4285F4;
            color: #4285F4;
        }

        .oauth-btn.google:hover {
            background-color: #f1f5ff;
            box-shadow: 0 8px 20px rgba(66, 133, 244, 0.3);
        }

        .oauth-btn.facebook {
            border-color: #1877F2;
            color: #1877F2;
        }

        .oauth-btn.facebook:hover {
            background-color: #f0f2f5;
            box-shadow: 0 8px 20px rgba(24, 119, 242, 0.3);
        }

        .oauth-icon {
            font-size: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .oauth-btn.google i {
            background: linear-gradient(135deg, #4285F4 0%, #34A853 25%, #FBBC04 50%, #EA4335 75%, #4285F4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }

        .oauth-btn.facebook i {
            color: #1877F2;
        }

        .additional-links {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .additional-links a {
            color: #0062F6;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .additional-links a:hover {
            color: #0052cc;
        }

        .additional-links a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #0062F6;
            transition: width 0.3s ease;
        }

        .additional-links a:hover::after {
            width: 100%;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
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
            <form onsubmit="return false;" style="display: flex; width: 100%; position: relative;">
                <input type="text" placeholder="Search" style="flex: 1;">
                <div class="search-icon">
                    <img src="../image/search-icon.png" alt="Search Icon">
                </div>
            </form>
        </div>
        <a href="../pages/location.php">
            <div class="location">
                <img class="location" src="../image/location-icon.png" alt="location-icon">
            </div>
        </a>
        <div class="track">
            <a href="../pages/track.php"><img class="track" src="../image/track-icon.png" alt="track-icon"></a>
        </div>
        <a href="../pages/cart.php">
            <div class="cart">
                <img class="cart" src="../image/cart-icon.png" alt="cart-icon">
                <span class="cart-counter">
                    <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                </span>
            </div>
        </a>
       <div class="login profile-dropdown">
        <a href="javascript:void(0)" onclick="toggleDropdown()">
            <img class="login" 
                 src="<?php echo isset($_SESSION['user_id']) ? $_SESSION['profile_picture'] : '../image/login-icon.png'; ?>" 
                 alt="login-icon" 
                 style="border-radius: <?php echo isset($_SESSION['user_id']) ? '50%' : '0'; ?>; 
                        width: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>; 
                        height: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>;">
        </a>
        <div id="dropdown-menu" class="dropdown-content">
            <?php if (isset($_SESSION['user_id'])): ?>
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
            <a href="../pages/brands.php">BRANDS</a>
    </div>

    <div class="breadcrumb">
        <a href="index.php">Home</a> >
        <a>Log In</a>
    </div>

    <div class="login-form-wrapper">
        <!-- Login Form (Auto-detects User or Admin) -->
        <div class="login-form" style="display: block;">
            <h2>Login</h2>
            <p>Sign in to your account!</p>
            <form action="login.php" method="POST">
                <label for="email">Email Address or Username *</label>
                <input type="text" id="email" name="email" placeholder="Enter your email or username" required>
                
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                
                <button type="submit" class="login-btn">Login</button>
            </form>

            <!-- OAuth Section -->
            <div class="oauth-section">
                <div class="oauth-title">Or continue with</div>
                <div class="oauth-buttons">
                    <a href="https://accounts.google.com/o/oauth2/v2/auth?client_id=272553410332-59c6252rrksomphh6f58rpuhgofv7a5u.apps.googleusercontent.com&redirect_uri=http%3A%2F%2Flocalhost%2FITP122%2Fuser%2Foauth_callback.php&response_type=code&scope=email%20profile&state=google" class="oauth-btn google">
                        <i class="mdi mdi-google oauth-icon"></i>
                        <span>Google</span>
                    </a>
                    <?php
                    $fb_app_id = defined('FACEBOOK_APP_ID') ? FACEBOOK_APP_ID : 'YOUR_FACEBOOK_APP_ID';
                    $fb_redirect = urlencode('http://localhost/ITP122/user/oauth_callback.php');
                    $fb_login_url = "https://www.facebook.com/v18.0/dialog/oauth?client_id={$fb_app_id}&redirect_uri={$fb_redirect}&response_type=code&scope=email,public_profile&state=facebook";
                    ?>
                    <a href="<?php echo $fb_login_url; ?>" class="oauth-btn facebook">
                        <i class="mdi mdi-facebook oauth-icon"></i>
                        <span>Facebook</span>
                    </a>
                </div>
            </div>

            <div class="additional-links">
                <a href="../index.php">Return to Store</a>
                <a id="create-account-link" href="#">Create an Account</a>
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

            <!-- OAuth Section -->
            <div class="oauth-section">
                <div class="oauth-title">Or sign up with</div>
                <div class="oauth-buttons">
                    <a href="https://accounts.google.com/o/oauth2/v2/auth?client_id=272553410332-59c6252rrksomphh6f58rpuhgofv7a5u.apps.googleusercontent.com&redirect_uri=http%3A%2F%2Flocalhost%2FITP122%2Fuser%2Foauth_callback.php&response_type=code&scope=email%20profile&state=google" class="oauth-btn google">
                        <i class="mdi mdi-google oauth-icon"></i>
                        <span>Google</span>
                    </a>
                    <?php
                    $fb_app_id = defined('FACEBOOK_APP_ID') ? FACEBOOK_APP_ID : 'YOUR_FACEBOOK_APP_ID';
                    $fb_redirect = urlencode('http://localhost/ITP122/user/oauth_callback.php');
                    $fb_login_url = "https://www.facebook.com/v18.0/dialog/oauth?client_id={$fb_app_id}&redirect_uri={$fb_redirect}&response_type=code&scope=email,public_profile&state=facebook";
                    ?>
                    <a href="<?php echo $fb_login_url; ?>" class="oauth-btn facebook">
                        <i class="mdi mdi-facebook oauth-icon"></i>
                        <span>Facebook</span>
                    </a>
                </div>
            </div>

            <div class="additional-links">
                <a href="#" id="back-to-login">Already have an account? Login</a>
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

        // Enable Enter key to submit the login form
        document.querySelector('.login-form form').addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                // Submit the form
                this.submit();
            }
        });
    </script>
</body>
</html>

<script src="../js/search.js"></script>
<script src="../js/register.js"></script>

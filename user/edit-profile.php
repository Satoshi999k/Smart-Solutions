<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$success_message = "";
$error_message = "";

// Handle profile picture upload
if (isset($_POST['upload_picture'])) {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_picture']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            $new_filename = time() . '_' . $filename;
            // Use image directory like admin
            $upload_path = '../image/' . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                // Store path relative to root in database
                $db_path = 'image/' . $new_filename;
                $update_pic = "UPDATE users SET profile_picture = ? WHERE id = ?";
                $stmt = $conn->prepare($update_pic);
                $stmt->bind_param("si", $db_path, $user_id);
                
                if ($stmt->execute()) {
                    $success_message = "Profile picture updated successfully!";
                    // Update the profile picture variable for display
                    $profile_picture = '../' . $db_path;
                } else {
                    $error_message = "Error updating profile picture in database.";
                }
                $stmt->close();
            } else {
                $error_message = "Error uploading file.";
            }
        } else {
            $error_message = "Invalid file type. Only JPG, JPEG, PNG and GIF allowed.";
        }
    }
}

// Handle profile information update
if (isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $address = trim($_POST['address']);
    $postal_code = trim($_POST['postal_code']);
    
    // Update profile information
    $update_sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone_number = ?, address = ?, postal_code = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $phone_number, $address, $postal_code, $user_id);
    
    if ($stmt->execute()) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile.";
    }
    $stmt->close();
    
    // Update password if provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_pass = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($update_pass);
        $stmt->bind_param("si", $password, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch user details
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) {
    die("User not found or query returned no results");
}
$stmt->close();

// Get profile picture - handle both uploaded and default profiles
$profile_picture = '../image/default-profile.png';
if (!empty($user['profile_picture'])) {
    $pic_path = $user['profile_picture'];
    // Check if it's a full URL (from OAuth like Google)
    if (strpos($pic_path, 'http://') === 0 || strpos($pic_path, 'https://') === 0) {
        $profile_picture = $pic_path;
    } elseif (strpos($pic_path, 'image/') === 0) {
        // If path starts with 'image/', prepend ../
        $profile_picture = '../' . $pic_path;
    } elseif (strpos($pic_path, 'uploads/') === 0) {
        // Legacy uploads directory, prepend ../
        $profile_picture = '../' . $pic_path;
    } else {
        // If it's already a full path, use it
        $profile_picture = $pic_path;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/design.css" />
    <link rel="stylesheet" href="../css/animations.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <meta charset="UTF-8">
    <title>Edit Profile - SMARTSOLUTIONS</title>
    <style>
        @keyframes slideDownMenu {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #main-menu {
            animation: slideDownMenu 0.6s ease-out 0.3s both;
        }
        
        .edit-profile-container {
            max-width: 700px;
            margin: 40px auto;
            padding: 40px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 98, 246, 0.12);
            animation: slideInDown 0.6s ease-out;
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

        .edit-profile-container h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #333;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .profile-picture-section {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px;
            background: linear-gradient(135deg, #f5f9ff 0%, #f0f5ff 100%);
            border-radius: 12px;
            border: 2px solid #e0ecff;
            transition: all 0.3s ease;
        }

        .profile-picture-section:hover {
            box-shadow: 0 8px 24px rgba(0, 98, 246, 0.15);
        }

        .profile-picture-preview {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 24px;
            border: 6px solid white;
            box-shadow: 0 8px 24px rgba(0, 98, 246, 0.2);
            transition: transform 0.3s ease;
        }

        .profile-picture-preview:hover {
            transform: scale(1.05);
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0062F6;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            font-family: inherit;
            transition: all 0.3s ease;
            background: white;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0062F6;
            box-shadow: 0 0 0 4px rgba(0, 98, 246, 0.1);
            background: #ffffff;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: flex;
            gap: 24px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .btn {
            padding: 14px 32px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0062F6 0%, #0052D4 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 98, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 98, 246, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .button-group {
            display: flex;
            gap: 16px;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .alert {
            padding: 16px 20px;
            margin-bottom: 24px;
            border-radius: 10px;
            text-align: center;
            font-weight: 500;
            animation: slideInDown 0.5s ease-out;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%);
            color: #059669;
            border: 2px solid rgba(16, 185, 129, 0.3);
        }

        .alert-error {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%);
            color: #dc2626;
            border: 2px solid rgba(220, 38, 38, 0.3);
        }
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
        }
        footer.footer {
            margin-top: auto;
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
        <a href="../pages/location.php">
            <div class="location">
                <img class="location" src="../image/location-icon.png" alt="location-icon">
            </div>
        </a>
        <div class="track">
            <a href="../pages/track.php"><img class="track" src="../image/track-icon.png" alt="track-icon"></a>
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

        <!-- Modern Profile Dropdown CSS -->
        <style>
            .profile-dropdown { position: relative; display: inline-block; }
            
            .dropdown-content { display: none; position: absolute; top: 110%; right: 0; background: white; border-radius: 8px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); border: 1px solid #e0e0e0; min-width: 200px; z-index: 1000; }
            
            .dropdown-content a { display: flex; align-items: center; padding: 12px 16px; color: #333; font-size: 14px; font-weight: 500; text-decoration: none; transition: all 0.2s ease; border-left: 3px solid transparent; }
            
            .dropdown-content a:hover { background: #f5f5f5; color: #0062F6; border-left-color: #0062F6; }
            
            .dropdown-content a .material-icons { font-size: 18px; margin-right: 12px; display: flex; align-items: center; }
            
            .profile-dropdown.active .dropdown-content { display: block; animation: slideDown 0.25s ease-out; }
            
            @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
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
            <div class="dropdown-content">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="../user/profile.php"><span class="material-icons">person</span>View Profile</a>
                    <a href="../user/edit-profile.php"><span class="material-icons">edit</span>Edit Profile</a>
                    <a href="../user/logout.php"><span class="material-icons">logout</span>Log Out</a>
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

    <div class="menu" id="main-menu">
        <a href="../index.php">HOME</a>
        <a href="../pages/product.php">PRODUCTS</a>
        <a href="../products/desktop.php">DESKTOP</a>
        <a href="../products/laptop.php">LAPTOP</a>
        <a href="../pages/brands.php">BRANDS</a>
    </div>

    <div class="breadcrumb">
        <a href="../index.php">Home</a> >
        <a>Edit Profile</a>
    </div>

    <div class="main-content">
        <div class="edit-profile-container">
            <h2>Edit Profile</h2>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Profile Picture Upload Section -->
            <div class="profile-picture-section">
                <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" class="profile-picture-preview">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="profile_picture">Upload New Profile Picture</label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required>
                    </div>
                    <button type="submit" name="upload_picture" class="btn btn-secondary">Upload Picture</button>
                </form>
            </div>

            <!-- Profile Information Form -->
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo isset($user['first_name']) ? htmlspecialchars($user['first_name']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo isset($user['last_name']) ? htmlspecialchars($user['last_name']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number *</label>
                    <input type="text" id="phone_number" name="phone_number" value="<?php echo isset($user['phone_number']) ? htmlspecialchars($user['phone_number']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="address">Address *</label>
                    <textarea id="address" name="address" required><?php echo isset($user['address']) ? htmlspecialchars($user['address']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="postal_code">Postal Code *</label>
                    <input type="text" id="postal_code" name="postal_code" value="<?php echo isset($user['postal_code']) ? htmlspecialchars($user['postal_code']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">New Password (leave blank to keep current password)</label>
                    <input type="password" id="password" name="password" placeholder="Enter new password">
                </div>

                <div class="button-group">
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                    <a href="../index.php"><button type="button" class="btn btn-secondary">Cancel</button></a>
                </div>
            </form>
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
        // Modern dropdown toggle function
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
<script src="../js/header-animations.js"></script>
<script src="../js/jquery-animations.js"></script>
</body>
</html>

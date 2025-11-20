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
            $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $filetype;
            $upload_path = 'uploads/' . $new_filename;
            
            // Create uploads directory if it doesn't exist
            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                $update_pic = "UPDATE users SET profile_picture = ? WHERE id = ?";
                $stmt = $conn->prepare($update_pic);
                $stmt->bind_param("si", $upload_path, $user_id);
                
                if ($stmt->execute()) {
                    $success_message = "Profile picture updated successfully!";
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
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get profile picture
$profile_picture = !empty($user['profile_picture']) ? $user['profile_picture'] : '../image/login-icon.png';
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/design.css" />
    <link rel="stylesheet" href="../css/animations.css" />
    <meta charset="UTF-8">
    <title>Edit Profile - SMARTSOLUTIONS</title>
    <style>
        .edit-profile-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .edit-profile-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .profile-picture-section {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .profile-picture-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 4px solid #ddd;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        .form-row {
            display: flex;
            gap: 20px;
        }
        .form-row .form-group {
            flex: 1;
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .btn-primary {
            background-color: #007BFF;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #545b62;
        }
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
            <div class="search-icon"></div>
        </div>
        <a href="location.php"><div class="location">
            <img class="location" src="../image/location-icon.png" alt="location-icon">
        </a>
        </div>
        <div class="track">
            <a href="../track.php"><img class="track" src="../image/track-icon.png" alt="track-icon"></a>
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
                 src="<?php echo $profile_picture; ?>" 
                 alt="profile-icon" 
                 style="border-radius: 50%; width: 40px; height: 40px; object-fit: cover;">
        </a>
        <div id="dropdown-menu" class="dropdown-content">
            <a href="profile.php">View Profile</a>
            <a href="edit-profile.php">Edit Profile</a>
            <a href="logout.php">Log Out</a>
        </div>
        </div>
        <div class="login-text">
            <a href="edit-profile.php"></a>
        </div>
    </div>
    </header>

    <div class="menu">
        <a href="../index.php">HOME</a>
        <a href="../product.php">PRODUCTS</a>
        <a href="../desktop.php">DESKTOP</a>
        <a href="../laptop.php">LAPTOP</a>
        <a href="../brands.php">BRANDS</a>
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
                        <input type="file" id="profile_picture" name="profile_picture" accept="../image/*">
                    </div>
                    <button type="submit" name="upload_picture" class="btn btn-secondary">Upload Picture</button>
                </form>
            </div>

            <!-- Profile Information Form -->
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number *</label>
                    <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="address">Address *</label>
                    <textarea id="address" name="address" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="postal_code">Postal Code *</label>
                    <input type="text" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>" required>
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
        function toggleDropdown() {
            var dropdown = document.getElementById("dropdown-menu");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        window.onclick = function(event) {
            if (!event.target.matches('.login')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    dropdowns[i].style.display = "none";
                }
            }
        }
    </script>
<script src="js/search.js"></script>
</body>
</html>

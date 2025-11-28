<?php
session_start();

// Security check (new system uses is_admin flag)
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    // Also check for old admin session for backwards compatibility
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: ../user/register.php");
        exit();
    }
}

// Check if user ID is provided
if (!isset($_GET['id'])) {
    header("Location: admin_users.php");
    exit();
}

$user_id = intval($_GET['id']);

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user exists
$check_result = $conn->query("SELECT * FROM users WHERE id = $user_id");

if ($check_result->num_rows == 0) {
    $_SESSION['error'] = "User not found!";
    header("Location: admin_users.php");
    $conn->close();
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $address = $conn->real_escape_string($_POST['address']);
    $postal_code = $conn->real_escape_string($_POST['postal_code']);
    $profile_picture = $user['profile_picture']; // Default to existing image
    
    // Handle image upload
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "../image/";
        $file_name = basename($_FILES['profile_picture']['name']);
        // Add timestamp to avoid overwriting files with same name
        $file_name = time() . "_" . $file_name;
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Validate file type
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                // Store relative path from root: image/filename.jpg
                $profile_picture = "image/" . $file_name;
            }
        }
    }
    
    $update_query = "UPDATE users SET 
                    first_name = '$first_name', 
                    last_name = '$last_name', 
                    email = '$email',
                    phone_number = '$phone_number',
                    address = '$address',
                    postal_code = '$postal_code',
                    profile_picture = '$profile_picture'
                    WHERE id = $user_id";
    
    if ($conn->query($update_query) === TRUE) {
        $_SESSION['success'] = "User updated successfully!";
        header("Location: admin_users.php");
        $conn->close();
        exit();
    } else {
        $error = "Error updating user: " . $conn->error;
    }
}

// Fetch user data
$user = $check_result->fetch_assoc();
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details - Admin</title>
    <link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .image-preview {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .image-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .image-preview p {
            color: #666;
            font-size: 12px;
            margin-top: 10px;
        }
        
        .user-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            font-size: 14px;
            color: #666;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 14px;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        input[type="file"]:focus,
        textarea:focus {
            outline: none;
            border-color: #2c3e50;
            box-shadow: 0 0 5px rgba(44, 62, 80, 0.3);
        }
        
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            justify-content: center;
        }
        
        button {
            padding: 12px 40px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            min-width: 150px;
        }
        
        .btn-save {
            background: #2c3e50;
            color: white;
        }
        
        .btn-save:hover {
            background: #1a252f;
        }
        
        .btn-cancel {
            background: #95a5a6;
            color: white;
        }
        
        .btn-cancel:hover {
            background: #7f8c8d;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #f44336;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .form-row .form-group {
            margin-bottom: 0;
        }
        
        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Details</h1>
        <div class="user-info">
            <strong>User ID:</strong> <?php echo $user['id']; ?> | 
            <strong>Registered:</strong> <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
        </div>
        
        <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <?php if (!empty($user['profile_picture'])): ?>
            <div class="image-preview">
                <p><strong>Current Profile Picture:</strong></p>
                <?php 
                $profile_pic = "../image/login-icon.png";
                if (!empty($user['profile_picture'])) {
                    // Check if it's a full URL (from OAuth like Google)
                    if (strpos($user['profile_picture'], 'http://') === 0 || strpos($user['profile_picture'], 'https://') === 0) {
                        $profile_pic = $user['profile_picture'];
                    } elseif (strpos($user['profile_picture'], '/') === 0) {
                        $profile_pic = $user['profile_picture'];
                    } else {
                        $profile_pic = '/ITP122/' . $user['profile_picture'];
                    }
                }
                ?>
                <img src="<?php echo $profile_pic; ?>" alt="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" style="max-width: 200px; max-height: 200px; border-radius: 8px; object-fit: cover;">
            </div>
            <?php else: ?>
            <div class="image-preview">
                <p><strong>Current Profile Picture:</strong></p>
                <img src="../image/login-icon.png" alt="Default Profile" style="max-width: 200px; max-height: 200px; border-radius: 8px; object-fit: cover;">
            </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="profile_picture">Upload New Profile Picture</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                <p style="color: #999; font-size: 12px; margin-top: 5px;">Accepted: JPG, JPEG, PNG, GIF</p>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="postal_code">Postal Code</label>
                <input type="text" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>">
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn-save">Save Changes</button>
                <button type="button" class="btn-cancel" onclick="window.location.href='admin_users.php'">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>

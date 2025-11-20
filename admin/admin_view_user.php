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
    header("Location: users.php");
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
    header("Location: users.php");
    $conn->close();
    exit();
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
    <title>View User - Admin</title>
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
            max-width: 700px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
        }
        
        .profile-section {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid #2c3e50;
        }
        
        .user-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        
        .info-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 20px;
            margin: 15px 0;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .info-value {
            color: #555;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
        }
        
        button, a {
            padding: 12px 40px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }
        
        .btn-edit {
            background: #2c3e50;
            color: white;
            min-width: 150px;
        }
        
        .btn-edit:hover {
            background: #1a252f;
        }
        
        .btn-back {
            background: #95a5a6;
            color: white;
            min-width: 150px;
        }
        
        .btn-back:hover {
            background: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Details</h1>
        
        <div class="profile-section">
            <?php 
            $profile_pic = "../image/login-icon.png";
            if (!empty($user['profile_picture'])) {
                $profile_pic = '/ITP122/' . $user['profile_picture'];
            }
            ?>
            <img src="<?php echo $profile_pic; ?>" alt="Profile" class="profile-picture" onerror="this.src='../image/login-icon.png'">
            <h2 style="color: #2c3e50; margin-top: 15px;"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
        </div>
        
        <div class="user-info">
            <div class="info-row">
                <div class="info-label">User ID</div>
                <div class="info-value"><?php echo htmlspecialchars($user['id']); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">First Name</div>
                <div class="info-value"><?php echo htmlspecialchars($user['first_name'] ?? 'N/A'); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Last Name</div>
                <div class="info-value"><?php echo htmlspecialchars($user['last_name'] ?? 'N/A'); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Phone</div>
                <div class="info-value"><?php echo htmlspecialchars($user['phone_number'] ?? 'N/A'); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Address</div>
                <div class="info-value"><?php echo htmlspecialchars($user['address'] ?? 'N/A'); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">City</div>
                <div class="info-value"><?php echo htmlspecialchars($user['postal_code'] ?? 'N/A'); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Registered</div>
                <div class="info-value"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div>
            </div>
        </div>
        
        <div class="button-group">
            <button class="btn-edit" onclick="window.location.href='admin_user_details.php?id=<?php echo $user['id']; ?>'">Edit User</button>
            <button class="btn-back" onclick="window.location.href='admin_users.php'">Back to Users</button>
        </div>
    </div>
</body>
</html>

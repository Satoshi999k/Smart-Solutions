<?php
session_start();

// Security check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
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

// Handle deletion confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delete_query = "DELETE FROM users WHERE id = $user_id";
    
    if ($conn->query($delete_query) === TRUE) {
        $_SESSION['success'] = "User deleted successfully!";
        header("Location: users.php");
        $conn->close();
        exit();
    } else {
        $error = "Error deleting user: " . $conn->error;
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
    <title>Delete User - Admin</title>
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
            margin-bottom: 30px;
        }
        
        .user-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 25px;
            border-left: 4px solid #f44336;
        }
        
        .user-info h3 {
            color: #f44336;
            margin-bottom: 15px;
        }
        
        .user-info p {
            margin: 8px 0;
            color: #555;
        }
        
        .user-info strong {
            color: #2c3e50;
        }
        
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            color: #856404;
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
        
        .btn-delete {
            background: #f44336;
            color: white;
        }
        
        .btn-delete:hover {
            background: #d32f2f;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Delete User</h1>
        
        <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="warning">
            ⚠️ <strong>Warning:</strong> This action cannot be undone. All user data will be permanently deleted.
        </div>
        
        <div class="user-info">
            <h3>Are you sure you want to delete this user?</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone_number'] ?? 'N/A'); ?></p>
            <p><strong>Registered:</strong> <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
        </div>
        
        <form method="POST">
            <div class="button-group">
                <button type="submit" class="btn-delete">Yes, Delete User</button>
                <button type="button" class="btn-cancel" onclick="window.location.href='admin_users.php'">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>

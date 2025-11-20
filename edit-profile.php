<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Include database connection
include('conn.php');

// Debug: Check session
if (!isset($_SESSION['user_id'])) {
    die("User ID not found in session. Are you logged in?");
}

$user_id = $_SESSION['user_id'];

// Check database connection
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch user details
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if (!$result) {
    die("SQL Error: " . $conn->error);
}

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("No user found for ID $user_id.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $profile_pic = $_POST['profile_pic'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $postal_code = $_POST['postal_code'];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $password_update = "password = '$hashed_password',";
    } else {
        $password_update = "";
    }

    $update_sql = "UPDATE users SET 
        first_name = '$first_name',
        last_name = '$last_name',
        email = '$email',
        $password_update
        profile_pic = '$profile_pic',
        address = '$address',
        phone_number = '$phone_number',
        postal_code = '$postal_code'
        WHERE id = $user_id";

    if ($conn->query($update_sql) === TRUE) {
        echo "Profile updated successfully!";
    } else {
        die("Update Error: " . $conn->error);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="shortcut icon" href="image/smartsolutionslogo.jpg" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="design.css" />
    <title>Edit Profile - SMART SOLUTIONS COMPUTER SHOP</title>
    <style>
        /* Form CSS */
        .edit-profile-form {
            background-color: #fff;
            padding: 20px;
            max-width: 600px;
            margin: 30px auto;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .edit-profile-form h2 {
            margin-bottom: 20px;
            text-align: center;
            font-size: 24px;
        }
        .edit-profile-form label {
            margin-bottom: 8px;
            font-weight: bold;
        }
        .edit-profile-form input, 
        .edit-profile-form textarea, 
        .edit-profile-form button {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .edit-profile-form button {
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .edit-profile-form button:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <div class="edit-profile-form">
        <h2>Edit Profile</h2>
        <form action="edit_profile.php" method="POST">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter a new password (leave blank if not changing)">

            <label for="profile_pic">Profile Picture (URL):</label>
            <input type="text" id="profile_pic" name="profile_pic" value="<?php echo htmlspecialchars($user['profile_pic']); ?>">

            <label for="address">Address:</label>
            <textarea id="address" name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>

            <label for="postal_code">Postal Code:</label>
            <input type="text" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code']); ?>" required>

            <button type="submit">Update Profile</button>
        </form>
    </div>
<script src="search.js"></script>
</body>
</html>

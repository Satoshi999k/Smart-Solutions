<?php
// Script to update admin account email to "admin"

$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if admin account exists with old email
$check_sql = "SELECT * FROM users WHERE is_admin = 1";
$result = $conn->query($check_sql);

if ($result && $result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    
    // Update email to just "admin"
    $update_sql = "UPDATE users SET email = 'admin' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $admin['id']);
    
    if ($stmt->execute()) {
        echo "✅ Admin email updated to 'admin'\n";
    } else {
        echo "❌ Error updating admin email: " . $stmt->error . "\n";
    }
    $stmt->close();
} else {
    echo "⚠️ No admin account found\n";
}

// Display admin account details
echo "\n📋 Admin Account Details:\n";
$show_sql = "SELECT id, email, first_name, is_admin FROM users WHERE is_admin = 1";
$show_result = $conn->query($show_sql);

if ($show_result && $show_result->num_rows > 0) {
    $admin = $show_result->fetch_assoc();
    echo "Email: " . $admin['email'] . "\n";
    echo "Name: " . $admin['first_name'] . "\n";
    echo "Password: Admin123!\n";
} else {
    echo "No admin account found\n";
}

$conn->close();
?>

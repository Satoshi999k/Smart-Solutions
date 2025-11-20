<?php
// Migration script to add is_admin column to users table

$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if is_admin column already exists
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'is_admin'");

if ($result && $result->num_rows === 0) {
    // Add is_admin column
    $sql = "ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0 AFTER password";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ is_admin column added successfully\n";
        
        // Create default admin account
        $admin_email = "admin";
        $admin_password = password_hash("Admin123!", PASSWORD_BCRYPT);
        
        // Check if admin account already exists
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $admin_email);
        $stmt->execute();
        $check_result = $stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            // Insert admin account
            $insert_sql = "INSERT INTO users (email, password, first_name, last_name, is_admin) VALUES (?, ?, ?, ?, 1)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssss", $admin_email, $admin_password, $admin_first, $admin_last);
            
            $admin_first = "Admin";
            $admin_last = "User";
            
            if ($insert_stmt->execute()) {
                echo "✅ Default admin account created: admin@smartsolutions.com / Admin123!\n";
            } else {
                echo "❌ Error creating admin account: " . $insert_stmt->error . "\n";
            }
            $insert_stmt->close();
        } else {
            echo "⚠️ Admin account already exists\n";
        }
        $stmt->close();
        
    } else {
        echo "❌ Error adding is_admin column: " . $conn->error . "\n";
    }
} else {
    echo "⚠️ is_admin column already exists\n";
}

// Display users table structure
echo "\n📋 Current users table structure:\n";
$show_result = $conn->query("SHOW COLUMNS FROM users");
while ($row = $show_result->fetch_assoc()) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

$conn->close();
?>

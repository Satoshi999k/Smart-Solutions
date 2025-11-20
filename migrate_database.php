<?php
/**
 * Database Migration Script
 * Adds missing user_id column to orders table
 * Run this once to update your database
 */

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Starting database migration...\n\n";

// 1. Check if orders table exists
$table_check = $conn->query("SHOW TABLES LIKE 'orders'");
if (!$table_check || $table_check->num_rows === 0) {
    echo "❌ Orders table does not exist. Creating it...\n";
    
    $create_orders = "CREATE TABLE IF NOT EXISTS `orders` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT UNSIGNED DEFAULT 0,
        `customer_details` JSON DEFAULT NULL,
        `order_details` JSON DEFAULT NULL,
        `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        `status` VARCHAR(50) DEFAULT 'Pending',
        `order_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        INDEX idx_user_id (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    
    if ($conn->query($create_orders)) {
        echo "✅ Orders table created successfully!\n\n";
    } else {
        echo "❌ Error creating orders table: " . $conn->error . "\n";
    }
} else {
    echo "✅ Orders table exists.\n";
    
    // 2. Check if user_id column exists
    $columns_check = $conn->query("SHOW COLUMNS FROM orders LIKE 'user_id'");
    
    if ($columns_check && $columns_check->num_rows === 0) {
        echo "❌ user_id column not found. Adding it...\n";
        
        $alter_user_id = "ALTER TABLE orders ADD COLUMN user_id INT UNSIGNED DEFAULT 0 AFTER id";
        
        if ($conn->query($alter_user_id)) {
            echo "✅ user_id column added successfully!\n";
        } else {
            echo "❌ Error adding user_id column: " . $conn->error . "\n";
        }
    } else {
        echo "✅ user_id column already exists.\n";
    }
    
    // 3. Check if status column exists
    $status_check = $conn->query("SHOW COLUMNS FROM orders LIKE 'status'");
    
    if ($status_check && $status_check->num_rows === 0) {
        echo "❌ status column not found. Adding it...\n";
        
        $alter_status = "ALTER TABLE orders ADD COLUMN status VARCHAR(50) DEFAULT 'Pending' AFTER total_price";
        
        if ($conn->query($alter_status)) {
            echo "✅ status column added successfully!\n";
        } else {
            echo "❌ Error adding status column: " . $conn->error . "\n";
        }
    } else {
        echo "✅ status column already exists.\n";
    }
    
    // 4. Check if order_date column exists
    $date_check = $conn->query("SHOW COLUMNS FROM orders LIKE 'order_date'");
    
    if ($date_check && $date_check->num_rows === 0) {
        echo "❌ order_date column not found. Adding it...\n";
        
        $alter_date = "ALTER TABLE orders ADD COLUMN order_date DATETIME DEFAULT CURRENT_TIMESTAMP AFTER status";
        
        if ($conn->query($alter_date)) {
            echo "✅ order_date column added successfully!\n";
        } else {
            echo "❌ Error adding order_date column: " . $conn->error . "\n";
        }
    } else {
        echo "✅ order_date column already exists.\n";
    }
    
    // 5. Add index on user_id if it doesn't exist
    $index_check = $conn->query("SHOW INDEX FROM orders WHERE Column_name='user_id' AND Seq_in_index=1");
    
    if ($index_check && $index_check->num_rows === 0) {
        echo "❌ Index on user_id not found. Adding it...\n";
        
        $add_index = "ALTER TABLE orders ADD INDEX idx_user_id (user_id)";
        
        if ($conn->query($add_index)) {
            echo "✅ Index on user_id added successfully!\n";
        } else {
            echo "❌ Error adding index: " . $conn->error . "\n";
        }
    } else {
        echo "✅ Index on user_id already exists.\n";
    }
}

// 6. Verify final structure
echo "\n" . str_repeat("=", 50) . "\n";
echo "Final Orders Table Structure:\n";
echo str_repeat("=", 50) . "\n";

$final_check = $conn->query("DESCRIBE orders");
if ($final_check) {
    while ($col = $final_check->fetch_assoc()) {
        echo sprintf("%-20s %-20s %-15s %-10s\n", 
            $col['Field'], 
            $col['Type'], 
            $col['Null'],
            $col['Key']
        );
    }
}

echo "\n✅ Database migration completed successfully!\n";
echo "\nYour orders table is now ready to store user orders.\n";

$conn->close();
?>

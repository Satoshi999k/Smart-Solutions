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

// Check if product ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "No product ID provided!";
    header("Location: products.php");
    exit();
}

$product_id = intval($_GET['id']);

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if product exists
$check_result = $conn->query("SELECT * FROM products WHERE id = $product_id");

if ($check_result->num_rows == 0) {
    $_SESSION['error'] = "Product not found!";
    header("Location: products.php");
    $conn->close();
    exit();
}

// Get product info for display
$product = $check_result->fetch_assoc();

// Handle confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    // Delete the product
    $delete_query = "DELETE FROM products WHERE id = $product_id";
    
    if ($conn->query($delete_query) === TRUE) {
        $_SESSION['success'] = "Product deleted successfully!";
        $conn->close();
        header("Location: products.php");
        exit();
    } else {
        $error = "Error deleting product: " . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product - Confirm</title>
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
            max-width: 500px;
            margin: 100px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .warning-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #f44336;
            margin-bottom: 15px;
            font-size: 28px;
        }
        
        .product-info {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
        }
        
        .product-info p {
            margin: 10px 0;
            color: #555;
        }
        
        .product-info strong {
            color: #2c3e50;
        }
        
        .warning-text {
            color: #d32f2f;
            font-weight: 600;
            margin: 20px 0;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        
        button, a {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .btn-delete {
            background: #f44336;
            color: white;
        }
        
        .btn-delete:hover {
            background: #d32f2f;
        }
        
        .btn-cancel {
            background: #4caf50;
            color: white;
        }
        
        .btn-cancel:hover {
            background: #45a049;
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
        <div class="warning-icon">⚠️</div>
        <h1>Delete Product?</h1>
        
        <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="product-info">
            <p><strong>Product ID:</strong> <?php echo $product['id']; ?></p>
            <p><strong>Product Name:</strong> <?php echo htmlspecialchars($product['name']); ?></p>
            <p><strong>Price:</strong> ₱<?php echo number_format($product['price'], 2); ?></p>
            <p><strong>Stock:</strong> <?php echo $product['stock']; ?></p>
        </div>
        
        <p class="warning-text">This action cannot be undone. This product will be permanently deleted from the database.</p>
        
        <form method="POST">
            <div class="button-group">
                <button type="submit" name="confirm_delete" value="1" class="btn-delete">Yes, Delete</button>
                <a href="admin_products.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>

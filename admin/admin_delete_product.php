<?php
session_start();

// Security check (new system uses is_admin flag)
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    // Also check for old admin session for backwards compatibility
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }
}

// Check if product ID is provided
if (!isset($_POST['id']) && !isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No product ID provided']);
    exit();
}

$product_id = intval($_POST['id'] ?? $_GET['id']);

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// Check if product exists
$check_result = $conn->query("SELECT * FROM products WHERE id = $product_id");

if ($check_result->num_rows == 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    $conn->close();
    exit();
}

// Handle confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    // Delete the product
    $delete_query = "DELETE FROM products WHERE id = $product_id";
    
    if ($conn->query($delete_query) === TRUE) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        $conn->close();
        exit();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error deleting product: ' . $conn->error]);
        $conn->close();
        exit();
    }
}

$conn->close();
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product - Confirm</title>
    <link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined|Material+Icons" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .material-icons {
            font-family: 'Material Icons';
            font-weight: normal;
            font-style: normal;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
        }
        
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 520px;
            width: 100%;
            animation: slideIn 0.4s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header {
            background: linear-gradient(135deg, #f44336 0%, #e53935 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .icon-wrapper {
            font-size: 48px;
            color: white;
            margin-bottom: 15px;
            animation: iconPulse 2s ease-in-out infinite;
        }
        
        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .header h1 {
            color: white;
            font-size: 28px;
            margin: 0;
            font-weight: 700;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
        }
        
        .content {
            padding: 40px;
        }
        
        .product-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 4px solid #f44336;
        }
        
        .product-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            font-size: 14px;
        }
        
        .product-row:last-child {
            margin-bottom: 0;
        }
        
        .product-label {
            font-weight: 600;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .product-label i {
            font-size: 18px;
            color: #667eea;
        }
        
        .product-value {
            color: #34495e;
            font-weight: 500;
        }
        
        .warning-section {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 25px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        
        .warning-section i {
            font-size: 20px;
            color: #ff9800;
            margin-top: 2px;
            flex-shrink: 0;
        }
        
        .warning-text {
            color: #856404;
            font-size: 13px;
            line-height: 1.6;
            font-weight: 500;
        }
        
        .error {
            background: #ffebee;
            border: 1px solid #ef5350;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            color: #c62828;
            font-size: 13px;
        }
        
        .error i {
            font-size: 20px;
            margin-top: 2px;
            flex-shrink: 0;
        }
        
        .button-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        
        button, a.btn {
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #f44336 0%, #e53935 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(244, 67, 54, 0.3);
        }
        
        .btn-delete:hover {
            background: linear-gradient(135deg, #e53935 0%, #d32f2f 100%);
            box-shadow: 0 8px 25px rgba(244, 67, 54, 0.4);
            transform: translateY(-2px);
        }
        
        .btn-delete:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(244, 67, 54, 0.3);
        }
        
        .btn-cancel {
            background: linear-gradient(135deg, #f5f5f5 0%, #eeeeee 100%);
            color: #2c3e50;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .btn-cancel:hover {
            background: linear-gradient(135deg, #eeeeee 0%, #e0e0e0 100%);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
            color: #1a252f;
        }
        
        .btn-cancel:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 480px) {
            .container {
                border-radius: 12px;
            }
            
            .content {
                padding: 24px;
            }
            
            .button-group {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon-wrapper">
                <i class="material-icons">delete_forever</i>
            </div>
            <h1>Delete Product</h1>
        </div>
        
        <div class="content">
            <?php if (isset($error)): ?>
            <div class="error">
                <i class="material-icons">error_outline</i>
                <span><?php echo $error; ?></span>
            </div>
            <?php endif; ?>
            
            <div class="product-card">
                <div class="product-row">
                    <span class="product-label">
                        <i class="material-icons">fingerprint</i>
                        Product ID
                    </span>
                    <span class="product-value"><?php echo $product['id']; ?></span>
                </div>
                <div class="product-row">
                    <span class="product-label">
                        <i class="material-icons">local_offer</i>
                        Product Name
                    </span>
                    <span class="product-value"><?php echo htmlspecialchars($product['name']); ?></span>
                </div>
                <div class="product-row">
                    <span class="product-label">
                        <i class="material-icons">attach_money</i>
                        Price
                    </span>
                    <span class="product-value">₱<?php echo number_format($product['price'], 2); ?></span>
                </div>
                <div class="product-row">
                    <span class="product-label">
                        <i class="material-icons">inventory_2</i>
                        Stock
                    </span>
                    <span class="product-value"><?php echo $product['stock']; ?> units</span>
                </div>
            </div>
            
            <div class="warning-section">
                <i class="material-icons">warning_amber</i>
                <div class="warning-text">
                    <strong>This action cannot be undone.</strong> This product will be permanently deleted from the database and cannot be recovered.
                </div>
            </div>
            
            <form method="POST">
                <div class="button-group">
                    <button type="submit" name="confirm_delete" value="1" class="btn-delete">
                        <i class="material-icons">delete</i>
                        Delete
                    </button>
                    <a href="admin_products.php" class="btn btn-cancel">
                        <i class="material-icons">cancel</i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

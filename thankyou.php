<?php
session_start();

// Check if there's an order ID in session
if (!isset($_SESSION['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_SESSION['order_id'];
$order_total = $_SESSION['order_total'] ?? 0;
$customer_name = $_SESSION['customer_name'] ?? 'Customer';

// Get order details from database
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM orders WHERE id = $order_id");
$order = $result->fetch_assoc();
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Smart Solutions</title>
    <link rel="shortcut icon" href="image/smartsolutionslogo.jpg" type="image/x-icon">
    <link rel="stylesheet" href="css/design.css">
    <link rel="stylesheet" href="css/animations.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        header {
            padding: 30px 0;
            text-align: center;
            width: 100%;
        }
        
        .logoheader {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        
        .logocp {
            text-align: center;
        }
        
        .logocp img {
            height: 110px;
            width: 130px;
            max-width: 150px;
            display: inline-block;
            text-align: center;
        }
        
        .thank-you-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .thank-you-header {
            color: #2c3e50;
            margin-bottom: 30px;
        }
        
        .success-icon {
            font-size: 80px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        
        .order-info {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 5px;
            margin: 25px 0;
            border-left: 4px solid #4caf50;
            text-align: left;
        }
        
        .order-info p {
            margin: 10px 0;
            font-size: 16px;
        }
        
        .order-info strong {
            color: #2c3e50;
        }
        
        .order-items {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 5px;
            margin: 25px 0;
            text-align: left;
        }
        
        .order-items h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .item-row:last-child {
            border-bottom: none;
        }
        
        .item-total {
            font-weight: bold;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
            margin-top: 15px;
            color: #2c3e50;
        }
        
        .continue-btn {
            background: #2c3e50;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 30px;
            transition: background 0.3s;
        }
        
        .continue-btn:hover {
            background: #1a252f;
        }
    </style>
</head>
<body>
    <header>
        <div class="logoheader">
            <div class="logocp">
                <img src="image/logo.png" alt="Smart Solutions Logo">
            </div>
        </div>
    </header>
    
    <div class="thank-you-container">
        <div class="success-icon">✓</div>
        <h1 class="thank-you-header">Thank You for Your Order!</h1>
        
        <p style="font-size: 18px; color: #666; margin-bottom: 30px;">
            Your order has been successfully placed and saved to our system.
        </p>
        
        <div class="order-info">
            <p><strong>Order ID:</strong> #<?php echo htmlspecialchars($order_id); ?></p>
            <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
            <p><strong>Order Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
        </div>
        
        <?php if ($order): 
            $order_details = json_decode($order['order_details'], true);
            $customer_details = json_decode($order['customer_details'], true);
        ?>
        
        <div class="order-items">
            <h3>Order Items</h3>
            <?php if (isset($order_details['items'])): ?>
                <?php foreach ($order_details['items'] as $item): ?>
                <div class="item-row">
                    <span><?php echo htmlspecialchars($item['product_name']); ?> x <?php echo $item['quantity']; ?></span>
                    <span>₱<?php echo number_format($item['subtotal'], 2); ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="item-total">
                <strong>Total Amount:</strong> ₱<?php echo number_format($order['total_price'], 2); ?>
            </div>
        </div>
        
        <div class="order-info">
            <h3 style="color: #2c3e50; margin-bottom: 15px;">Delivery Address</h3>
            <p><?php echo htmlspecialchars($customer_details['address'] ?? ''); ?></p>
            <p><?php echo htmlspecialchars($customer_details['city'] . ', ' . $customer_details['region'] . ' ' . $customer_details['postal_code']); ?></p>
            <p><?php echo htmlspecialchars($customer_details['country']); ?></p>
            <p style="margin-top: 15px;"><strong>Phone:</strong> <?php echo htmlspecialchars($customer_details['phone']); ?></p>
        </div>
        
        <?php endif; ?>
        
        <p style="color: #666; margin-top: 30px;">
            A confirmation email will be sent shortly. You can track your order status from your account.
        </p>
        
        <a href="index.php" class="continue-btn">Continue Shopping</a>
    </div>
    
    <script src="js/search.js"></script>
    <script>
        // Clear cart from localStorage when order is completed
        localStorage.removeItem('cart');
        localStorage.removeItem('cartCount');
    </script>
</body>
</html>

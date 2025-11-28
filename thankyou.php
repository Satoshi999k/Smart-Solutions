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
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0062F6 0%, #0052cc 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .thank-you-container {
            max-width: 900px;
            margin: 60px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 98, 246, 0.3);
            overflow: hidden;
        }

        .success-header {
            background: linear-gradient(135deg, #0062F6 0%, #0052cc 100%);
            padding: 60px 40px;
            text-align: center;
            color: white;
            position: relative;
            box-shadow: 0 20px 60px rgba(0, 98, 246, 0.4), inset 0 -2px 10px rgba(0, 0, 0, 0.1);
            animation: waveGradient 6s ease-in-out infinite;
            background-size: 200% 200%;
        }

        @keyframes waveGradient {
            0% {
                background: linear-gradient(135deg, #0062F6 0%, #0052cc 100%);
            }
            25% {
                background: linear-gradient(180deg, #0062F6 0%, #0052cc 100%);
            }
            50% {
                background: linear-gradient(45deg, #0052cc 0%, #0062F6 100%);
            }
            75% {
                background: linear-gradient(0deg, #0062F6 0%, #0052cc 100%);
            }
            100% {
                background: linear-gradient(135deg, #0062F6 0%, #0052cc 100%);
            }
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 60px;
            color: #0062F6;
            animation: scaleIn 0.6s ease-out;
            box-shadow: 0 10px 30px rgba(0, 98, 246, 0.2);
        }

        .success-icon .material-icons {
            font-size: 60px;
            color: #0062F6;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .thank-you-header {
            color: white;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .thank-you-subtitle {
            color: rgba(255,255,255,0.95);
            font-size: 16px;
            font-weight: 300;
        }

        .content {
            padding: 50px 40px;
        }

        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .info-card {
            background: #f0f7ff;
            padding: 25px;
            border-radius: 10px;
            border-left: 4px solid #0062F6;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 98, 246, 0.1);
        }

        .info-label {
            color: #999;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .info-value {
            color: #0062F6;
            font-size: 20px;
            font-weight: 700;
        }

        .section-title {
            color: #2c3e50;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #0062F6;
        }

        .order-items {
            margin-bottom: 40px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 0;
            border-bottom: 1px solid #e0e0e0;
            font-size: 15px;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-name {
            color: #2c3e50;
            font-weight: 500;
            flex: 1;
        }

        .item-qty {
            color: #999;
            font-size: 14px;
            margin: 0 20px;
            min-width: 40px;
            text-align: center;
        }

        .item-price {
            color: #0062F6;
            font-weight: 700;
            min-width: 80px;
            text-align: right;
        }

        .total-row {
            background: linear-gradient(135deg, #0062F6 0%, #0052cc 100%);
            color: white;
            padding: 20px 25px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            font-size: 18px;
            font-weight: 700;
            box-shadow: 0 10px 25px rgba(0, 98, 246, 0.2);
        }

        .delivery-info {
            background: #f0f7ff;
            padding: 25px;
            border-radius: 10px;
            border-left: 4px solid #0062F6;
        }

        .delivery-info p {
            color: #2c3e50;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .delivery-info strong {
            color: #0062F6;
        }

        .confirmation-message {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 20px;
            border-radius: 10px;
            color: #2c3e50;
            margin: 30px 0;
            font-size: 15px;
            line-height: 1.6;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .confirmation-message .material-icons {
            font-size: 24px;
            color: #4caf50;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .continue-btn, .track-btn {
            padding: 14px 35px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .continue-btn {
            background: linear-gradient(135deg, #0062F6 0%, #0052cc 100%);
            color: white;
            box-shadow: 0 10px 25px rgba(0, 98, 246, 0.2);
        }

        .continue-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0, 98, 246, 0.4);
        }

        .track-btn {
            background: white;
            color: #0062F6;
            border: 2px solid #0062F6;
        }

        .track-btn:hover {
            background: #0062F6;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 98, 246, 0.2);
        }

        @media (max-width: 768px) {
            .content {
                padding: 30px 20px;
            }

            .thank-you-header {
                font-size: 28px;
            }

            .success-header {
                padding: 40px 20px;
            }

            .order-info-grid {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }

            .continue-btn, .track-btn {
                width: 100%;
            }

            .item-row {
                flex-wrap: wrap;
                gap: 10px;
            }

            .item-qty, .item-price {
                margin: 0;
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="thank-you-container">
        <div class="success-header">
            <div class="success-icon">
                <span class="material-icons">check_circle</span>
            </div>
            <h1 class="thank-you-header">Thank You for Your Order!</h1>
            <p class="thank-you-subtitle">Your order has been successfully placed</p>
        </div>

        <div class="content">
            <div class="order-info-grid">
                <div class="info-card">
                    <div class="info-label">Order ID</div>
                    <div class="info-value">#<?php echo htmlspecialchars($order_id); ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">Customer Name</div>
                    <div class="info-value"><?php echo htmlspecialchars($customer_name); ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">Order Date</div>
                    <div class="info-value"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></div>
                </div>
            </div>
            
            <?php if ($order): 
                $order_details = json_decode($order['order_details'], true);
                $customer_details = json_decode($order['customer_details'], true);
            ?>
            
            <div class="order-items">
                <h2 class="section-title">Order Items</h2>
                <?php if (isset($order_details['items'])): ?>
                    <?php foreach ($order_details['items'] as $item): ?>
                    <div class="item-row">
                        <span class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></span>
                        <span class="item-qty">× <?php echo $item['quantity']; ?></span>
                        <span class="item-price">₱<?php echo number_format($item['subtotal'], 2); ?></span>
                    </div>
                    <?php endforeach; ?>
                    <div class="total-row">
                        <span>Total Amount:</span>
                        <span>₱<?php echo number_format($order['total_price'], 2); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="delivery-info">
                <h2 class="section-title">Delivery Address</h2>
                <p><strong><?php echo htmlspecialchars($customer_details['first_name'] . ' ' . $customer_details['last_name']); ?></strong></p>
                <p><?php echo htmlspecialchars($customer_details['address'] ?? ''); ?></p>
                <?php if (!empty($customer_details['apartment'])): ?>
                    <p><?php echo htmlspecialchars($customer_details['apartment']); ?></p>
                <?php endif; ?>
                <p><?php echo htmlspecialchars($customer_details['city'] . ', ' . $customer_details['region'] . ' ' . $customer_details['postal_code']); ?></p>
                <p><?php echo htmlspecialchars($customer_details['country']); ?></p>
                <p style="margin-top: 15px;"><strong>Phone:</strong> <?php echo htmlspecialchars($customer_details['phone']); ?></p>
            </div>
            
            <?php endif; ?>
            
            <div class="confirmation-message">
                <span class="material-icons" style="vertical-align: middle; margin-right: 10px; color: #0062F6;">mail_outline</span>
                A confirmation email will be sent to your registered email address shortly. You can track your order status from your account dashboard.
            </div>
            
            <div class="button-group">
                <a href="index.php" class="continue-btn">Continue Shopping</a>
                <a href="user/profile.php" class="track-btn">Track Order</a>
            </div>
        </div>
    </div>
    
    <script src="js/search.js"></script>
    <script>
        // Clear cart from localStorage when order is completed
        localStorage.removeItem('cart');
        localStorage.removeItem('cartCount');
    </script>
</body>
</html>

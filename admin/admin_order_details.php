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

// Check if order ID is provided
if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = intval($_GET['id']);

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch order data
$result = $conn->query("SELECT * FROM orders WHERE id = $order_id");

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Order not found!";
    header("Location: orders.php");
    $conn->close();
    exit();
}

$order = $result->fetch_assoc();
$customer = json_decode($order['customer_details'], true);
$order_details = json_decode($order['order_details'], true);

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Admin</title>
    <link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
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
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
        }
        
        .order-header {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .info-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            border-left: 4px solid #2c3e50;
        }
        
        .info-section h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .info-row {
            margin: 8px 0;
            font-size: 14px;
            color: #555;
        }
        
        .info-row strong {
            color: #2c3e50;
            display: inline-block;
            width: 120px;
        }
        
        .order-items {
            margin-top: 30px;
        }
        
        .order-items h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        table th {
            background: #2c3e50;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        table tr:last-child td {
            border-bottom: none;
        }
        
        .total-row {
            background: #e8eef5;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            justify-content: center;
        }
        
        button {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-back {
            background: #95a5a6;
            color: white;
        }
        
        .btn-back:hover {
            background: #7f8c8d;
        }
        
        .btn-print {
            background: #2c3e50;
            color: white;
        }
        
        .btn-print:hover {
            background: #1a252f;
        }
        
        .btn-ship {
            background: #3498db;
            color: white;
        }
        
        .btn-ship:hover {
            background: #2980b9;
        }
        
        .btn-process {
            background: #f39c12;
            color: white;
        }
        
        .btn-process:hover {
            background: #d68910;
        }
        
        .btn-complete {
            background: #27ae60;
            color: white;
        }
        
        .btn-complete:hover {
            background: #229954;
        }
        
        @media (max-width: 768px) {
            .order-header {
                grid-template-columns: 1fr;
            }
            
            .container {
                margin: 10px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order #<?php echo htmlspecialchars($order['id']); ?> Details</h1>
        
        <div class="order-header">
            <div class="info-section">
                <h3>Order Information</h3>
                <div class="info-row"><strong>Order ID:</strong> #<?php echo htmlspecialchars($order['id']); ?></div>
                <div class="info-row"><strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></div>
                <div class="info-row"><strong>Total Amount:</strong> <span style="color: #2c3e50; font-weight: bold;">₱<?php echo number_format($order['total_price'], 2); ?></span></div>
                <div class="info-row"><strong>Status:</strong> 
                    <?php
                        $status = $order['status'] ?? 'Pending';
                        $status_style = '';
                        
                        if (strtolower($status) === 'completed') {
                            $status_style = 'background: #d4edda; color: #155724;';
                        } elseif (strtolower($status) === 'shipped') {
                            $status_style = 'background: #d1ecf1; color: #0c5460;';
                        } elseif (strtolower($status) === 'processing' || strtolower($status) === 'pending') {
                            $status_style = 'background: #fff3cd; color: #856404;';
                        } elseif (strtolower($status) === 'cancelled') {
                            $status_style = 'background: #f8d7da; color: #721c24;';
                        }
                    ?>
                    <span style="<?php echo $status_style; ?> padding: 3px 8px; border-radius: 3px;"><?php echo ucfirst($status); ?></span>
                </div>
            </div>
            
            <div class="info-section">
                <h3>Customer Information</h3>
                <div class="info-row"><strong>Name:</strong> <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></div>
                <div class="info-row"><strong>Email:</strong> <?php echo htmlspecialchars($customer['email'] ?? 'N/A'); ?></div>
                <div class="info-row"><strong>Phone:</strong> <?php echo htmlspecialchars($customer['phone']); ?></div>
                <div class="info-row"><strong>Address:</strong> <?php echo htmlspecialchars($customer['address']); ?></div>
                <div class="info-row"><strong>City:</strong> <?php echo htmlspecialchars($customer['city']); ?></div>
                <div class="info-row"><strong>Postal Code:</strong> <?php echo htmlspecialchars($customer['postal_code']); ?></div>
            </div>
        </div>
        
        <div class="order-items">
            <h2>Order Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($order_details['items'])): ?>
                        <?php foreach ($order_details['items'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td>₱<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo intval($item['quantity']); ?></td>
                            <td>₱<?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;">Total:</td>
                        <td>₱<?php echo number_format($order['total_price'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="button-group">
            <button class="btn-print" onclick="window.print()">Print Order</button>
            <button class="btn-ship" onclick="updateOrderStatus('shipped')">Ship Order</button>
            <button class="btn-complete" onclick="updateOrderStatus('completed')">Mark Completed</button>
            <button class="btn-process" onclick="updateOrderStatus('processing')">Mark Processing</button>
            <button class="btn-back" onclick="window.location.href='admin_orders.php'">Back to Orders</button>
        </div>
    </div>
    
    <script>
        function updateOrderStatus(status) {
            if (confirm('Are you sure you want to mark this order as ' + status + '?')) {
                const formData = new FormData();
                formData.append('order_id', <?php echo $order_id; ?>);
                formData.append('status', status);
                
                fetch('update_order_status.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order status updated to: ' + status);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred');
                });
            }
        }
    </script>
</body>
</html>

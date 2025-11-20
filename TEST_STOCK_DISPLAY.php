<!DOCTYPE html>
<html>
<head>
    <title>Test Stock Display - Smart Solutions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #0062F6; }
        .button {
            background-color: #0062F6;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin: 5px;
        }
        .button:hover {
            background-color: #0050cc;
        }
        .info {
            background-color: #e3f2fd;
            padding: 15px;
            border-left: 4px solid #0062F6;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #e8f5e9;
            padding: 15px;
            border-left: 4px solid #4caf50;
            margin: 10px 0;
            border-radius: 4px;
            color: #2e7d32;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Stock Display System - Setup & Test</h1>
        
        <div class="info">
            <strong>ℹ️ Important:</strong> Run the initialization script below to populate stock data for all products.
        </div>

        <h2>Step 1: Initialize Stock Data</h2>
        <p>Click the button below to initialize stock for all products in the database:</p>
        <a href="init_stock.php" target="_blank">
            <button class="button">Initialize Stock Data</button>
        </a>

        <h2>Step 2: Test the Stock Display</h2>
        <p>After initializing stock, visit any product page to test:</p>
        <ul>
            <li><a href="products/processor.php">Processor Page</a></li>
            <li><a href="products/motherboard.php">Motherboard Page</a></li>
            <li><a href="products/graphicscard.php">Graphics Card Page</a></li>
            <li><a href="products/headset.php">Headset Page</a></li>
            <li><a href="products/keyboard.php">Keyboard Page</a></li>
        </ul>

        <h2>Step 3: How to Test</h2>
        <ol>
            <li>Go to any product page (e.g., Processor)</li>
            <li>Click the "Add to Cart" button on any product</li>
            <li>A modal should appear showing:
                <ul>
                    <li>Product name</li>
                    <li><strong>📦 Stock Available: [number] units</strong> ← This is the stock display</li>
                    <li>Quantity input field</li>
                    <li>Cancel and Add to Cart buttons</li>
                </ul>
            </li>
            <li>Try entering a quantity higher than the stock - you should see an alert</li>
            <li>If stock is 0, the "Add to Cart" button should be disabled</li>
        </ol>

        <h2>What Should Appear in the Modal:</h2>
        <div class="success">
            <strong>Before (what you saw):</strong><br>
            - Quantity input showing "14"<br>
            - Cancel and Add to Cart buttons<br>
            <br>
            <strong>After (what should appear now):</strong><br>
            - Product name<br>
            - 📦 Stock Available: <span style="color: #0062F6; font-size: 16px;"><strong>15</strong></span> units ← NEW!<br>
            - Quantity input<br>
            - Cancel and Add to Cart buttons<br>
        </div>

        <h2>Troubleshooting:</h2>
        <ul>
            <li><strong>Stock not showing?</strong> 
                <ul>
                    <li>Make sure you ran the init_stock.php first</li>
                    <li>Check browser console (F12) for errors</li>
                    <li>Clear browser cache (Ctrl+Shift+Delete)</li>
                    <li>Hard refresh the page (Ctrl+Shift+R)</li>
                </ul>
            </li>
            <li><strong>Modal not appearing?</strong> 
                <ul>
                    <li>Check that ajax-cart.js is loaded</li>
                    <li>Open browser console and check for JavaScript errors</li>
                </ul>
            </li>
            <li><strong>Stock showing 0?</strong> 
                <ul>
                    <li>Edit product stock in admin_products.php</li>
                    <li>Or manually update database stock values</li>
                </ul>
            </li>
        </ul>

        <h2>Browser Console Check:</h2>
        <p>To see debug info, open browser Developer Tools (F12) and check:</p>
        <ul>
            <li>Console tab - should show "Stock data received: {stock: 15}" or similar</li>
            <li>Network tab - verify get_product_stock.php is being called</li>
            <li>Check for any red error messages</li>
        </ul>

        <h2>Files Updated:</h2>
        <ul>
            <li>✓ ajax-cart.js - Updated with stock display</li>
            <li>✓ js/ajax-cart.js - Updated with stock display</li>
            <li>✓ get_product_stock.php - Fetches stock from database</li>
            <li>✓ init_stock.php - Initializes stock data (run this first!)</li>
        </ul>
    </div>

    <script>
        console.log('Test page loaded. Open browser console to see debug info.');
        console.log('When you click Add to Cart, check console for: "Stock data received: ..."');
    </script>
</body>
</html>

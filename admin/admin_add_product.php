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

$error = '';
$success = '';

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']);
    $image = 'image/default-product.png'; // Default image
    
    // Validate required fields
    if (empty($name) || empty($price)) {
        $error = "Product name and price are required!";
    } else {
        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "../image/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $file_name = basename($_FILES['image']['name']);
            $target_file = $target_dir . time() . '_' . $file_name; // Add timestamp to avoid conflicts
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            
            // Validate file type
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($file_type, $allowed_types)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image = $target_file;
                }
            } else {
                $error = "Invalid file type. Allowed: JPG, JPEG, PNG, GIF";
            }
        }
        
        // Insert new product if no errors
        if (empty($error)) {
            $insert_query = "INSERT INTO products (name, price, stock, category, description, image) 
                            VALUES ('$name', $price, $stock, '$category', '$description', '$image')";
            
            if ($conn->query($insert_query) === TRUE) {
                $success = "Product added successfully!";
                $_SESSION['success'] = $success;
                header("Location: products.php");
                $conn->close();
                exit();
            } else {
                $error = "Error adding product: " . $conn->error;
            }
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
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
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
        }
        
        .image-preview {
            margin-bottom: 20px;
            text-align: center;
            display: none;
        }
        
        .image-preview.show {
            display: block;
        }
        
        .image-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .image-preview p {
            color: #666;
            font-size: 12px;
            margin-top: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
        }
        
        input[type="text"],
        input[type="number"],
        input[type="file"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 14px;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            justify-content: center;
        }
        
        button {
            padding: 12px 40px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-save {
            background: #2c3e50;
            color: white;
            min-width: 150px;
        }
        
        .btn-save:hover {
            background: #1a252f;
        }
        
        .btn-cancel {
            background: #95a5a6;
            color: white;
            min-width: 150px;
        }
        
        .btn-cancel:hover {
            background: #7f8c8d;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #f44336;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Product</h1>
        
        <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" id="addProductForm">
            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" accept="../image/*" onchange="previewImage(event)">
                <p style="color: #999; font-size: 12px; margin-top: 5px;">Accepted: JPG, JPEG, PNG, GIF</p>
            </div>
            
            <div id="imagePreview" class="image-preview">
                <p><strong>Preview:</strong></p>
                <img id="previewImg" src="" alt="Preview">
            </div>
            
            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" id="name" name="name" placeholder="Enter product name" required>
            </div>
            
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" id="category" name="category" placeholder="e.g., Laptop, Desktop, Memory">
            </div>
            
            <div class="form-group">
                <label for="price">Price (₱) *</label>
                <input type="number" id="price" name="price" step="0.01" placeholder="0.00" required>
            </div>
            
            <div class="form-group">
                <label for="stock">Stock Quantity</label>
                <input type="number" id="stock" name="stock" value="0" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Enter product description"></textarea>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn-save">Add Product</button>
                <button type="button" class="btn-cancel" onclick="window.location.href='admin_products.php'">Cancel</button>
            </div>
        </form>
    </div>
    
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.add('show');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.remove('show');
            }
        }
    </script>
</body>
</html>

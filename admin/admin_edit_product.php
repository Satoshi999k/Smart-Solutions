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
    header("Location: admin_products.php");
    $conn->close();
    exit();
}

// Fetch product data
$product = $check_result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']);
    $image = $product['image']; // Default to existing image
    $upload_error = false;
    
    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../image/";
        $file_name = basename($_FILES['image']['name']);
        
        // Add timestamp to make filename unique
        $unique_filename = time() . '_' . $file_name;
        $target_file = $target_dir . $unique_filename;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check for upload errors
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $upload_errors = array(
                UPLOAD_ERR_INI_SIZE => "File exceeds upload_max_filesize",
                UPLOAD_ERR_FORM_SIZE => "File exceeds form MAX_FILE_SIZE",
                UPLOAD_ERR_PARTIAL => "File was only partially uploaded",
                UPLOAD_ERR_NO_FILE => "No file was uploaded",
                UPLOAD_ERR_NO_TMP_DIR => "Missing temporary folder",
                UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
                UPLOAD_ERR_EXTENSION => "File upload stopped by extension"
            );
            $error_msg = isset($upload_errors[$_FILES['image']['error']]) 
                ? $upload_errors[$_FILES['image']['error']] 
                : "Unknown upload error";
            $_SESSION['error'] = "Upload failed: " . $error_msg;
            $upload_error = true;
        }
        // Validate file type
        else if (!in_array($file_type, array('jpg', 'jpeg', 'png', 'gif'))) {
            $_SESSION['error'] = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
            $upload_error = true;
        }
        // Check file size (max 5MB)
        else if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = "File is too large. Maximum size is 5MB.";
            $upload_error = true;
        }
        else {
            // Check if directory exists, create if not
            if (!is_dir($target_dir)) {
                if (!mkdir($target_dir, 0755, true)) {
                    $_SESSION['error'] = "Failed to create image directory.";
                    $upload_error = true;
                }
            }
            
            if (!$upload_error && is_writable($target_dir)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    // Store relative path for database
                    $image = 'image/' . $unique_filename;
                } else {
                    $_SESSION['error'] = "Failed to move uploaded file. Permissions may be restricted.";
                    $upload_error = true;
                }
            } else if (!$upload_error) {
                $_SESSION['error'] = "Image directory is not writable. Check folder permissions.";
                $upload_error = true;
            }
        }
    }
    
    // Only update database if no upload errors
    if (!$upload_error) {
        $update_query = "UPDATE products SET 
                        name = '$name', 
                        price = $price, 
                        stock = $stock, 
                        category = '$category', 
                        description = '$description',
                        image = '$image'
                        WHERE id = $product_id";
        
        if ($conn->query($update_query) === TRUE) {
            $_SESSION['success'] = "Product updated successfully!";
            header("Location: admin_products.php");
            $conn->close();
            exit();
        } else {
            $error = "Error updating product: " . $conn->error;
        }
    }
}

// Connection already made above, no need to close and reopen
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin</title>
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
            border-left: 4px solid #4caf50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Product</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
        <div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
        <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <?php if (!empty($product['image'])): ?>
            <div class="image-preview">
                <p><strong>Current Image:</strong></p>
                <?php 
                    $image_path = $product['image'];
                    // If image path doesn't start with '/' or 'http', prepend '../'
                    if (!preg_match('/^(\/|http)/', $image_path)) {
                        $image_path = '../' . $image_path;
                    }
                ?>
                <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="image">Upload New Image</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/jpg">
                <p style="color: #999; font-size: 12px; margin-top: 5px;">Accepted: JPG, JPEG, PNG, GIF (Max 5MB)</p>
            </div>
            
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($product['category'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="price">Price (₱)</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="stock">Stock Quantity</label>
                <input type="number" id="stock" name="stock" value="<?php echo $product['stock'] ?? 0; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn-save">Save Changes</button>
                <button type="button" class="btn-cancel" onclick="window.location.href='admin_products.php'">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>

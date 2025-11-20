<?php
// Database connection setup
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your MySQL password if set
$dbname = "smartsolutions";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt password
    $first_name = $conn->real_escape_string($_POST['first-name']);
    $last_name = $conn->real_escape_string($_POST['last-name']);
    $address = $conn->real_escape_string($_POST['address']);
    $phone_number = $conn->real_escape_string($_POST['phone-number']);
    $postal_code = $conn->real_escape_string($_POST['postal-code']);

    // Handle file upload
    $profile_picture = "";
    if (isset($_FILES['profile-picture']) && $_FILES['profile-picture']['error'] == 0) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $profile_picture = $upload_dir . basename($_FILES['profile-picture']['name']);
        move_uploaded_file($_FILES['profile-picture']['tmp_name'], $profile_picture);
    }

    // Insert data into the database
    $sql = "INSERT INTO users (email, password, first_name, last_name, address, phone_number, postal_code, profile_picture) 
            VALUES ('$email', '$password', '$first_name', '$last_name', '$address', '$phone_number', '$postal_code', '$profile_picture')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Account created successfully! Please log in.');
                window.location.href = 'login.php';
              </script>";
        exit;
    } else {
        echo "<script>
                alert('Error: " . addslashes($conn->error) . "');
              </script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - SmartSolutions</title>
    <link rel="stylesheet" href="design.css" />
    <link rel="shortcut icon" href="image/smartsolutionslogo.jpg" type="image/x-icon">
</head>
<body>
    <header>
        <!-- Header content -->
    </header>

    <div class="menu">
        <!-- Menu content -->
    </div>

    <div class="breadcrumb">
        <a href="index.html">Home</a> >
        <a>Register</a>
    </div>

    <div class="formreg-container">
        <!-- Register Form -->
        <h2>Create Account</h2>
        <p>Sign up to access your account</p>
        <form action="register.php" method="POST" enctype="multipart/form-data">
            <label for="new-email">Email Address *</label>
            <input type="email" id="new-email" name="email" placeholder="Enter your email" required>
            
            <label for="new-password">Password *</label>
            <input type="password" id="new-password" name="password" placeholder="Enter your password" required>
            
            <label for="confirm-password">Re-enter Password *</label>
            <input type="password" id="confirm-password" name="confirm-password" placeholder="Re-enter your password" required>
            
            <label for="profile-picture">Upload Profile Picture *</label>
            <input type="file" id="profile-picture" name="profile-picture" accept="image/*" required>
            
            <label for="first-name">First Name *</label>
            <input type="text" id="first-name" name="first-name" placeholder="Enter your first name" required>
            
            <label for="last-name">Last Name *</label>
            <input type="text" id="last-name" name="last-name" placeholder="Enter your last name" required>
            
            <label for="address">Address *</label>
            <input type="text" id="address" name="address" placeholder="Enter your address" required>
            
            <label for="phone-number">Phone Number *</label>
            <input type="tel" id="phone-number" name="phone-number" placeholder="Enter your phone number" required>
            
            <label for="postal-code">Postal Code *</label>
            <input type="text" id="postal-code" name="postal-code" placeholder="Enter your postal code" required>
            
            <button type="submit" class="register-btn">Register</button>
        </form>
        <div class="additional-links">
            <a href="login.php">Already have an account? Login</a>
        </div>
    </div>

    <footer class="footer">
        <!-- Footer content -->
    </footer>
</body>
</html>

<?php
/**
 * Login Logic Processor
 * This file handles POST requests from register.php login form
 * Validates credentials and manages user session with cart migration
 */

session_start();
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && isset($_POST['password'])) {
    $email_or_username = $_POST['email'];
    $password = $_POST['password'];

    // First, try to find by email as-is
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email_or_username);
    $stmt->execute();
    $result = $stmt->get_result();

    // If not found and input doesn't contain @, try searching as email pattern
    if ($result->num_rows === 0 && strpos($email_or_username, '@') === false) {
        $stmt->close();
        $sql = "SELECT * FROM users WHERE email LIKE ?";
        $stmt = $conn->prepare($sql);
        $search_pattern = $email_or_username . '%';
        $stmt->bind_param("s", $search_pattern);
        $stmt->execute();
        $result = $stmt->get_result();
    }

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            // Adjust path for profile picture - add ../ prefix if it's in uploads folder
            $profile_path = !empty($user['profile_picture']) ? $user['profile_picture'] : 'image/default-profile.png';
            if (strpos($profile_path, 'uploads/') === 0) {
                $_SESSION['profile_picture'] = '../' . $profile_path;
            } else {
                $_SESSION['profile_picture'] = $profile_path;
            }
            $_SESSION['is_admin'] = $user['is_admin'];

            // Migrate guest cart items to shopping_cart table
            if (!empty($_SESSION['cart'])) {
                $guest_cart = $_SESSION['cart'];
                
                foreach ($guest_cart as $item) {
                    if (empty($item['id'])) {
                        continue;
                    }
                    
                    $item_id = intval($item['id']);
                    $item_quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
                    if ($item_quantity < 1) $item_quantity = 1;
                    
                    $insert_query = "INSERT INTO shopping_cart (user_id, product_id, quantity) VALUES (?, ?, ?) 
                                     ON DUPLICATE KEY UPDATE quantity = quantity + ?";
                    $insert_stmt = $conn->prepare($insert_query);
                    if ($insert_stmt) {
                        $insert_stmt->bind_param("iiii", $user['id'], $item_id, $item_quantity, $item_quantity);
                        $insert_stmt->execute();
                        $insert_stmt->close();
                    }
                }
            }

            // Load shopping_cart into session
            $load_query = "SELECT * FROM shopping_cart WHERE user_id = ?";
            $load_stmt = $conn->prepare($load_query);
            $load_stmt->bind_param("i", $user['id']);
            $load_stmt->execute();
            $load_result = $load_stmt->get_result();
            $session_cart = [];
            while ($row = $load_result->fetch_assoc()) {
                $session_cart[] = $row;
            }
            $load_stmt->close();
            $_SESSION['cart'] = $session_cart;

            // Set success alert and redirect
            if ($user['is_admin'] == 1) {
                $_SESSION['alert'] = ['type' => 'success', 'title' => 'Welcome Admin!', 'message' => '<i class="mdi mdi-shield-account" style="font-size: 20px; vertical-align: middle; margin-right: 8px; color: #28a745;"></i>You are logged in as an administrator.'];
                $_SESSION['redirect'] = '../admin/admin_dashboard.php';
            } else {
                $_SESSION['alert'] = ['type' => 'success', 'title' => 'Login Successful!', 'message' => '<i class="mdi mdi-check-circle" style="font-size: 20px; vertical-align: middle; margin-right: 8px; color: #28a745;"></i>Welcome back!'];
                $_SESSION['redirect'] = '../index.php';
            }
            
            header('Location: register.php');
            exit;
        } else {
            $_SESSION['alert'] = ['type' => 'error', 'title' => 'Invalid Password!', 'message' => '<i class="mdi mdi-lock-alert" style="font-size: 20px; vertical-align: middle; margin-right: 8px; color: #dc3545;"></i>The password you entered is incorrect. Please try again.'];
            header('Location: register.php');
            exit;
        }
    } else {
        $_SESSION['alert'] = ['type' => 'error', 'title' => 'Account Not Found!', 'message' => '<i class="mdi mdi-email-off" style="font-size: 20px; vertical-align: middle; margin-right: 8px; color: #dc3545;"></i>No account found with this email. Please check and try again.'];
        header('Location: register.php');
        exit;
    }
}
$conn->close();
?>


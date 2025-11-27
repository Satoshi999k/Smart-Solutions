<?php
session_start();
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Set session variables using the correct column name
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['profile_picture'] = !empty($user['profile_picture']) ? $user['profile_picture'] : 'image/default-profile.png';

            // Store success alert in session
            $_SESSION['alert'] = [
                'title' => 'Login Successful!',
                'message' => '<i class="mdi mdi-check-circle" style="font-size: 20px; vertical-align: middle; margin-right: 8px; color: #28a745;"></i>Welcome back, ' . htmlspecialchars($user['first_name']) . '!',
                'type' => 'success'
            ];
            $_SESSION['redirect'] = 'index.php';
            
            header("Location: user/register.php");
            exit();
        } else {
            // Store error alert in session
            $_SESSION['alert'] = [
                'title' => 'Invalid Password!',
                'message' => '<i class="mdi mdi-lock-alert" style="font-size: 20px; vertical-align: middle; margin-right: 8px; color: #dc3545;"></i>The password you entered is incorrect. Please try again.',
                'type' => 'error'
            ];
            
            header("Location: user/register.php");
            exit();
        }
    } else {
        // Store error alert in session
        $_SESSION['alert'] = [
            'title' => 'Account Not Found!',
            'message' => '<i class="mdi mdi-email-off" style="font-size: 20px; vertical-align: middle; margin-right: 8px; color: #dc3545;"></i>No account found with this email. Please check and try again.',
            'type' => 'error'
        ];
        
        header("Location: user/register.php");
        exit();
    }
}
$conn->close();
?>


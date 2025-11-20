<?php
session_start();
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_or_username = $_POST['email'];
    $password = $_POST['password'];

    // First, try to find by email as-is
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email_or_username);
    $stmt->execute();
    $result = $stmt->get_result();

    // If not found and input doesn't contain @, try adding @ domain or search as email pattern
    if ($result->num_rows === 0 && strpos($email_or_username, '@') === false) {
        // Try to find account with this as part of email (e.g., "admin" finds "admin@smartsolutions.com")
        // Or just accept it as a username stored in email field
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
            // Set session variables using the correct column name
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['profile_picture'] = !empty($user['profile_picture']) ? $user['profile_picture'] : '../image/default-profile.png';
            $_SESSION['is_admin'] = $user['is_admin'];

            // Auto-detect: Check if user is admin and redirect accordingly
            if ($user['is_admin'] == 1) {
                echo "<script>alert('Welcome Admin!'); window.location.href='../admin/admin_dashboard.php';</script>";
            } else {
                echo "<script>alert('Login Successful!'); window.location.href='../index.php';</script>";
            }
        } else {
            echo "<script>alert('Invalid password!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('No account found with this email or username!'); window.history.back();</script>";
    }

    $stmt->close();
}
$conn->close();
?>


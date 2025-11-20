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

            echo "<script>alert('Login Successful!'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Invalid password!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('No account found with this email!'); window.history.back();</script>";
    }

    $stmt->close();
}
$conn->close();
?>


<?php
session_start();

// Include OAuth config
require_once '../includes/oauth_config.php';

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Determine provider from state parameter
$provider = $_GET['state'] ?? null;

if (!$provider) {
    die("Error: Missing OAuth provider state");
}

if ($provider === 'google') {
    // Handle Google OAuth callback
    $code = $_GET['code'] ?? null;

    if (!$code) {
        header("Location: register.php?error=Missing authorization code");
        exit;
    }

    // Exchange authorization code for access token
    $token_url = 'https://oauth2.googleapis.com/token';
    $post_data = [
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'code' => $code,
        'grant_type' => 'authorization_code',
        'redirect_uri' => GOOGLE_REDIRECT_URI
    ];

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        header("Location: register.php?error=cURL error: " . urlencode($curl_error));
        exit;
    }

    $token_data = json_decode($response, true);

    if (isset($token_data['error'])) {
        header("Location: register.php?error=" . urlencode($token_data['error_description'] ?? $token_data['error']));
        exit;
    }

    if (!isset($token_data['access_token'])) {
        header("Location: register.php?error=No access token returned");
        exit;
    }

    // Get user info from Google
    $access_token = $token_data['access_token'];
    $userinfo_url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . urlencode($access_token);

    $ch = curl_init($userinfo_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $userinfo_response = curl_exec($ch);
    curl_close($ch);

    $userinfo = json_decode($userinfo_response, true);

    if (!isset($userinfo['email'])) {
        header("Location: register.php?error=Could not get email from Google");
        exit;
    }

    $email = $userinfo['email'];
    $full_name = $userinfo['name'] ?? 'User';
    $picture = $userinfo['picture'] ?? null;
    
    // Split full name into first and last name
    $name_parts = explode(' ', trim($full_name), 2);
    $first_name = $name_parts[0];
    $last_name = isset($name_parts[1]) ? $name_parts[1] : '';

    // Check if user exists
    $query = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists, log them in
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $email;
        $stmt->close();
        $conn->close();
        header("Location: ../index.php");
        exit;
    }

    // Create new user
    $query = "INSERT INTO users (email, password, first_name, last_name, profile_picture) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        header("Location: register.php?error=Database error: " . urlencode($conn->error));
        exit;
    }

    $hashed_password = password_hash(uniqid(), PASSWORD_BCRYPT);
    $stmt->bind_param("sssss", $email, $hashed_password, $first_name, $last_name, $picture);
    
    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $email;
        $stmt->close();
        $conn->close();
        header("Location: ../index.php");
        exit;
    } else {
        header("Location: register.php?error=Failed to create account: " . urlencode($stmt->error));
        exit;
    }
} else {
    header("Location: register.php?error=Invalid OAuth provider");
    exit;
}

$conn->close();
?>
?>

    // Handle Facebook OAuth callback
    $code = $_GET['code'] ?? null;

    if (!$code) {
        header("Location: register.php?error=Missing authorization code");
        exit;
    }

    // Exchange authorization code for access token
    $fb_app_id = FACEBOOK_APP_ID;
    $fb_app_secret = FACEBOOK_APP_SECRET;
    $fb_redirect = FACEBOOK_REDIRECT_URI;

    $token_url = 'https://graph.facebook.com/v18.0/oauth/access_token';
    $post_data = [
        'client_id' => $fb_app_id,
        'client_secret' => $fb_app_secret,
        'code' => $code,
        'redirect_uri' => $fb_redirect
    ];

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    $response = curl_exec($ch);
    curl_close($ch);

    $token_data = json_decode($response, true);

    if (isset($token_data['access_token'])) {
        // Get user info from Facebook
        $access_token = $token_data['access_token'];
        $userinfo_url = 'https://graph.facebook.com/v18.0/me?fields=id,name,email,picture&access_token=' . $access_token;

        $ch = curl_init($userinfo_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userinfo_response = curl_exec($ch);
        curl_close($ch);

        $userinfo = json_decode($userinfo_response, true);

        if (isset($userinfo['email'])) {
            $email = $userinfo['email'];
            $name = $userinfo['name'] ?? 'User';
            $picture = $userinfo['picture']['data']['url'] ?? null;

            // Check if user exists
            $query = "SELECT id FROM users WHERE email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // User exists, log them in
                $user = $result->fetch_assoc();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $email;
                header("Location: ../index.php");
                exit;
            } else {
                // Create new user
                $query = "INSERT INTO users (email, password, full_name, profile_picture, created_at) VALUES (?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($query);
                $hashed_password = password_hash(uniqid(), PASSWORD_BCRYPT);
                $stmt->bind_param("sss", $email, $hashed_password, $name);
                
                if ($stmt->execute()) {
                    $user_id = $conn->insert_id;
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['email'] = $email;
                    
                    // Download and save profile picture if available
                    if ($picture) {
                        $upload_dir = '../image/profile_pictures/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        $filename = 'profile_' . $user_id . '_' . time() . '.jpg';
                        $filepath = $upload_dir . $filename;
                        
                        $ch = curl_init($picture);
                        $fp = fopen($filepath, 'w');
                        curl_setopt($ch, CURLOPT_FILE, $fp);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_exec($ch);
                        curl_close($ch);
                        fclose($fp);
                        
                        // Update user profile picture in database
                        $pic_path = 'image/profile_pictures/' . $filename;
                        $update_query = "UPDATE users SET profile_picture = ? WHERE id = ?";
                        $update_stmt = $conn->prepare($update_query);
                        $update_stmt->bind_param("si", $pic_path, $user_id);
                        $update_stmt->execute();
                    }
                    
                    header("Location: ../index.php");
                    exit;
                } else {
                    header("Location: register.php?error=Failed to create account");
                    exit;
                }
            }
            $stmt->close();
        } else {
            header("Location: register.php?error=Failed to get user info from Facebook");
            exit;
        }
    } else {
        header("Location: register.php?error=Failed to get access token from Facebook");
        exit;
    }
} else {
    header("Location: register.php?error=Invalid OAuth provider");
    exit;
}

$conn->close();
?>

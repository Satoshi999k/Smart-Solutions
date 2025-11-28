<?php
session_start();

// Check if admin is logged in (new system uses is_admin flag)
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    // Also check for old admin session for backwards compatibility
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: ../user/register.php");
        exit();
    }
}

$conn = new mysqli("localhost", "root", "", "smartsolutions");

// Get all users
$users_result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - Admin</title>
    <link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
        
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid #34495e;
            text-align: center;
        }
        
        .sidebar-header img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        
        .sidebar-header h2 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .sidebar-header p {
            font-size: 12px;
            color: #bdc3c7;
        }
        
        .sidebar-menu {
            margin-top: 20px;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #34495e;
        }
        
        .sidebar-menu a i {
            margin-right: 10px;
            width: 20px;
            display: inline-block;
            font-size: 20px;
            vertical-align: middle;
        }
        
        .material-icons {
            font-family: 'Material Icons';
            font-weight: normal;
            font-style: normal;
            font-size: 20px;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
        }
        
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 20px;
        }
        
        .top-bar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .top-bar h1 {
            color: #2c3e50;
            font-size: 24px;
        }
        
        .logout-btn {
            padding: 10px 20px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .content-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e0e0e0;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
        }
        
        table tr:hover {
            background: #f8f9fa;
        }
        
        .user-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .action-btn {
            padding: 8px 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            margin-right: 5px;
            transition: all 0.3s ease;
            display: inline-block;
            min-width: 70px;
            text-align: center;
        }
        
        .action-btn.view {
            background: #2196f3;
            color: white;
        }
        
        .action-btn.view:hover {
            background: #1976d2;
            box-shadow: 0 2px 8px rgba(33, 150, 243, 0.3);
        }
        
        .action-btn.delete {
            background: #f44336;
            color: white;
        }
        
        .action-btn.delete:hover {
            background: #da190b;
            box-shadow: 0 2px 8px rgba(244, 67, 54, 0.3);
        }
        
        .actions-cell {
            white-space: nowrap;
            width: 180px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="../image/smartsolutionslogo.jpg" alt="Admin" onerror="this.src='../image/login-icon.png'">
                <h2>Admin Panel</h2>
                <p><?php echo htmlspecialchars($_SESSION['first_name']); ?></p>
            </div>
            
            <div class="sidebar-menu">
                <a href="/ITP122/admin/admin_dashboard.php"><i class="material-icons">dashboard</i> Dashboard</a>
                <a href="/ITP122/admin/admin_orders.php"><i class="material-icons">shopping_cart</i> Orders</a>
                <a href="/ITP122/admin/admin_users.php" class="active"><i class="material-icons">people</i> Users</a>
                <a href="/ITP122/admin/admin_products.php"><i class="material-icons">inventory_2</i> Products</a>
                <a href="/ITP122/admin/admin_reports.php"><i class="material-icons">trending_up</i> Reports</a>
                <a href="/ITP122/admin/admin_settings.php"><i class="material-icons">settings</i> Settings</a>
            </div>
        </div>
        
        <div class="main-content">
            <div class="top-bar">
                <h1>Users Management</h1>
                <a href="/ITP122/admin/admin_logout.php" class="logout-btn">Logout</a>
            </div>
            
            <div class="content-section">
                <h2>All Users (<?php echo $users_result->num_rows; ?>)</h2>
                <?php if ($users_result && $users_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <?php 
                                $profile_pic = "../image/login-icon.png";
                                if (!empty($user['profile_picture'])) {
                                    $profile_pic = '/ITP122/' . $user['profile_picture'];
                                }
                                ?>
                                <img src="<?php echo $profile_pic; ?>" alt="Profile" class="user-img" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            </td>
                            <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone_number'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars(substr($user['address'] ?? 'N/A', 0, 30)); ?></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td class="actions-cell">
                                <button class="action-btn view" onclick="viewUser(<?php echo $user['id']; ?>)">View</button>
                                <button class="action-btn delete" onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="no-data">No users found</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        function viewUser(id) {
            window.location.href = 'admin_view_user.php?id=' + id;
        }
        
        function deleteUser(id) {
            window.location.href = 'admin_delete_user.php?id=' + id;
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>

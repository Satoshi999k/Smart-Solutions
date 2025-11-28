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
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin</title>
    <link rel="shortcut icon" href="../image/smartsolutionslogo.jpg" type="../image/x-icon">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
        
        .settings-grid {
            display: grid;
            gap: 20px;
        }
        
        .settings-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .settings-card h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .settings-card h3 .material-icons {
            color: #2196f3;
            font-size: 24px;
        }
        
        .settings-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .settings-item:last-child {
            border-bottom: none;
        }
        
        .settings-label {
            color: #555;
        }
        
        .settings-value {
            color: #2c3e50;
            font-weight: 500;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-primary {
            background: #2196f3;
            color: white;
        }
        
        .btn-danger {
            background: #f44336;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
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
                <a href="/ITP122/admin/admin_users.php"><i class="material-icons">people</i> Users</a>
                <a href="/ITP122/admin/admin_products.php"><i class="material-icons">inventory_2</i> Products</a>
                <a href="/ITP122/admin/admin_reports.php"><i class="material-icons">trending_up</i> Reports</a>
                <a href="/ITP122/admin/admin_settings.php" class="active"><i class="material-icons">settings</i> Settings</a>
            </div>
        </div>
        
        <div class="main-content">
            <div class="top-bar">
                <h1>Settings</h1>
                <a href="/ITP122/admin/admin_logout.php" class="logout-btn">Logout</a>
            </div>
            
            <div class="settings-grid">
                <div class="settings-card">
                    <h3><i class="material-icons">lock</i> Account Settings</h3>
                    <div class="settings-item">
                        <span class="settings-label">Admin Username</span>
                        <span class="settings-value"><?php echo htmlspecialchars($_SESSION['first_name']); ?></span>
                    </div>
                    <div class="settings-item">
                        <span class="settings-label">Password</span>
                        <button class="btn btn-primary" onclick="changePassword()">Change Password</button>
                    </div>
                </div>
                
                <div class="settings-card">
                    <h3><i class="material-icons">store</i> Store Settings</h3>
                    <div class="settings-item">
                        <span class="settings-label">Store Name</span>
                        <span class="settings-value">Smart Solutions</span>
                    </div>
                    <div class="settings-item">
                        <span class="settings-label">Database</span>
                        <span class="settings-value">smartsolutions</span>
                    </div>
                    <div class="settings-item">
                        <span class="settings-label">Default Currency</span>
                        <span class="settings-value">PHP (₱)</span>
                    </div>
                </div>
                
                <div class="settings-card">
                    <h3><i class="material-icons">notifications_active</i> Notifications</h3>
                    <div class="settings-item">
                        <span class="settings-label">Email Notifications</span>
                        <button class="btn btn-primary">Configure</button>
                    </div>
                    <div class="settings-item">
                        <span class="settings-label">Order Alerts</span>
                        <button class="btn btn-primary">Configure</button>
                    </div>
                </div>
                
                <div class="settings-card">
                    <h3><i class="material-icons">build</i> System</h3>
                    <div class="settings-item">
                        <span class="settings-label">Clear Cache</span>
                        <button class="btn btn-primary" onclick="clearCache()">Clear Now</button>
                    </div>
                    <div class="settings-item">
                        <span class="settings-label">Backup Database</span>
                        <button class="btn btn-primary" onclick="backupDatabase()">Create Backup</button>
                    </div>
                    <div class="settings-item">
                        <span class="settings-label">System Logs</span>
                        <button class="btn btn-primary" onclick="viewLogs()">View Logs</button>
                    </div>
                </div>
                
                <div class="settings-card">
                    <h3><i class="material-icons">warning</i> Danger Zone</h3>
                    <div class="settings-item">
                        <span class="settings-label">Reset All Settings</span>
                        <button class="btn btn-danger" onclick="resetSettings()">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function changePassword() {
            alert('Password change feature coming soon!');
        }
        
        function clearCache() {
            if (confirm('Are you sure you want to clear the cache?')) {
                alert('Cache cleared successfully!');
            }
        }
        
        function backupDatabase() {
            if (confirm('Create a database backup?')) {
                alert('Backup feature coming soon!');
            }
        }
        
        function viewLogs() {
            alert('System logs viewer coming soon!');
        }
        
        function resetSettings() {
            if (confirm('WARNING: This will reset all settings to default. Continue?')) {
                alert('Reset functionality coming soon!');
            }
        }
    </script>
</body>
</html>

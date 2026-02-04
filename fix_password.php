<?php
/**
 * Password Reset & User Fix Script
 */

require_once 'config/database.php';

echo "<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
h2 { background: #000; color: #fff; padding: 15px; margin: -20px -20px 20px -20px; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.btn { display: inline-block; padding: 10px 20px; background: #000; color: #fff; text-decoration: none; margin: 10px 5px; border: none; cursor: pointer; }
.btn:hover { background: #333; }
pre { background: #fff; padding: 15px; border: 1px solid #ddd; }
</style>";

echo "<h2>🔧 Password Reset & User Fix</h2>";

// Check current users
echo "<h3>Current Users in Database:</h3>";
$users = fetchAll("SELECT id, username, full_name, email FROM users");
if ($users) {
    echo "<table border='1' cellpadding='10' style='width:100%; background:#fff;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Full Name</th><th>Email</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td><strong>" . htmlspecialchars($user['username']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";
} else {
    echo "<p class='error'>❌ No users found!</p>";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'reset_von') {
        // Reset Von's password to 'admin123'
        $new_password = 'admin123';
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        $result = query("UPDATE users SET password = ? WHERE username = ?", [$new_hash, 'Von']);
        
        if ($result) {
            echo "<div style='background:#d4edda; padding:15px; border:1px solid #c3e6cb; margin:20px 0;'>";
            echo "<p class='success'>✅ Password updated successfully for user 'Von'!</p>";
            echo "<p><strong>New Login Credentials:</strong></p>";
            echo "<p>Username: <strong>Von</strong></p>";
            echo "<p>Password: <strong>admin123</strong></p>";
            echo "<p><a href='/amsp/auth/login.php' class='btn'>Go to Login</a></p>";
            echo "</div>";
        }
    }
    
    if ($action === 'create_admin') {
        // Check if admin exists
        $check = fetchOne("SELECT id FROM users WHERE username = ?", ['admin']);
        
        if ($check) {
            // Update existing admin
            $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
            query("UPDATE users SET password = ?, full_name = ?, email = ? WHERE username = ?", 
                  [$new_hash, 'System Administrator', 'admin@example.com', 'admin']);
            echo "<div style='background:#fff3cd; padding:15px; border:1px solid #ffeaa7; margin:20px 0;'>";
            echo "<p class='success'>⚠️ Admin user already existed. Password updated!</p>";
        } else {
            // Create new admin
            $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
            query("INSERT INTO users (username, password, full_name, email) VALUES (?, ?, ?, ?)",
                  ['admin', $password_hash, 'System Administrator', 'admin@example.com']);
            echo "<div style='background:#d4edda; padding:15px; border:1px solid #c3e6cb; margin:20px 0;'>";
            echo "<p class='success'>✅ Admin user created successfully!</p>";
        }
        
        echo "<p><strong>Admin Login Credentials:</strong></p>";
        echo "<p>Username: <strong>admin</strong></p>";
        echo "<p>Password: <strong>admin123</strong></p>";
        echo "<p><a href='/amsp/auth/login.php' class='btn'>Go to Login</a></p>";
        echo "</div>";
    }
    
    if ($action === 'delete_von') {
        // Delete Von user
        query("DELETE FROM users WHERE username = ?", ['Von']);
        echo "<div style='background:#d4edda; padding:15px; border:1px solid #c3e6cb; margin:20px 0;'>";
        echo "<p class='success'>✅ User 'Von' deleted successfully!</p>";
        echo "<p><a href='' class='btn'>Refresh Page</a></p>";
        echo "</div>";
    }
}

// Show action buttons
echo "<hr>";
echo "<h3>Choose an Action:</h3>";

echo "<form method='POST' style='margin:20px 0;'>";
echo "<input type='hidden' name='action' value='reset_von'>";
echo "<button type='submit' class='btn'>🔑 Reset 'Von' Password to 'admin123'</button>";
echo "<p style='color:#666; font-size:14px;'>This will allow you to login with: Von / admin123</p>";
echo "</form>";

echo "<form method='POST' style='margin:20px 0;'>";
echo "<input type='hidden' name='action' value='create_admin'>";
echo "<button type='submit' class='btn'>➕ Create/Fix Admin User</button>";
echo "<p style='color:#666; font-size:14px;'>This will create admin user with: admin / admin123</p>";
echo "</form>";

echo "<form method='POST' style='margin:20px 0;' onsubmit='return confirm(\"Are you sure you want to delete user Von?\");'>";
echo "<input type='hidden' name='action' value='delete_von'>";
echo "<button type='submit' class='btn' style='background:#dc3545;'>🗑️ Delete User 'Von'</button>";
echo "<p style='color:#666; font-size:14px;'>This will permanently delete the Von user account</p>";
echo "</form>";

echo "<hr>";
echo "<p><strong>After fixing:</strong> <a href='/amsp/auth/login.php'>Go to Login Page</a></p>";
echo "<p><small>Delete this fix_password.php file after you're done for security.</small></p>";
?>

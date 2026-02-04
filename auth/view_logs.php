<?php
/**
 * Authentication Log Viewer
 * View recent authentication errors and activities
 */
session_start();
require_once '../includes/auth_logger.php';

// Simple password protection
$view_password = 'admin123';
$authenticated = false;

if (isset($_POST['password']) && $_POST['password'] === $view_password) {
    $_SESSION['log_viewer_auth'] = true;
}

if (isset($_SESSION['log_viewer_auth'])) {
    $authenticated = true;
}

if (isset($_GET['clear']) && $authenticated) {
    AuthLogger::clearLogs();
    header('Location: view_logs.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Logs</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #252526;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }
        h1 {
            color: #4ec9b0;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        .login-form {
            background: #2d2d30;
            padding: 30px;
            border-radius: 8px;
            max-width: 400px;
            margin: 50px auto;
        }
        input[type="password"] {
            width: 100%;
            padding: 12px;
            background: #3c3c3c;
            border: 1px solid #555;
            color: #d4d4d4;
            border-radius: 4px;
            margin: 10px 0;
            font-size: 1rem;
        }
        button {
            background: #0e639c;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            margin: 5px;
        }
        button:hover { background: #1177bb; }
        .danger { background: #c73636; }
        .danger:hover { background: #d63031; }
        .log-container {
            background: #1e1e1e;
            padding: 20px;
            border-radius: 8px;
            max-height: 70vh;
            overflow-y: auto;
            white-space: pre-wrap;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .warning { color: #dcdcaa; }
        .info { color: #569cd6; }
        .timestamp { color: #858585; }
        .controls {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #2d2d30;
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid #0e639c;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #4ec9b0;
        }
        .stat-label {
            color: #858585;
            font-size: 0.85rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php if (!$authenticated): ?>
        <div class="login-form">
            <h1>🔒 Authentication Required</h1>
            <form method="POST">
                <input type="password" name="password" placeholder="Enter password" autofocus required>
                <button type="submit">View Logs</button>
            </form>
            <p style="color: #858585; margin-top: 15px; font-size: 0.85rem;">Default password: admin123</p>
        </div>
    <?php else: ?>
        <div class="container">
            <h1>🔍 Authentication Activity Logs</h1>
            
            <div class="controls">
                <button onclick="location.reload()">🔄 Refresh</button>
                <button onclick="if(confirm('Clear all logs?')) location.href='?clear=1'" class="danger">🗑️ Clear Logs</button>
                <button onclick="window.history.back()">← Back to App</button>
            </div>
            
            <?php
            $logs = AuthLogger::getRecentLogs(100);
            $logLines = explode("\n", $logs);
            
            // Count different types
            $successCount = substr_count($logs, '✅ LOGIN SUCCESS');
            $failureCount = substr_count($logs, '❌ LOGIN FAILED');
            $logoutCount = substr_count($logs, '🚪 LOGOUT');
            $unauthorizedCount = substr_count($logs, '🚫 UNAUTHORIZED');
            ?>
            
            <div class="stats">
                <div class="stat-card" style="border-color: #4ec9b0;">
                    <div class="stat-value"><?php echo $successCount; ?></div>
                    <div class="stat-label">✅ Successful Logins</div>
                </div>
                <div class="stat-card" style="border-color: #f48771;">
                    <div class="stat-value"><?php echo $failureCount; ?></div>
                    <div class="stat-label">❌ Failed Logins</div>
                </div>
                <div class="stat-card" style="border-color: #dcdcaa;">
                    <div class="stat-value"><?php echo $logoutCount; ?></div>
                    <div class="stat-label">🚪 Logouts</div>
                </div>
                <div class="stat-card" style="border-color: #c73636;">
                    <div class="stat-value"><?php echo $unauthorizedCount; ?></div>
                    <div class="stat-label">🚫 Unauthorized Access</div>
                </div>
            </div>
            
            <div class="log-container">
                <?php 
                if (empty(trim($logs))) {
                    echo "📝 No logs recorded yet. Start using the system to see activity here.";
                } else {
                    // Color code the logs
                    $logs = str_replace('✅', '<span class="success">✅</span>', $logs);
                    $logs = str_replace('❌', '<span class="error">❌</span>', $logs);
                    $logs = str_replace('🚪', '<span class="warning">🚪</span>', $logs);
                    $logs = str_replace('🚫', '<span class="error">🚫</span>', $logs);
                    $logs = str_replace('💾', '<span class="error">💾</span>', $logs);
                    $logs = str_replace('↩️', '<span class="info">↩️</span>', $logs);
                    $logs = str_replace('🔍', '<span class="info">🔍</span>', $logs);
                    $logs = preg_replace('/\[([^\]]+)\]/', '<span class="timestamp">[$1]</span>', $logs);
                    
                    echo $logs;
                }
                ?>
            </div>
            
            <div style="margin-top: 15px; color: #858585; font-size: 0.85rem;">
                <strong>Log Location:</strong> /logs/auth_errors.log<br>
                <strong>Last Updated:</strong> <?php echo date('Y-m-d H:i:s'); ?>
            </div>
        </div>
    <?php endif; ?>
    
    <script>
        // Auto-scroll to bottom
        const logContainer = document.querySelector('.log-container');
        if (logContainer) {
            logContainer.scrollTop = logContainer.scrollHeight;
        }
    </script>
</body>
</html>

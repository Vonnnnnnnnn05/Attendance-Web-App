<?php
/**
 * Logout Handler
 * Destroys session and redirects to login
 */
session_start();
require_once '../includes/auth_logger.php';

try {
    // Log the logout
    $username = $_SESSION['username'] ?? 'Unknown';
    AuthLogger::logLogout($username);

    // Unset all session variables
    $_SESSION = [];

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

    // Destroy the session
    session_destroy();

    // Build redirect URL
    $redirectUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/login.php';
    AuthLogger::logRedirect('logout', $redirectUrl, 'Session destroyed');
    
    // Redirect to login page
    header('Location: ' . $redirectUrl);
    exit();
    
} catch (Exception $e) {
    AuthLogger::log('❌ LOGOUT ERROR', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    // Force redirect even on error
    header('Location: login.php');
    exit();
}
?>

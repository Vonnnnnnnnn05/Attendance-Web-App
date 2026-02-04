<?php
/**
 * Authentication Check
 * Include this file on every protected page
 */

require_once __DIR__ . '/auth_logger.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
        // Redirect to login page
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        // Remove common folder names to get to root
        $base = preg_replace('#/(students|subjects|attendance|includes).*$#', '', $base);
        $loginUrl = $protocol . '://' . $host . $base . '/auth/login.php';
        
        AuthLogger::log('🚫 UNAUTHORIZED ACCESS', [
            'attempted_page' => $_SERVER['REQUEST_URI'],
            'redirect_to' => $loginUrl
        ]);
        
        header('Location: ' . $loginUrl);
        exit();
    }
} catch (Exception $e) {
    AuthLogger::log('❌ AUTH CHECK ERROR', [
        'error' => $e->getMessage(),
        'page' => $_SERVER['REQUEST_URI']
    ]);
}

// Optional: Refresh session timeout
$_SESSION['last_activity'] = time();
?>

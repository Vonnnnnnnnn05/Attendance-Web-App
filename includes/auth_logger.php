<?php
/**
 * Error Logger for Authentication
 * Tracks all auth-related errors for debugging
 */

class AuthLogger {
    private static $logFile = __DIR__ . '/../logs/auth_errors.log';
    
    /**
     * Log an error with timestamp and context
     */
    public static function log($message, $context = []) {
        // Create logs directory if it doesn't exist
        $logDir = dirname(self::$logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $url = $_SERVER['REQUEST_URI'] ?? 'Unknown';
        
        $logEntry = sprintf(
            "[%s] IP: %s | URL: %s\n%s\nContext: %s\nUser-Agent: %s\n%s\n\n",
            $timestamp,
            $ip,
            $url,
            $message,
            json_encode($context, JSON_PRETTY_PRINT),
            $userAgent,
            str_repeat('-', 80)
        );
        
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND);
    }
    
    /**
     * Log successful login
     */
    public static function logSuccess($username) {
        self::log("✅ LOGIN SUCCESS", ['username' => $username]);
    }
    
    /**
     * Log failed login
     */
    public static function logFailure($username, $reason) {
        self::log("❌ LOGIN FAILED", [
            'username' => $username,
            'reason' => $reason
        ]);
    }
    
    /**
     * Log logout
     */
    public static function logLogout($username) {
        self::log("🚪 LOGOUT", ['username' => $username]);
    }
    
    /**
     * Log redirect issues
     */
    public static function logRedirect($from, $to, $reason = '') {
        self::log("↩️ REDIRECT", [
            'from' => $from,
            'to' => $to,
            'reason' => $reason
        ]);
    }
    
    /**
     * Log database errors
     */
    public static function logDatabaseError($error) {
        self::log("💾 DATABASE ERROR", ['error' => $error]);
    }
    
    /**
     * Get last 50 log entries
     */
    public static function getRecentLogs($lines = 50) {
        if (!file_exists(self::$logFile)) {
            return "No logs found.";
        }
        
        $content = file_get_contents(self::$logFile);
        $entries = explode(str_repeat('-', 80), $content);
        $entries = array_filter($entries);
        $entries = array_slice($entries, -$lines);
        
        return implode(str_repeat('-', 80), $entries);
    }
    
    /**
     * Clear logs
     */
    public static function clearLogs() {
        if (file_exists(self::$logFile)) {
            unlink(self::$logFile);
        }
    }
}
?>

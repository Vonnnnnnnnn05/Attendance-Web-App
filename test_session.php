<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$sid = session_id();
echo "<h1>Session Test</h1>";
echo "<p>Session ID: " . htmlspecialchars($sid) . "</p>";

if (!isset($_SESSION['counter'])) {
    $_SESSION['counter'] = 0;
    echo "<p>Counter initialized to 0</p>";
} else {
    $_SESSION['counter']++;
    echo "<p>Counter incremented to: " . $_SESSION['counter'] . "</p>";
}

echo "<p><a href='test_session.php'>Reload Page</a></p>";
echo "<p>Server Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<pre>Session Data:\n";
print_r($_SESSION);
echo "</pre>";
?>

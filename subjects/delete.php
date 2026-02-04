<?php
/**
 * Delete Subject
 */
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Get subject ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Check if subject exists
    $subject = fetchOne("SELECT id, subject_name FROM subjects WHERE id = ?", [$id]);
    
    if ($subject) {
        // Delete subject (this will also delete related records due to CASCADE)
        $result = query("DELETE FROM subjects WHERE id = ?", [$id]);
        
        if ($result) {
            $_SESSION['success'] = 'Subject deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete subject. Please try again.';
        }
    } else {
        $_SESSION['error'] = 'Subject not found.';
    }
} else {
    $_SESSION['error'] = 'Invalid subject ID.';
}

header('Location: index.php');
exit();
?>

<?php
/**
 * Delete Student
 */
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Get student ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Check if student exists
    $student = fetchOne("SELECT id, full_name FROM students WHERE id = ?", [$id]);
    
    if ($student) {
        // Delete student (this will also delete related attendance records due to CASCADE)
        $result = query("DELETE FROM students WHERE id = ?", [$id]);
        
        if ($result) {
            $_SESSION['success'] = 'Student deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete student. Please try again.';
        }
    } else {
        $_SESSION['error'] = 'Student not found.';
    }
} else {
    $_SESSION['error'] = 'Invalid student ID.';
}

header('Location: index.php');
exit();
?>

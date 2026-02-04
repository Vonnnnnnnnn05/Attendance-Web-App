<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page name for active menu
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="description" content="Student Management System with Attendance Tracking and QR Code Scanner">
    <meta name="keywords" content="student management, attendance, qr code, education">
    <meta name="author" content="Student Management System">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#000000">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SMS">
    
    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/png" sizes="192x192" href="./assets/images/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="192x192" href="./assets/images/icon-192x192.png">
    <link rel="shortcut icon" href="./assets/images/icon-192x192.png">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="./manifest.json">
    
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Student Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/amsp/assets/css/style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/amsp/index.php">
                <i class="bi bi-mortarboard-fill"></i> Student Management System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="/amsp/index.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/students/') !== false ? 'active' : ''; ?>" href="/amsp/students/index.php">
                            <i class="bi bi-people-fill"></i> Students
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo strpos($_SERVER['REQUEST_URI'], '/subjects/') !== false ? 'active' : ''; ?>" href="#" id="subjectsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-book-fill"></i> Subjects
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/amsp/subjects/index.php">All Subjects</a></li>
                            <li><a class="dropdown-item" href="/amsp/subjects/enrollments.php"><i class="bi bi-person-lines-fill"></i> Student Enrollments</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo strpos($_SERVER['REQUEST_URI'], '/attendance/') !== false ? 'active' : ''; ?>" href="#" id="attendanceDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-calendar-check"></i> Attendance
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/amsp/attendance/mark.php">Mark Attendance</a></li>
                            <li><a class="dropdown-item" href="/amsp/attendance/view.php">View Records</a></li>
                            <li><a class="dropdown-item" href="/amsp/attendance/report.php">Reports</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/amsp/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <!-- Main Content Container -->
    <div class="container-fluid main-container">

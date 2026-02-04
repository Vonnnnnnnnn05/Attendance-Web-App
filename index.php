<?php
/**
 * Dashboard - Main landing page
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication immediately and redirect if not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: /amsp/auth/login.php');
    exit();
}

$page_title = 'Dashboard';
require_once 'includes/auth_check.php';
require_once 'config/database.php';

// Fetch statistics
$total_students = fetchOne("SELECT COUNT(*) as count FROM students")['count'];
$total_subjects = fetchOne("SELECT COUNT(*) as count FROM subjects")['count'];
$today_attendance = fetchOne("SELECT COUNT(*) as count FROM attendance WHERE attendance_date = CURDATE()")['count'];
$present_today = fetchOne("SELECT COUNT(*) as count FROM attendance WHERE attendance_date = CURDATE() AND status = 'Present'")['count'];

// Recent attendance records
$recent_attendance = fetchAll("
    SELECT a.*, s.full_name, s.student_id, sub.subject_name 
    FROM attendance a
    JOIN students s ON a.student_id = s.id
    JOIN subjects sub ON a.subject_id = sub.id
    ORDER BY a.created_at DESC
    LIMIT 10
");

require_once 'includes/header.php';
?>

<!-- PWA Install Banner -->
<div class="row" id="pwaInstallBanner" style="display: none;">
    <div class="col-12 mb-3">
        <div class="alert alert-dark alert-dismissible fade show d-flex align-items-center justify-content-between" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-phone-fill fs-4 me-3"></i>
                <div>
                    <strong>Install Student Management System</strong>
                    <p class="mb-0 small">Add this app to your home screen for quick access and offline use!</p>
                </div>
            </div>
            <button type="button" class="btn btn-light btn-sm ms-3" id="pwaInstallButton">
                <i class="bi bi-download"></i> Install App
            </button>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h2>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card">
            <i class="bi bi-people-fill"></i>
            <h3><?php echo $total_students; ?></h3>
            <p>Total Students</p>
            <a href="/amsp/students/index.php" class="btn btn-sm btn-outline-dark mt-2">View All</a>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card">
            <i class="bi bi-book-fill"></i>
            <h3><?php echo $total_subjects; ?></h3>
            <p>Total Subjects</p>
            <a href="/amsp/subjects/index.php" class="btn btn-sm btn-outline-dark mt-2">View All</a>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card">
            <i class="bi bi-calendar-check"></i>
            <h3><?php echo $today_attendance; ?></h3>
            <p>Today's Attendance</p>
            <a href="/amsp/attendance/mark.php" class="btn btn-sm btn-outline-dark mt-2">Mark Now</a>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card">
            <i class="bi bi-check-circle-fill"></i>
            <h3><?php echo $present_today; ?></h3>
            <p>Present Today</p>
            <a href="/amsp/attendance/view.php" class="btn btn-sm btn-outline-dark mt-2">View Records</a>
        </div>
    </div>
</div>

<!-- Recent Attendance -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history"></i> Recent Attendance Records
            </div>
            <div class="card-body">
                <?php if (count($recent_attendance) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_attendance as $record): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($record['student_id']); ?></td>
                                        <td><?php echo htmlspecialchars($record['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($record['subject_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($record['attendance_date'])); ?></td>
                                        <td>
                                            <?php
                                            $badge_class = match($record['status']) {
                                                'Present' => 'badge-present',
                                                'Absent' => 'badge-absent',
                                                'Late' => 'badge-late',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>">
                                                <?php echo htmlspecialchars($record['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('h:i A', strtotime($record['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No attendance records found.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-lightning-fill"></i> Quick Actions
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="/amsp/students/add.php" class="btn btn-dark w-100 py-3">
                            <i class="bi bi-person-plus-fill"></i><br>Add New Student
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="/amsp/subjects/add.php" class="btn btn-dark w-100 py-3">
                            <i class="bi bi-book-half"></i><br>Add New Subject
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="/amsp/attendance/mark.php" class="btn btn-dark w-100 py-3">
                            <i class="bi bi-calendar-check-fill"></i><br>Mark Attendance
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="/amsp/attendance/report.php" class="btn btn-dark w-100 py-3">
                            <i class="bi bi-file-earmark-bar-graph"></i><br>View Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

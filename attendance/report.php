<?php
/**
 * Attendance Reports & Statistics
 */
$page_title = 'Attendance Reports';
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Date range filter
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Overall statistics
$total_records = fetchOne("SELECT COUNT(*) as count FROM attendance WHERE attendance_date BETWEEN ? AND ?", [$start_date, $end_date])['count'];
$present_count = fetchOne("SELECT COUNT(*) as count FROM attendance WHERE status = 'Present' AND attendance_date BETWEEN ? AND ?", [$start_date, $end_date])['count'];
$absent_count = fetchOne("SELECT COUNT(*) as count FROM attendance WHERE status = 'Absent' AND attendance_date BETWEEN ? AND ?", [$start_date, $end_date])['count'];
$late_count = fetchOne("SELECT COUNT(*) as count FROM attendance WHERE status = 'Late' AND attendance_date BETWEEN ? AND ?", [$start_date, $end_date])['count'];

// Attendance by student
$student_stats = fetchAll("
    SELECT s.id, s.student_id, s.full_name, s.course,
           COUNT(a.id) as total_records,
           SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as present_count,
           SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) as absent_count,
           SUM(CASE WHEN a.status = 'Late' THEN 1 ELSE 0 END) as late_count,
           ROUND((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / COUNT(a.id)) * 100, 2) as attendance_rate
    FROM students s
    LEFT JOIN attendance a ON s.id = a.student_id AND a.attendance_date BETWEEN ? AND ?
    GROUP BY s.id
    HAVING total_records > 0
    ORDER BY attendance_rate DESC, s.full_name ASC
", [$start_date, $end_date]);

// Attendance by subject
$subject_stats = fetchAll("
    SELECT sub.id, sub.subject_code, sub.subject_name,
           COUNT(a.id) as total_records,
           SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as present_count,
           SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) as absent_count,
           SUM(CASE WHEN a.status = 'Late' THEN 1 ELSE 0 END) as late_count,
           ROUND((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / COUNT(a.id)) * 100, 2) as attendance_rate
    FROM subjects sub
    LEFT JOIN attendance a ON sub.id = a.subject_id AND a.attendance_date BETWEEN ? AND ?
    GROUP BY sub.id
    HAVING total_records > 0
    ORDER BY attendance_rate DESC
", [$start_date, $end_date]);

// Daily attendance trend
$daily_stats = fetchAll("
    SELECT attendance_date,
           COUNT(*) as total,
           SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present,
           SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent,
           SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) as late
    FROM attendance
    WHERE attendance_date BETWEEN ? AND ?
    GROUP BY attendance_date
    ORDER BY attendance_date DESC
    LIMIT 14
", [$start_date, $end_date]);

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4"><i class="bi bi-file-earmark-bar-graph"></i> Attendance Reports</h2>
    </div>
</div>

<!-- Date Range Filter -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-calendar-range"></i> Date Range
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-dark w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-outline-dark w-100" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Overall Statistics -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card">
            <i class="bi bi-graph-up"></i>
            <h3><?php echo $total_records; ?></h3>
            <p>Total Records</p>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card">
            <i class="bi bi-check-circle"></i>
            <h3><?php echo $present_count; ?></h3>
            <p>Present</p>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card">
            <i class="bi bi-x-circle"></i>
            <h3><?php echo $absent_count; ?></h3>
            <p>Absent</p>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card">
            <i class="bi bi-clock"></i>
            <h3><?php echo $late_count; ?></h3>
            <p>Late</p>
        </div>
    </div>
</div>

<!-- Attendance by Student -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-people"></i> Attendance by Student
    </div>
    <div class="card-body">
        <?php if (count($student_stats) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Course</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Late</th>
                            <th>Total</th>
                            <th>Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($student_stats as $stat): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($stat['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($stat['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($stat['course']); ?></td>
                                <td><span class="badge badge-present"><?php echo $stat['present_count']; ?></span></td>
                                <td><span class="badge badge-absent"><?php echo $stat['absent_count']; ?></span></td>
                                <td><span class="badge badge-late"><?php echo $stat['late_count']; ?></span></td>
                                <td><?php echo $stat['total_records']; ?></td>
                                <td>
                                    <strong><?php echo number_format($stat['attendance_rate'], 1); ?>%</strong>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-dark" style="width: <?php echo $stat['attendance_rate']; ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No attendance data available for the selected date range.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Attendance by Subject -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-book"></i> Attendance by Subject
    </div>
    <div class="card-body">
        <?php if (count($subject_stats) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Late</th>
                            <th>Total</th>
                            <th>Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subject_stats as $stat): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($stat['subject_code']); ?></strong></td>
                                <td><?php echo htmlspecialchars($stat['subject_name']); ?></td>
                                <td><span class="badge badge-present"><?php echo $stat['present_count']; ?></span></td>
                                <td><span class="badge badge-absent"><?php echo $stat['absent_count']; ?></span></td>
                                <td><span class="badge badge-late"><?php echo $stat['late_count']; ?></span></td>
                                <td><?php echo $stat['total_records']; ?></td>
                                <td>
                                    <strong><?php echo number_format($stat['attendance_rate'], 1); ?>%</strong>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-dark" style="width: <?php echo $stat['attendance_rate']; ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No attendance data available for the selected date range.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Daily Trend -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-graph-up-arrow"></i> Daily Attendance Trend (Last 14 Days)
    </div>
    <div class="card-body">
        <?php if (count($daily_stats) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Late</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daily_stats as $stat): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($stat['attendance_date'])); ?></td>
                                <td><span class="badge badge-present"><?php echo $stat['present']; ?></span></td>
                                <td><span class="badge badge-absent"><?php echo $stat['absent']; ?></span></td>
                                <td><span class="badge badge-late"><?php echo $stat['late']; ?></span></td>
                                <td><strong><?php echo $stat['total']; ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No daily attendance data available.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

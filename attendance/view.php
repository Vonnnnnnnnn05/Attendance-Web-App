<?php
/**
 * View Attendance Records
 */
$page_title = 'Attendance Records';
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Get filter parameters
$filter_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$filter_subject = isset($_GET['subject']) ? (int)$_GET['subject'] : 0;
$filter_student = isset($_GET['student']) ? (int)$_GET['student'] : 0;

// Build query
$where_conditions = [];
$params = [];

if (!empty($filter_date)) {
    $where_conditions[] = "a.attendance_date = ?";
    $params[] = $filter_date;
}

if ($filter_subject > 0) {
    $where_conditions[] = "a.subject_id = ?";
    $params[] = $filter_subject;
}

if ($filter_student > 0) {
    $where_conditions[] = "a.student_id = ?";
    $params[] = $filter_student;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Fetch attendance records
$sql = "SELECT a.*, s.full_name, s.student_id as student_number, s.course,
        sub.subject_name, sub.subject_code, u.full_name as marked_by_name
        FROM attendance a
        JOIN students s ON a.student_id = s.id
        JOIN subjects sub ON a.subject_id = sub.id
        JOIN users u ON a.marked_by = u.id
        $where_clause
        ORDER BY a.attendance_date DESC, s.full_name ASC";
$records = fetchAll($sql, $params);

// Get subjects and students for filters
$subjects = fetchAll("SELECT * FROM subjects ORDER BY subject_name");
$students = fetchAll("SELECT id, student_id, full_name FROM students ORDER BY full_name");

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4"><i class="bi bi-calendar-check"></i> Attendance Records</h2>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-funnel"></i> Filter Records
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($filter_date); ?>">
            </div>

            <div class="col-md-3">
                <label for="subject" class="form-label">Subject</label>
                <select class="form-select" id="subject" name="subject">
                    <option value="0">All Subjects</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?php echo $subject['id']; ?>" <?php echo ($filter_subject == $subject['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="student" class="form-label">Student</label>
                <select class="form-select" id="student" name="student">
                    <option value="0">All Students</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?php echo $student['id']; ?>" <?php echo ($filter_student == $student['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($student['student_id'] . ' - ' . $student['full_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-dark w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Results -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul"></i> Attendance Records (<?php echo count($records); ?>)</span>
        <div>
            <a href="mark.php" class="btn btn-light btn-sm">
                <i class="bi bi-plus-circle"></i> Mark Attendance
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (count($records) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Marked By</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $record): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($record['attendance_date'])); ?></td>
                                <td><?php echo htmlspecialchars($record['student_number']); ?></td>
                                <td><?php echo htmlspecialchars($record['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($record['course']); ?></td>
                                <td><?php echo htmlspecialchars($record['subject_code']); ?></td>
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
                                <td><?php echo htmlspecialchars($record['marked_by_name']); ?></td>
                                <td><?php echo date('h:i A', strtotime($record['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No attendance records found for the selected filters.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

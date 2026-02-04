<?php
/**
 * Student-Subject Enrollment Management
 */
$page_title = 'Student Enrollment';
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Handle enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll'])) {
    $student_id = (int)$_POST['student_id'];
    $subject_ids = $_POST['subjects'] ?? [];
    
    if ($student_id > 0 && !empty($subject_ids)) {
        $success_count = 0;
        $duplicate_count = 0;
        
        foreach ($subject_ids as $subject_id) {
            // Check if already enrolled
            $check = fetchOne("SELECT id FROM student_subjects WHERE student_id = ? AND subject_id = ?", 
                             [$student_id, $subject_id]);
            
            if (!$check) {
                query("INSERT INTO student_subjects (student_id, subject_id) VALUES (?, ?)", 
                     [$student_id, $subject_id]);
                $success_count++;
            } else {
                $duplicate_count++;
            }
        }
        
        $msg = "Enrollment successful! ";
        if ($success_count > 0) $msg .= "$success_count subject(s) added. ";
        if ($duplicate_count > 0) $msg .= "$duplicate_count already enrolled.";
        $_SESSION['success'] = $msg;
    } else {
        $_SESSION['error'] = 'Please select a student and at least one subject.';
    }
}

// Handle unenrollment
if (isset($_GET['unenroll'])) {
    $enrollment_id = (int)$_GET['unenroll'];
    query("DELETE FROM student_subjects WHERE id = ?", [$enrollment_id]);
    $_SESSION['success'] = 'Student unenrolled successfully!';
}

// Get all students
$students = fetchAll("SELECT id, student_id, full_name, course, year_level FROM students ORDER BY full_name");

// Get all subjects
$subjects = fetchAll("SELECT id, subject_code, subject_name FROM subjects ORDER BY subject_name");

// Get all enrollments
$enrollments = fetchAll("
    SELECT ss.id, s.student_id, s.full_name, s.course, s.year_level,
           sub.subject_code, sub.subject_name, ss.enrolled_at
    FROM student_subjects ss
    JOIN students s ON ss.student_id = s.id
    JOIN subjects sub ON ss.subject_id = sub.id
    ORDER BY s.full_name, sub.subject_name
");

$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4"><i class="bi bi-person-lines-fill"></i> Student Subject Enrollment</h2>
    </div>
</div>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill"></i> <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Enrollment Form -->
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-plus-circle"></i> Enroll Student in Subjects
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="enroll" value="1">
                    
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Select Student *</label>
                        <select class="form-select" id="student_id" name="student_id" required>
                            <option value="">Choose a student...</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?php echo $student['id']; ?>">
                                    <?php echo htmlspecialchars($student['student_id'] . ' - ' . $student['full_name'] . ' (' . $student['course'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Subjects to Enroll *</label>
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <?php if (count($subjects) > 0): ?>
                                <?php foreach ($subjects as $subject): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="subjects[]" 
                                               value="<?php echo $subject['id']; ?>" 
                                               id="subject_<?php echo $subject['id']; ?>">
                                        <label class="form-check-label" for="subject_<?php echo $subject['id']; ?>">
                                            <strong><?php echo htmlspecialchars($subject['subject_code']); ?></strong> - 
                                            <?php echo htmlspecialchars($subject['subject_name']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No subjects available. <a href="/amsp/subjects/add.php">Add subjects first</a>.</p>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted">You can select multiple subjects</small>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">
                        <i class="bi bi-check-circle"></i> Enroll Student
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Current Enrollments -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list-check"></i> Current Enrollments (<?php echo count($enrollments); ?>)
            </div>
            <div class="card-body">
                <?php if (count($enrollments) > 0): ?>
                    <!-- Search Filter -->
                    <div class="mb-3">
                        <input type="text" class="form-control" id="searchEnrollment" 
                               placeholder="Search by student name or subject..." 
                               onkeyup="filterEnrollments()">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="enrollmentTable">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th>Enrolled Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($enrollments as $enrollment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($enrollment['student_id']); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($enrollment['full_name']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($enrollment['course']); ?></small>
                                        </td>
                                        <td><span class="badge bg-dark"><?php echo htmlspecialchars($enrollment['subject_code']); ?></span></td>
                                        <td><?php echo htmlspecialchars($enrollment['subject_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($enrollment['enrolled_at'])); ?></td>
                                        <td>
                                            <a href="?unenroll=<?php echo $enrollment['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Remove this enrollment?')">
                                                <i class="bi bi-x-circle"></i> Unenroll
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No enrollments yet. Start by enrolling students in subjects!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="bi bi-people-fill" style="font-size: 2rem;"></i>
                <h3><?php echo count($students); ?></h3>
                <p class="mb-0">Total Students</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="bi bi-book-fill" style="font-size: 2rem;"></i>
                <h3><?php echo count($subjects); ?></h3>
                <p class="mb-0">Total Subjects</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="bi bi-diagram-3-fill" style="font-size: 2rem;"></i>
                <h3><?php echo count($enrollments); ?></h3>
                <p class="mb-0">Total Enrollments</p>
            </div>
        </div>
    </div>
</div>

<script>
function filterEnrollments() {
    const input = document.getElementById('searchEnrollment');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('enrollmentTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        let found = false;
        const td = tr[i].getElementsByTagName('td');
        
        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        tr[i].style.display = found ? '' : 'none';
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>

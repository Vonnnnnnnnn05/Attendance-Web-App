<?php
/**
 * Mark Attendance Page
 */
$page_title = 'Mark Attendance';
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Get subjects for dropdown
$subjects = fetchAll("SELECT * FROM subjects ORDER BY subject_name");

// Get students
$students = fetchAll("SELECT * FROM students ORDER BY full_name");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = isset($_POST['subject_id']) ? (int)$_POST['subject_id'] : 0;
    $attendance_date = isset($_POST['attendance_date']) ? $_POST['attendance_date'] : '';
    $student_attendance = $_POST['attendance'] ?? [];
    
    $errors = [];
    $success_count = 0;
    $duplicate_count = 0;
    
    if (empty($subject_id)) $errors[] = 'Please select a subject.';
    if (empty($attendance_date)) $errors[] = 'Please select a date.';
    if (empty($student_attendance)) $errors[] = 'Please mark attendance for at least one student.';
    
    if (empty($errors)) {
        foreach ($student_attendance as $student_id => $status) {
            // Check if attendance already exists
            $existing = fetchOne(
                "SELECT id FROM attendance WHERE student_id = ? AND subject_id = ? AND attendance_date = ?",
                [$student_id, $subject_id, $attendance_date]
            );
            
            if ($existing) {
                // Update existing attendance
                query(
                    "UPDATE attendance SET status = ?, marked_by = ? WHERE id = ?",
                    [$status, $_SESSION['user_id'], $existing['id']]
                );
                $duplicate_count++;
            } else {
                // Insert new attendance
                query(
                    "INSERT INTO attendance (student_id, subject_id, attendance_date, status, marked_by) VALUES (?, ?, ?, ?, ?)",
                    [$student_id, $subject_id, $attendance_date, $status, $_SESSION['user_id']]
                );
                $success_count++;
            }
        }
        
        $message = "Attendance marked successfully! ";
        if ($success_count > 0) $message .= "$success_count new record(s) created. ";
        if ($duplicate_count > 0) $message .= "$duplicate_count existing record(s) updated.";
        
        $_SESSION['success'] = $message;
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

// Handle QR code scan submission
if (isset($_POST['qr_scan'])) {
    $qr_code = trim($_POST['qr_code']);
    $subject_id = (int)$_POST['qr_subject_id'];
    $attendance_date = $_POST['qr_attendance_date'];
    
    if (!empty($qr_code) && $subject_id > 0 && !empty($attendance_date)) {
        // Find student by QR code (student_id)
        $student = fetchOne("SELECT id FROM students WHERE student_id = ?", [$qr_code]);
        
        if ($student) {
            // Check if attendance already exists
            $existing = fetchOne(
                "SELECT id FROM attendance WHERE student_id = ? AND subject_id = ? AND attendance_date = ?",
                [$student['id'], $subject_id, $attendance_date]
            );
            
            if ($existing) {
                $_SESSION['error'] = 'Attendance already marked for this student today.';
            } else {
                // Mark as present
                query(
                    "INSERT INTO attendance (student_id, subject_id, attendance_date, status, marked_by) VALUES (?, ?, ?, 'Present', ?)",
                    [$student['id'], $subject_id, $attendance_date, $_SESSION['user_id']]
                );
                $_SESSION['success'] = 'Attendance marked as Present via QR code!';
            }
        } else {
            $_SESSION['error'] = 'Student not found with QR code: ' . htmlspecialchars($qr_code);
        }
    }
}

$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4"><i class="bi bi-calendar-check"></i> Mark Attendance</h2>
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

<!-- QR Code Attendance Section -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-qr-code-scan"></i> Quick Attendance via QR Code Scanner
    </div>
    <div class="card-body">
        <!-- Camera Scanner -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h5><i class="bi bi-camera-fill"></i> Scan QR Code with Camera</h5>
                <div id="qr-reader" style="width: 100%; max-width: 500px; border: 2px solid #000; border-radius: 8px;"></div>
                <div id="qr-reader-results" class="mt-3"></div>
            </div>
            <div class="col-md-6">
                <h5><i class="bi bi-info-circle"></i> Instructions</h5>
                <ol>
                    <li>Select the <strong>Subject</strong> and <strong>Date</strong> below</li>
                    <li>Click <strong>"Start Camera Scanner"</strong></li>
                    <li>Allow camera access when prompted</li>
                    <li>Point camera at student's QR code</li>
                    <li>Attendance will be marked automatically!</li>
                </ol>
                <div class="alert alert-info mt-3">
                    <i class="bi bi-lightbulb"></i> <strong>Tip:</strong> Works best with good lighting and steady hands!
                </div>
            </div>
        </div>

        <hr>

        <form method="POST" id="qrAttendanceForm" class="row g-3">
            <input type="hidden" name="qr_scan" value="1">
            <input type="hidden" name="qr_code" id="scanned_qr_code">
            
            <div class="col-md-4">
                <label for="qr_subject_id" class="form-label">Subject *</label>
                <select class="form-select" id="qr_subject_id" name="qr_subject_id" required>
                    <option value="">Select Subject</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?php echo $subject['id']; ?>">
                            <?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="qr_attendance_date" class="form-label">Date *</label>
                <input type="date" class="form-control" id="qr_attendance_date" name="qr_attendance_date" 
                       value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-dark w-100" id="startScannerBtn" onclick="startScanner()">
                    <i class="bi bi-camera-fill"></i> Start Camera Scanner
                </button>
            </div>

            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-outline-danger w-100" id="stopScannerBtn" onclick="stopScanner()" style="display:none;">
                    <i class="bi bi-stop-circle"></i> Stop Scanner
                </button>
            </div>
        </form>

        <hr>

        <h5>Or Manual Entry:</h5>
        <form method="POST" class="row g-3">
            <input type="hidden" name="qr_scan" value="1">
            
            <div class="col-md-4">
                <label for="qr_subject_id" class="form-label">Subject *</label>
                <select class="form-select" id="qr_subject_id" name="qr_subject_id" required>
                    <option value="">Select Subject</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?php echo $subject['id']; ?>">
                            <?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="qr_attendance_date" class="form-label">Date *</label>
                <input type="date" class="form-control" id="qr_attendance_date" name="qr_attendance_date" 
                       value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="col-md-3">
                <label for="qr_code" class="form-label">Scan/Enter Student ID *</label>
                <input type="text" class="form-control" id="qr_code" name="qr_code" 
                       placeholder="Scan QR or type Student ID" required autofocus>
            </div>

            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-dark w-100">
                    <i class="bi bi-check-circle"></i> Mark Present
                </button>
            </div>
        </form>
        
        <div class="alert alert-info mt-3 mb-0">
            <i class="bi bi-info-circle"></i> <strong>Quick Tip:</strong> Scan the student's QR code or manually enter their Student ID to instantly mark them as Present.
        </div>
    </div>
</div>

<!-- Manual Attendance Form -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list-check"></i> Manual Attendance Entry
    </div>
    <div class="card-body">
        <form method="POST" id="attendanceForm">
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="subject_id" class="form-label">Subject *</label>
                    <select class="form-select" id="subject_id" name="subject_id" required>
                        <option value="">Select Subject</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?php echo $subject['id']; ?>">
                                <?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="attendance_date" class="form-label">Attendance Date *</label>
                    <input type="date" class="form-control" id="attendance_date" name="attendance_date" 
                           value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>

            <?php if (count($students) > 0): ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label">Mark Attendance for Students</label>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-dark" onclick="markAllAs('Present')">
                                <i class="bi bi-check-all"></i> All Present
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-dark" onclick="markAllAs('Absent')">
                                <i class="bi bi-x-circle"></i> All Absent
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th width="100">Student ID</th>
                                    <th>Full Name</th>
                                    <th>Course</th>
                                    <th width="250" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['course']); ?></td>
                                        <td>
                                            <div class="btn-group w-100" role="group">
                                                <input type="radio" class="btn-check" name="attendance[<?php echo $student['id']; ?>]" 
                                                       id="present_<?php echo $student['id']; ?>" value="Present">
                                                <label class="btn btn-outline-success btn-sm" for="present_<?php echo $student['id']; ?>">
                                                    <i class="bi bi-check-circle"></i> Present
                                                </label>

                                                <input type="radio" class="btn-check" name="attendance[<?php echo $student['id']; ?>]" 
                                                       id="late_<?php echo $student['id']; ?>" value="Late">
                                                <label class="btn btn-outline-warning btn-sm" for="late_<?php echo $student['id']; ?>">
                                                    <i class="bi bi-clock"></i> Late
                                                </label>

                                                <input type="radio" class="btn-check" name="attendance[<?php echo $student['id']; ?>]" 
                                                       id="absent_<?php echo $student['id']; ?>" value="Absent">
                                                <label class="btn btn-outline-danger btn-sm" for="absent_<?php echo $student['id']; ?>">
                                                    <i class="bi bi-x-circle"></i> Absent
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark btn-lg">
                    <i class="bi bi-save"></i> Save Attendance
                </button>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> No students found. Please <a href="/amsp/students/add.php">add students</a> first.
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Include HTML5 QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrCode = null;
let scannerRunning = false;

function startScanner() {
    const subjectId = document.getElementById('qr_subject_id').value;
    const attendanceDate = document.getElementById('qr_attendance_date').value;
    
    if (!subjectId) {
        alert('Please select a subject first!');
        return;
    }
    
    if (!attendanceDate) {
        alert('Please select a date first!');
        return;
    }
    
    // Show/hide buttons
    document.getElementById('startScannerBtn').style.display = 'none';
    document.getElementById('stopScannerBtn').style.display = 'block';
    
    // Initialize scanner
    html5QrCode = new Html5Qrcode("qr-reader");
    
    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };
    
    html5QrCode.start(
        { facingMode: "environment" }, // Use back camera
        config,
        onScanSuccess,
        onScanError
    ).then(() => {
        scannerRunning = true;
        document.getElementById('qr-reader-results').innerHTML = 
            '<div class="alert alert-info"><i class="bi bi-camera-fill"></i> Scanner active. Point camera at QR code...</div>';
    }).catch(err => {
        alert('Unable to start camera: ' + err);
        stopScanner();
    });
}

function stopScanner() {
    if (html5QrCode && scannerRunning) {
        html5QrCode.stop().then(() => {
            scannerRunning = false;
            document.getElementById('startScannerBtn').style.display = 'block';
            document.getElementById('stopScannerBtn').style.display = 'none';
            document.getElementById('qr-reader-results').innerHTML = '';
        });
    } else {
        document.getElementById('startScannerBtn').style.display = 'block';
        document.getElementById('stopScannerBtn').style.display = 'none';
    }
}

function onScanSuccess(decodedText, decodedResult) {
    // QR code scanned successfully
    console.log(`QR Code detected: ${decodedText}`);
    
    // Stop scanner
    stopScanner();
    
    // Show scanning result
    document.getElementById('qr-reader-results').innerHTML = 
        `<div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i> QR Code Scanned: <strong>${decodedText}</strong><br>
            Marking attendance...
        </div>`;
    
    // Set the scanned QR code and submit form
    document.getElementById('scanned_qr_code').value = decodedText;
    document.getElementById('qrAttendanceForm').submit();
}

function onScanError(errorMessage) {
    // Scanner error (usually just no QR code detected, which is normal)
    // Don't show errors to avoid spam
}

function markAllAs(status) {
    const radios = document.querySelectorAll(`input[value="${status}"]`);
    radios.forEach(radio => {
        radio.checked = true;
    });
}

// Auto-stop scanner when leaving page
window.addEventListener('beforeunload', function() {
    if (scannerRunning) {
        stopScanner();
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>

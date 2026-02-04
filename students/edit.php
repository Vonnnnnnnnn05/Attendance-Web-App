<?php
/**
 * Edit Student Page
 */
$page_title = 'Edit Student';
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Get student ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch student data
$student = fetchOne("SELECT * FROM students WHERE id = ?", [$id]);

if (!$student) {
    $_SESSION['error'] = 'Student not found.';
    header('Location: index.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $full_name = trim($_POST['full_name']);
    $gender = $_POST['gender'];
    $course = trim($_POST['course']);
    $year_level = $_POST['year_level'];
    $email = trim($_POST['email']);
    
    // Validate input
    $errors = [];
    
    if (empty($student_id)) $errors[] = 'Student ID is required.';
    if (empty($full_name)) $errors[] = 'Full name is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
    
    // Check for duplicate student ID or email (excluding current student)
    if (empty($errors)) {
        $check = fetchOne("SELECT id FROM students WHERE (student_id = ? OR email = ?) AND id != ?", 
                         [$student_id, $email, $id]);
        if ($check) {
            $errors[] = 'Student ID or Email already exists.';
        }
    }
    
    if (empty($errors)) {
        // Update student
        $sql = "UPDATE students SET student_id = ?, full_name = ?, gender = ?, course = ?, 
                year_level = ?, email = ?, qr_code = ? WHERE id = ?";
        $result = query($sql, [$student_id, $full_name, $gender, $course, $year_level, $email, $student_id, $id]);
        
        if ($result) {
            $_SESSION['success'] = 'Student updated successfully!';
            header('Location: index.php');
            exit();
        } else {
            $errors[] = 'Failed to update student. Please try again.';
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Student</h2>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-file-earmark-text"></i> Student Information
            </div>
            <div class="card-body">
                <form method="POST" data-validate="true">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="student_id" class="form-label">Student ID *</label>
                            <input type="text" class="form-control" id="student_id" name="student_id" required 
                                   value="<?php echo htmlspecialchars($_POST['student_id'] ?? $student['student_id']); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required 
                                   value="<?php echo htmlspecialchars($_POST['full_name'] ?? $student['full_name']); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender *</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <?php
                                $current_gender = $_POST['gender'] ?? $student['gender'];
                                $genders = ['Male', 'Female', 'Other'];
                                foreach ($genders as $g) {
                                    $selected = ($current_gender == $g) ? 'selected' : '';
                                    echo "<option value=\"$g\" $selected>$g</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="year_level" class="form-label">Year Level *</label>
                            <select class="form-select" id="year_level" name="year_level" required>
                                <option value="">Select Year Level</option>
                                <?php
                                $current_year = $_POST['year_level'] ?? $student['year_level'];
                                $years = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];
                                foreach ($years as $y) {
                                    $selected = ($current_year == $y) ? 'selected' : '';
                                    echo "<option value=\"$y\" $selected>$y</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="course" class="form-label">Course *</label>
                        <input type="text" class="form-control" id="course" name="course" required 
                               value="<?php echo htmlspecialchars($_POST['course'] ?? $student['course']); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? $student['email']); ?>">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-save"></i> Update Student
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-qr-code"></i> QR Code
            </div>
            <div class="card-body text-center">
                <?php
                $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($student['student_id']);
                ?>
                <div class="qr-code-container">
                    <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code" class="qr-code-img">
                    <p class="mt-3"><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></p>
                    <a href="<?php echo $qrCodeUrl; ?>" download="student_<?php echo $student['student_id']; ?>_qr.png" class="btn btn-dark btn-sm">
                        <i class="bi bi-download"></i> Download
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Information
            </div>
            <div class="card-body">
                <p><strong>Created:</strong><br><?php echo date('M d, Y h:i A', strtotime($student['created_at'])); ?></p>
                <p><strong>Last Updated:</strong><br><?php echo date('M d, Y h:i A', strtotime($student['updated_at'])); ?></p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

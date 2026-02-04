<?php
/**
 * Add Student Page
 */
$page_title = 'Add Student';
require_once '../includes/auth_check.php';
require_once '../config/database.php';

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
    
    // Check for duplicate student ID or email
    if (empty($errors)) {
        $check = fetchOne("SELECT id FROM students WHERE student_id = ? OR email = ?", [$student_id, $email]);
        if ($check) {
            $errors[] = 'Student ID or Email already exists.';
        }
    }
    
    if (empty($errors)) {
        // Insert student
        $sql = "INSERT INTO students (student_id, full_name, gender, course, year_level, email, qr_code) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $result = query($sql, [$student_id, $full_name, $gender, $course, $year_level, $email, $student_id]);
        
        if ($result) {
            $_SESSION['success'] = 'Student added successfully!';
            header('Location: index.php');
            exit();
        } else {
            $errors[] = 'Failed to add student. Please try again.';
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
        <h2 class="mb-4"><i class="bi bi-person-plus-fill"></i> Add New Student</h2>
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
                                   value="<?php echo isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : ''; ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required 
                                   value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender *</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="year_level" class="form-label">Year Level *</label>
                            <select class="form-select" id="year_level" name="year_level" required>
                                <option value="">Select Year Level</option>
                                <option value="1st Year" <?php echo (isset($_POST['year_level']) && $_POST['year_level'] == '1st Year') ? 'selected' : ''; ?>>1st Year</option>
                                <option value="2nd Year" <?php echo (isset($_POST['year_level']) && $_POST['year_level'] == '2nd Year') ? 'selected' : ''; ?>>2nd Year</option>
                                <option value="3rd Year" <?php echo (isset($_POST['year_level']) && $_POST['year_level'] == '3rd Year') ? 'selected' : ''; ?>>3rd Year</option>
                                <option value="4th Year" <?php echo (isset($_POST['year_level']) && $_POST['year_level'] == '4th Year') ? 'selected' : ''; ?>>4th Year</option>
                                <option value="5th Year" <?php echo (isset($_POST['year_level']) && $_POST['year_level'] == '5th Year') ? 'selected' : ''; ?>>5th Year</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="course" class="form-label">Course *</label>
                        <input type="text" class="form-control" id="course" name="course" required 
                               placeholder="e.g., Computer Science, Information Technology"
                               value="<?php echo isset($_POST['course']) ? htmlspecialchars($_POST['course']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-save"></i> Save Student
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
                <i class="bi bi-info-circle"></i> Information
            </div>
            <div class="card-body">
                <p><strong>Note:</strong> Fields marked with * are required.</p>
                <hr>
                <p><strong>QR Code:</strong> A QR code will be automatically generated for this student using their Student ID.</p>
                <hr>
                <p><strong>Student ID:</strong> Must be unique for each student.</p>
                <hr>
                <p><strong>Email:</strong> Must be a valid email address and unique.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

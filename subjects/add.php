<?php
/**
 * Add Subject Page
 */
$page_title = 'Add Subject';
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_code = trim($_POST['subject_code']);
    $subject_name = trim($_POST['subject_name']);
    $description = trim($_POST['description']);
    
    // Validate input
    $errors = [];
    
    if (empty($subject_code)) $errors[] = 'Subject code is required.';
    if (empty($subject_name)) $errors[] = 'Subject name is required.';
    
    // Check for duplicate subject code
    if (empty($errors)) {
        $check = fetchOne("SELECT id FROM subjects WHERE subject_code = ?", [$subject_code]);
        if ($check) {
            $errors[] = 'Subject code already exists.';
        }
    }
    
    if (empty($errors)) {
        // Insert subject
        $sql = "INSERT INTO subjects (subject_code, subject_name, description) VALUES (?, ?, ?)";
        $result = query($sql, [$subject_code, $subject_name, $description]);
        
        if ($result) {
            $_SESSION['success'] = 'Subject added successfully!';
            header('Location: index.php');
            exit();
        } else {
            $errors[] = 'Failed to add subject. Please try again.';
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
        <h2 class="mb-4"><i class="bi bi-book-half"></i> Add New Subject</h2>
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
                <i class="bi bi-file-earmark-text"></i> Subject Information
            </div>
            <div class="card-body">
                <form method="POST" data-validate="true">
                    <div class="mb-3">
                        <label for="subject_code" class="form-label">Subject Code *</label>
                        <input type="text" class="form-control" id="subject_code" name="subject_code" required 
                               placeholder="e.g., CS101, MATH201"
                               value="<?php echo isset($_POST['subject_code']) ? htmlspecialchars($_POST['subject_code']) : ''; ?>">
                        <small class="text-muted">A unique identifier for the subject</small>
                    </div>

                    <div class="mb-3">
                        <label for="subject_name" class="form-label">Subject Name *</label>
                        <input type="text" class="form-control" id="subject_name" name="subject_name" required 
                               placeholder="e.g., Introduction to Programming"
                               value="<?php echo isset($_POST['subject_name']) ? htmlspecialchars($_POST['subject_name']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" 
                                  placeholder="Brief description of the subject (optional)"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-save"></i> Save Subject
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
                <p><strong>Subject Code:</strong> Must be unique for each subject.</p>
                <hr>
                <p><strong>Description:</strong> Optional field to provide additional context about the subject.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

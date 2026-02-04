<?php
/**
 * Edit Subject Page
 */
$page_title = 'Edit Subject';
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Get subject ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch subject data
$subject = fetchOne("SELECT * FROM subjects WHERE id = ?", [$id]);

if (!$subject) {
    $_SESSION['error'] = 'Subject not found.';
    header('Location: index.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_code = trim($_POST['subject_code']);
    $subject_name = trim($_POST['subject_name']);
    $description = trim($_POST['description']);
    
    // Validate input
    $errors = [];
    
    if (empty($subject_code)) $errors[] = 'Subject code is required.';
    if (empty($subject_name)) $errors[] = 'Subject name is required.';
    
    // Check for duplicate subject code (excluding current subject)
    if (empty($errors)) {
        $check = fetchOne("SELECT id FROM subjects WHERE subject_code = ? AND id != ?", [$subject_code, $id]);
        if ($check) {
            $errors[] = 'Subject code already exists.';
        }
    }
    
    if (empty($errors)) {
        // Update subject
        $sql = "UPDATE subjects SET subject_code = ?, subject_name = ?, description = ? WHERE id = ?";
        $result = query($sql, [$subject_code, $subject_name, $description, $id]);
        
        if ($result) {
            $_SESSION['success'] = 'Subject updated successfully!';
            header('Location: index.php');
            exit();
        } else {
            $errors[] = 'Failed to update subject. Please try again.';
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
        <h2 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Subject</h2>
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
                               value="<?php echo htmlspecialchars($_POST['subject_code'] ?? $subject['subject_code']); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="subject_name" class="form-label">Subject Name *</label>
                        <input type="text" class="form-control" id="subject_name" name="subject_name" required 
                               value="<?php echo htmlspecialchars($_POST['subject_name'] ?? $subject['subject_name']); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($_POST['description'] ?? $subject['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-save"></i> Update Subject
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
                <p><strong>Created:</strong><br><?php echo date('M d, Y h:i A', strtotime($subject['created_at'])); ?></p>
                <p><strong>Last Updated:</strong><br><?php echo date('M d, Y h:i A', strtotime($subject['updated_at'])); ?></p>
                <hr>
                <?php
                $student_count = fetchOne("SELECT COUNT(*) as count FROM student_subjects WHERE subject_id = ?", [$id])['count'];
                ?>
                <p><strong>Enrolled Students:</strong> <?php echo $student_count; ?></p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

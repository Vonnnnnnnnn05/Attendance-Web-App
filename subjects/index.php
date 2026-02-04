<?php
/**
 * Subjects List Page
 */
$page_title = 'Subjects';
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Fetch subjects with student count
$sql = "SELECT s.*, 
        (SELECT COUNT(*) FROM student_subjects WHERE subject_id = s.id) as student_count 
        FROM subjects s 
        ORDER BY s.created_at DESC";
$subjects = fetchAll($sql);

// Get success/error messages
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4"><i class="bi bi-book-fill"></i> Subject Management</h2>
    </div>
</div>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($success); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul"></i> All Subjects</span>
        <a href="add.php" class="btn btn-light btn-sm">
            <i class="bi bi-plus-circle"></i> Add New Subject
        </a>
    </div>
    <div class="card-body">
        <?php if (count($subjects) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Description</th>
                            <th>Enrolled Students</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($subject['subject_code']); ?></strong></td>
                                <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars(substr($subject['description'] ?? '', 0, 50)) . (strlen($subject['description'] ?? '') > 50 ? '...' : ''); ?></td>
                                <td>
                                    <span class="badge bg-dark"><?php echo $subject['student_count']; ?> students</span>
                                </td>
                                <td>
                                    <a href="edit.php?id=<?php echo $subject['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="delete.php?id=<?php echo $subject['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirmDelete('Are you sure you want to delete this subject?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No subjects found. <a href="add.php" class="alert-link">Add a new subject</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

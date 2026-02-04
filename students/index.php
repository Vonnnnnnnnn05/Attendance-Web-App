<?php
/**
 * Students List Page
 */
$page_title = 'Students';
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clause = '';
$params = [];

if (!empty($search)) {
    $where_clause = "WHERE student_id LIKE ? OR full_name LIKE ? OR email LIKE ? OR course LIKE ?";
    $search_param = "%$search%";
    $params = [$search_param, $search_param, $search_param, $search_param];
}

// Fetch students
$sql = "SELECT * FROM students $where_clause ORDER BY created_at DESC";
$students = fetchAll($sql, $params);

// Get success/error messages
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4"><i class="bi bi-people-fill"></i> Student Management</h2>
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
        <span><i class="bi bi-list-ul"></i> All Students</span>
        <a href="add.php" class="btn btn-light btn-sm">
            <i class="bi bi-plus-circle"></i> Add New Student
        </a>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Search by Student ID, Name, Email, or Course..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </div>
        </form>

        <?php if (count($students) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped" id="studentsTable">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Gender</th>
                            <th>Course</th>
                            <th>Year Level</th>
                            <th>Email</th>
                            <th>QR Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['gender']); ?></td>
                                <td><?php echo htmlspecialchars($student['course']); ?></td>
                                <td><?php echo htmlspecialchars($student['year_level']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td>
                                    <?php if ($student['qr_code']): ?>
                                        <button class="btn btn-sm btn-outline-dark" onclick="showQRCode('<?php echo htmlspecialchars($student['student_id']); ?>')">
                                            <i class="bi bi-qr-code"></i> View
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="delete.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirmDelete('Are you sure you want to delete this student?')">
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
                <i class="bi bi-info-circle"></i> No students found. 
                <?php if (!empty($search)): ?>
                    <a href="index.php" class="alert-link">Clear search</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-qr-code"></i> Student QR Code</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="qrCodeContent">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showQRCode(studentId) {
    const modal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
    const content = document.getElementById('qrCodeContent');
    
    // Show loading
    content.innerHTML = '<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>';
    modal.show();
    
    // Generate QR Code using external API
    const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(studentId)}`;
    
    // Display QR Code
    content.innerHTML = `
        <div class="qr-code-container">
            <img src="${qrCodeUrl}" alt="QR Code" class="qr-code-img">
            <p class="mt-3"><strong>Student ID:</strong> ${studentId}</p>
            <a href="${qrCodeUrl}" download="student_${studentId}_qr.png" class="btn btn-dark mt-2">
                <i class="bi bi-download"></i> Download QR Code
            </a>
        </div>
    `;
}
</script>

<?php require_once '../includes/footer.php'; ?>

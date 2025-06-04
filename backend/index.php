<?php
require_once 'config.php';
include 'header.php';

// Check if search parameter exists
$search = $_GET['search'] ?? '';

// Prepare the query based on search parameter
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE no_pendaftaran LIKE ? ORDER BY created_at DESC");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY created_at DESC");
}
$students = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2>Student Records</h2>
    </div>
    <div class="col-md-6 text-end">
        <?php if(isset($_SESSION['admin_logged_in'])): ?>
            <a href="add_student.php" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                    <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"/>
                </svg>
                Add New Student
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-10">
                <input type="text" class="form-control" name="search" 
                       placeholder="Search by Registration Number" 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>
    </div>
</div>

<?php if(count($students) > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>School</th>
                    <th>Registration No.</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $student): ?>
                    <tr>
                        <td><?php echo $student['id']; ?></td>
                        <td><?php echo htmlspecialchars($student['nama_siswa']); ?></td>
                        <td><?php echo htmlspecialchars($student['asal_sekolah']); ?></td>
                        <td><?php echo htmlspecialchars($student['no_pendaftaran']); ?></td>
                        <td>
                            <span class="badge <?php echo $student['status_kelulusan'] === 'LULUS' ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo htmlspecialchars($student['status_kelulusan']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if(isset($_SESSION['admin_logged_in'])): ?>
                                <a href="edit_student.php?id=<?php echo $student['id']; ?>" 
                                   class="btn btn-sm btn-warning">Edit</a>
                                <a href="delete_student.php?id=<?php echo $student['id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                            <?php endif; ?>
                            <?php if($student['status_kelulusan'] === 'LULUS'): ?>
                                <a href="certificate.php?id=<?php echo $student['id']; ?>" 
                                   class="btn btn-sm btn-info" target="_blank">
                                   Certificate
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        No student records found.
        <?php if($search): ?>
            <a href="index.php" class="alert-link">Clear search</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>

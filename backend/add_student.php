<?php
require_once 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $nama_siswa = trim($_POST['nama_siswa'] ?? '');
    $asal_sekolah = trim($_POST['asal_sekolah'] ?? '');
    $no_pendaftaran = trim($_POST['no_pendaftaran'] ?? '');
    $status_kelulusan = $_POST['status_kelulusan'] ?? '';

    // Validate input
    if (empty($nama_siswa)) {
        $errors[] = "Student name is required";
    }
    if (empty($asal_sekolah)) {
        $errors[] = "School name is required";
    }
    if (empty($no_pendaftaran)) {
        $errors[] = "Registration number is required";
    }
    if (!in_array($status_kelulusan, ['LULUS', 'TIDAK LULUS'])) {
        $errors[] = "Invalid graduation status";
    }

    // Check if registration number already exists
    if (!empty($no_pendaftaran)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE no_pendaftaran = ?");
        $stmt->execute([$no_pendaftaran]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Registration number already exists";
        }
    }

    // If no errors, insert the record
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO students (nama_siswa, asal_sekolah, no_pendaftaran, status_kelulusan) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama_siswa, $asal_sekolah, $no_pendaftaran, $status_kelulusan]);
            
            header("Location: index.php?msg=Student added successfully");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

include 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title mb-4">Add New Student</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="add_student.php">
                    <div class="mb-3">
                        <label for="nama_siswa" class="form-label">Student Name</label>
                        <input type="text" class="form-control" id="nama_siswa" name="nama_siswa" 
                               value="<?php echo htmlspecialchars($_POST['nama_siswa'] ?? ''); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="asal_sekolah" class="form-label">School Name</label>
                        <input type="text" class="form-control" id="asal_sekolah" name="asal_sekolah" 
                               value="<?php echo htmlspecialchars($_POST['asal_sekolah'] ?? ''); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="no_pendaftaran" class="form-label">Registration Number</label>
                        <input type="text" class="form-control" id="no_pendaftaran" name="no_pendaftaran" 
                               value="<?php echo htmlspecialchars($_POST['no_pendaftaran'] ?? ''); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="status_kelulusan" class="form-label">Graduation Status</label>
                        <select class="form-select" id="status_kelulusan" name="status_kelulusan" required>
                            <option value="">Select Status</option>
                            <option value="LULUS" <?php echo (isset($_POST['status_kelulusan']) && $_POST['status_kelulusan'] === 'LULUS') ? 'selected' : ''; ?>>LULUS</option>
                            <option value="TIDAK LULUS" <?php echo (isset($_POST['status_kelulusan']) && $_POST['status_kelulusan'] === 'TIDAK LULUS') ? 'selected' : ''; ?>>TIDAK LULUS</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">Back to List</a>
                        <button type="submit" class="btn btn-primary">Add Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

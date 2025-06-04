<?php
require_once 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Get student ID from URL
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php?msg=No student ID provided");
    exit;
}

try {
    // First check if student exists
    $stmt = $pdo->prepare("SELECT id FROM students WHERE id = ?");
    $stmt->execute([$id]);
    
    if (!$stmt->fetch()) {
        header("Location: index.php?msg=Student not found");
        exit;
    }

    // Delete the student record
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: index.php?msg=Student deleted successfully");
    exit;
    
} catch (PDOException $e) {
    header("Location: index.php?msg=Error deleting student: " . urlencode($e->getMessage()));
    exit;
}
?>

<?php
session_start();
if ($_SESSION['user_type'] !== 'teacher') {
    header('Location: index.php');
    exit;
}

require_once 'config/database.php';

// Handle teacher feedback
if ($_POST && isset($_POST['action'])) {
    $status = ($_POST['action'] === 'accept') ? 'accepted' : 'rejected';
    
    $stmt = $pdo->prepare("UPDATE submissions SET status = ?, teacher_feedback = ?, reviewed_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$status, $_POST['feedback'], $_POST['submission_id']]);
}

// Get submissions for assignment
$assignment_id = $_GET['assignment_id'];
$stmt = $pdo->prepare("
    SELECT s.*, u.full_name as student_name, a.title as assignment_title
    FROM submissions s 
    JOIN users u ON s.student_id = u.id 
    JOIN assignments a ON s.assignment_id = a.id
    WHERE s.assignment_id = ? AND a.teacher_id = ?
    ORDER BY s.submitted_at DESC
");
$stmt->execute([$assignment_id, $_SESSION['user_id']]);
$submissions = $stmt->fetchAll();
?>
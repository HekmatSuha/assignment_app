<?php
session_start();
if ($_SESSION['user_type'] !== 'student') {
    header('Location: index.php');
    exit;
}

require_once 'config/database.php';

// Get assignments with student's submission status
$stmt = $pdo->prepare("
    SELECT a.*, s.status, s.teacher_feedback, s.submitted_at
    FROM assignments a 
    LEFT JOIN submissions s ON a.id = s.assignment_id AND s.student_id = ?
    ORDER BY a.due_date ASC
");
$stmt->execute([$_SESSION['user_id']]);
$assignments = $stmt->fetchAll();
?>
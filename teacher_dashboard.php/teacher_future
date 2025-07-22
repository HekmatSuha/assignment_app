<?php
session_start();
if ($_SESSION['user_type'] !== 'teacher') {
    header('Location: index.php');
    exit;
}

require_once 'config/database.php';

// Get teacher's assignments
$stmt = $pdo->prepare("
    SELECT a.*, COUNT(s.id) as submission_count 
    FROM assignments a 
    LEFT JOIN submissions s ON a.id = s.assignment_id 
    WHERE a.teacher_id = ? 
    GROUP BY a.id 
    ORDER BY a.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$assignments = $stmt->fetchAll();
?>
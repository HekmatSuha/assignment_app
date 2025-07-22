<?php
session_start();
if ($_SESSION['user_type'] !== 'teacher') {
    header('Location: index.php');
    exit;
}

if ($_POST) {
    require_once 'config/database.php';
    
    $stmt = $pdo->prepare("INSERT INTO assignments (teacher_id, title, description, due_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['title'],
        $_POST['description'],
        $_POST['due_date'] . ' ' . $_POST['due_time']
    ]);
    
    header('Location: teacher_dashboard.php');
    exit;
}
?>
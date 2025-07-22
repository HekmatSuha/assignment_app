<?php
session_start();
if ($_SESSION['user_type'] !== 'student') {
    header('Location: index.php');
    exit;
}

if ($_POST) {
    require_once 'config/database.php';
    
    // Handle file upload
    $target_dir = "uploads/";
    $file_extension = pathinfo($_FILES["assignment_file"]["name"], PATHINFO_EXTENSION);
    $new_filename = "assignment_" . $_POST['assignment_id'] . "_student_" . $_SESSION['user_id'] . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $target_file)) {
        // Save to database
        $stmt = $pdo->prepare("INSERT INTO submissions (assignment_id, student_id, file_path, student_comment) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE file_path = ?, student_comment = ?, submitted_at = CURRENT_TIMESTAMP");
        $stmt->execute([
            $_POST['assignment_id'],
            $_SESSION['user_id'],
            $target_file,
            $_POST['comment'],
            $target_file,
            $_POST['comment']
        ]);
        
        header('Location: student_dashboard.php');
        exit;
    }
}
?>
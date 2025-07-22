<?php
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user has specific role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /index.php');
        exit;
    }
}

// Redirect if not teacher
function requireTeacher() {
    requireLogin();
    if (!hasRole('teacher')) {
        header('Location: /student_dashboard.php');
        exit;
    }
}

// Redirect if not student
function requireStudent() {
    requireLogin();
    if (!hasRole('student')) {
        header('Location: /teacher_dashboard.php');
        exit;
    }
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Format date
function formatDate($date) {
    return date('M j, Y g:i A', strtotime($date));
}

// Check if deadline has passed
function isDeadlinePassed($deadline) {
    return strtotime($deadline) < time();
}

// Get file extension
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

// Check if file type is allowed
function isAllowedFileType($filename) {
    $allowed = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'zip', 'rar'];
    $extension = getFileExtension($filename);
    return in_array($extension, $allowed);
}

// Generate unique filename
function generateUniqueFilename($original_filename) {
    $extension = getFileExtension($original_filename);
    return uniqid() . '_' . time() . '.' . $extension;
}

// Set flash message
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Get and clear flash message
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}
?>


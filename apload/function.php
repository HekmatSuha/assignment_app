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
        header('Location: /assignment_system/index.php');
        exit;
    }
}

// Redirect if not teacher
function requireTeacher() {
    requireLogin();
    if (!hasRole('teacher')) {
        header('Location: /assignment_system/student/dashboard.php');
        exit;
    }
}

// Redirect if not student
function requireStudent() {
    requireLogin();
    if (!hasRole('student')) {
        header('Location: /assignment_system/teacher/dashboard.php');
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

// Upload file with validation
function uploadFile($file, $upload_dir = 'uploads/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'No file uploaded or upload error occurred.'];
    }
    
    $original_filename = $file['name'];
    $file_size = $file['size'];
    $tmp_name = $file['tmp_name'];
    
    // Validate file type
    if (!isAllowedFileType($original_filename)) {
        return ['success' => false, 'message' => 'File type not allowed.'];
    }
    
    // Validate file size (10MB max)
    if ($file_size > 10 * 1024 * 1024) {
        return ['success' => false, 'message' => 'File size too large. Maximum size is 10MB.'];
    }
    
    // Create upload directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            return ['success' => false, 'message' => 'Failed to create upload directory.'];
        }
    }
    
    // Generate unique filename
    $unique_filename = generateUniqueFilename($original_filename);
    $file_path = $upload_dir . $unique_filename;
    
    // Move uploaded file
    if (move_uploaded_file($tmp_name, $file_path)) {
        return [
            'success' => true,
            'file_path' => $file_path,
            'original_filename' => $original_filename,
            'unique_filename' => $unique_filename
        ];
    } else {
        return ['success' => false, 'message' => 'Failed to move uploaded file.'];
    }
}

// Delete uploaded file
function deleteUploadedFile($file_path) {
    if (file_exists($file_path)) {
        return unlink($file_path);
    }
    return true; // File doesn't exist, consider it deleted
}

// Format file size
function formatFileSize($bytes) {
    if ($bytes == 0) return '0 Bytes';
    $k = 1024;
    $sizes = array('Bytes', 'KB', 'MB', 'GB', 'TB');
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

// Validate assignment deadline
function validateAssignmentData($title, $description, $deadline) {
    $errors = [];
    
    if (empty(trim($title))) {
        $errors[] = 'Assignment title is required.';
    }
    
    if (empty(trim($description))) {
        $errors[] = 'Assignment description is required.';
    }
    
    if (empty($deadline)) {
        $errors[] = 'Assignment deadline is required.';
    } elseif (strtotime($deadline) <= time()) {
        $errors[] = 'Assignment deadline must be in the future.';
    }
    
    return $errors;
}
?>
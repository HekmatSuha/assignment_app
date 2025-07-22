<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (hasRole('teacher')) {
        header('Location: teacher/dashboard.php');
    } else {
        header('Location: student/dashboard.php');
    }
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize($_POST['full_name']);
    $role = $_POST['role'];
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($full_name) || empty($role)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (!in_array($role, ['teacher', 'student'])) {
        $error = 'Please select a valid role.';
    } else {
        try {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->fetch()) {
                $error = 'Username or email already exists.';
            } else {
                // Insert new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name) VALUES (?, ?, ?, ?, ?)");
                
                if ($stmt->execute([$username, $email, $hashed_password, $role, $full_name])) {
                    $success = 'Registration successful! You can now login.';
                    // Clear form data
                    $_POST = [];
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Registration failed. Please try again.';
        }
    }
}

$page_title = 'Register - Assignment System';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-card" style="max-width: 500px;">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus"></i>
                        Create Account
                    </h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" data-validate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">
                                    <i class="fas fa-id-card"></i> Full Name
                                </label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" 
                                       required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user"></i> Username
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email Address
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">
                                <i class="fas fa-user-tag"></i> I am a
                            </label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="">Select your role</option>
                                <option value="student" <?php echo (isset($_POST['role']) && $_POST['role'] == 'student') ? 'selected' : ''; ?>>
                                    Student
                                </option>
                                <option value="teacher" <?php echo (isset($_POST['role']) && $_POST['role'] == 'teacher') ? 'selected' : ''; ?>>
                                    Teacher
                                </option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Password
                                </label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       minlength="6" required>
                                <small class="text-muted">At least 6 characters</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock"></i> Confirm Password
                                </label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                       minlength="6" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-user-plus"></i> Create Account
                        </button>
                    </form>
                    
                    <hr>
                    
                    <div class="text-center">
                        <p class="mb-0">Already have an account? 
                            <a href="index.php" class="text-decoration-none">
                                <i class="fas fa-sign-in-alt"></i> Login here
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    
    <script>
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>

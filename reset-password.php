<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Set timezone
date_default_timezone_set('Asia/Manila');

$error = '';
$success = '';
$valid_token = false;
$token = isset($_GET['token']) ? sanitize_input($_GET['token']) : '';

if ($token) {
    $database = new Database();
    $db = $database->getConnection();

    // Get current time
    $current_time = date('Y-m-d H:i:s');

    try {
        // Check if token exists and is valid
        $query = "SELECT pr.*, u.email, u.student_id 
                  FROM password_resets pr 
                  JOIN users u ON pr.student_id = u.student_id 
                  WHERE pr.token = :token 
                  AND pr.created_at <= :current_time
                  AND pr.expires_at >= :current_time
                  LIMIT 1";
                  
        $stmt = $db->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":current_time", $current_time);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $valid_token = true;
            $reset_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $password = sanitize_input($_POST['password']);
                $confirm_password = sanitize_input($_POST['confirm_password']);
                
                if (strlen($password) < 8) {
                    $error = "Password must be at least 8 characters long.";
                } elseif ($password !== $confirm_password) {
                    $error = "Passwords do not match.";
                } else {
                    // Begin transaction
                    $db->beginTransaction();
                    
                    try {
                        // Update password
                        $update_query = "UPDATE users 
                                       SET password = :password 
                                       WHERE student_id = :student_id";
                        $update_stmt = $db->prepare($update_query);
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $update_stmt->bindParam(":password", $hashed_password);
                        $update_stmt->bindParam(":student_id", $reset_data['student_id']);
                        $update_stmt->execute();
                        
                        // Delete all reset tokens for this user
                        $delete_query = "DELETE FROM password_resets 
                                       WHERE student_id = :student_id";
                        $delete_stmt = $db->prepare($delete_query);
                        $delete_stmt->bindParam(":student_id", $reset_data['student_id']);
                        $delete_stmt->execute();
                        
                        // Commit transaction
                        $db->commit();
                        
                        $success = "Password has been reset successfully. You can now login with your new password.";
                        header("refresh:3;url=login.php");
                    } catch (Exception $e) {
                        // Rollback transaction on error
                        $db->rollBack();
                        error_log("Password reset error: " . $e->getMessage());
                        $error = "Failed to update password. Please try again.";
                    }
                }
            }
        } else {
            // Check if token exists but expired
            $check_query = "SELECT expires_at FROM password_resets WHERE token = :token";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bindParam(":token", $token);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                $token_data = $check_stmt->fetch(PDO::FETCH_ASSOC);
                if ($token_data['expires_at'] < $current_time) {
                    $error = "Reset token has expired. Please request a new password reset.";
                } else {
                    $error = "Invalid reset token. Please request a new password reset.";
                }
            } else {
                $error = "Invalid reset token. Please request a new password reset.";
            }
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $error = "An error occurred. Please try again later.";
    }
} else {
    $error = "No reset token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCSFP BSE - Reset Password</title>
    <link href="assets/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="signin-container">
        <div class="signin-form">
            <h2 class="text-center mb-4 form-title">Reset Password</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                    <?php if (!$valid_token): ?>
                        <div class="mt-3 text-center">
                            <a href="login.php" class="btn btn-primary">Back to Login</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                    <div class="mt-3 text-center">
                        <a href="login.php" class="btn btn-primary">Back to Login</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($valid_token && !$success): ?>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?token=" . $token; ?>">
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" name="password" id="password" 
                                   placeholder="Enter new password" required minlength="8">
                            <span class="input-group-text password-toggle" onclick="togglePassword('password')">
                                <i class="bi bi-eye-fill" id="toggleIcon1"></i>
                            </span>
                        </div>
                        <div class="form-text">Password must be at least 8 characters long.</div>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" name="confirm_password" 
                                   id="confirm_password" placeholder="Confirm new password" required minlength="8">
                            <span class="input-group-text password-toggle" onclick="togglePassword('confirm_password')">
                                <i class="bi bi-eye-fill" id="toggleIcon2"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-check-lg me-2"></i>Reset Password
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function togglePassword(inputId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(inputId === 'password' ? 'toggleIcon1' : 'toggleIcon2');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('bi-eye-fill');
            toggleIcon.classList.add('bi-eye-slash-fill');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('bi-eye-slash-fill');
            toggleIcon.classList.add('bi-eye-fill');
        }
    }
    </script>
</body>
</html>
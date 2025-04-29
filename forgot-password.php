<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Replace the autoload with manual includes
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Set timezone
date_default_timezone_set('Asia/Manila');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = sanitize_input($_POST['studentId']);
    
    $database = new Database();
    $db = $database->getConnection();

    try {
        // Begin transaction
        $db->beginTransaction();

        // Check if student exists and get their email
        $query = "SELECT email FROM users WHERE student_id = :student_id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Generate unique token
            $token = bin2hex(random_bytes(32));
            
            // Set timestamps
            $created_at = date('Y-m-d H:i:s');
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Delete any existing reset tokens for this user
            $delete_query = "DELETE FROM password_resets WHERE student_id = :student_id";
            $delete_stmt = $db->prepare($delete_query);
            $delete_stmt->bindParam(":student_id", $student_id);
            $delete_stmt->execute();
            
            // Insert new reset token
            $insert_query = "INSERT INTO password_resets (student_id, token, created_at, expires_at) 
                            VALUES (:student_id, :token, :created_at, :expires_at)";
            $insert_stmt = $db->prepare($insert_query);
            $insert_stmt->bindParam(":student_id", $student_id);
            $insert_stmt->bindParam(":token", $token);
            $insert_stmt->bindParam(":created_at", $created_at);
            $insert_stmt->bindParam(":expires_at", $expires_at);
            
            if ($insert_stmt->execute()) {
                // Commit transaction
                $db->commit();

                // Create reset link
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . 
                             dirname($_SERVER['PHP_SELF']) . 
                             "/reset-password.php?token=" . $token;
                
                // Email content
                $to = $user['email'];
                $subject = "Password Reset Request - CCSFP BSE";
                $message = "Hello,<br><br>";
                $message .= "You have requested to reset your password.<br><br>";
                $message .= "Please click the following link to reset your password:<br>";
                $message .= "<a href='" . $reset_link . "'>" . $reset_link . "</a><br><br>";
                $message .= "This link will expire in 1 hour.<br><br>";
                $message .= "If you did not request this password reset, please ignore this email.<br><br>";
                $message .= "Best regards,<br>CCSFP BSE Team";

                // PHPMailer implementation
                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->SMTPDebug = SMTP::DEBUG_OFF;
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'ccsfpbse@gmail.com';
                    $mail->Password   = 'ohdj tqfw bmso mrmh';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port       = 465;
                
                    // Set timeout and keep alive
                    $mail->Timeout = 60;
                    $mail->SMTPKeepAlive = true;
                
                    // Recipients
                    $mail->setFrom('ccsfpbse@gmail.com', 'CCSFP BSE');
                    $mail->addAddress($to);
                
                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;
                
                    $mail->send();
                    $success = "Password reset instructions have been sent to your email.";
                } catch (Exception $e) {
                    $error = "Failed to send reset email. Please try again later.";
                    error_log("Mailer Error: " . $mail->ErrorInfo);
                }
            } else {
                // Rollback transaction
                $db->rollBack();
                $error = "Failed to create reset token. Please try again.";
            }
        } else {
            $error = "No account found with that Student ID.";
        }
    } catch (PDOException $e) {
        // Rollback transaction
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log("Database error: " . $e->getMessage());
        $error = "An error occurred. Please try again later.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCSFP BSE - Forgot Password</title>
    <link href="assets/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="signin-container">
        <div class="signin-form">
            <h2 class="text-center mb-4 form-title">Forgot Password</h2>
            <p class="text-center mb-4">Enter your Student ID and we'll send you instructions to reset your password.</p>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                    <div class="mt-3 text-center">
                        <a href="login.php" class="btn btn-primary">Back to Login</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control" name="studentId" id="studentId" 
                               placeholder="Enter your Student ID" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-envelope-fill me-2"></i>Send Reset Instructions
                    </button>
                    
                    <div class="text-center">
                        <a href="login.php" class="text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Back to Login
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
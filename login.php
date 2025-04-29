<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = sanitize_input($_POST['studentId']);
    $password = sanitize_input($_POST['password']);
    $remember_me = isset($_POST['rememberMe']);

    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT * FROM users WHERE student_id = :student_id LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":student_id", $student_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (verify_password($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];

            if ($remember_me) {
                setcookie('remembered_user', $student_id, time() + (30 * 24 * 60 * 60), '/');
            }

            redirect_to('home.php');
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Student ID not found";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCSFP BSE</title>
    <link href="assets/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="signin-container">
        <div class="signin-form">
            <h3 class="text-center mb-4 welcome-title">Welcome to CCSFP Board of Student Election</h3>
            <h2 class="text-center mb-4 form-title">SIGN IN</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="d-flex flex-column justify-content-between flex-grow-1">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <input type="text" class="form-control" name="studentId" id="studentId" placeholder="Student ID" required>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                    <span class="input-group-text password-toggle" onclick="togglePassword()">
                        <i class="bi bi-eye-fill" id="toggleIcon"></i>
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="rememberMe" id="rememberMe">
                        <label class="form-check-label form-links" for="rememberMe">
                            Remember me
                        </label>
                    </div>
                    <a href="forgot-password.php" class="text-decoration-none form-links">Forgot your password?</a>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
                <div class="text-center mt-3 form-links">
                    Don't have an account? <a href="signup.php" class="text-decoration-none">Sign up</a>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/login.js"></script>
</body>
</html>
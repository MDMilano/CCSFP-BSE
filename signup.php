<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    // Sanitize inputs
    $student_id = sanitize_input($_POST['studentId']);
    $last_name = sanitize_input($_POST['lastName']);
    $first_name = sanitize_input($_POST['firstName']);
    $middle_name = sanitize_input($_POST['middleName']);
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);
    $confirm_password = sanitize_input($_POST['confirmPassword']);
    $course = sanitize_input($_POST['course']);
    $major = isset($_POST['major']) ? sanitize_input($_POST['major']) : null;
    $year = sanitize_input($_POST['year']);
    $section = sanitize_input($_POST['section']);

    // Validate inputs
    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if student ID already exists
        $query = "SELECT id FROM users WHERE student_id = :student_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error = "Student ID already exists";
        } else {
            // Insert new user
            $query = "INSERT INTO users (student_id, last_name, first_name, middle_name, email, password, course, major, year, section) 
                     VALUES (:student_id, :last_name, :first_name, :middle_name, :email, :password, :course, :major, :year, :section)";
            
            try {
                $stmt = $db->prepare($query);
                $hashed_password = hash_password($password);
                
                $stmt->bindParam(":student_id", $student_id);
                $stmt->bindParam(":last_name", $last_name);
                $stmt->bindParam(":first_name", $first_name);
                $stmt->bindParam(":middle_name", $middle_name);
                $stmt->bindParam(":email", $email);
                $stmt->bindParam(":password", $hashed_password);
                $stmt->bindParam(":course", $course);
                $stmt->bindParam(":major", $major);
                $stmt->bindParam(":year", $year);
                $stmt->bindParam(":section", $section);

                if ($stmt->execute()) {
                    $success = "Registration successful! Please login.";
                    header("refresh:2;url=login.php");
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCSFP BSE - Sign Up</title>
    <link href="assets/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="signin-container">
        <div class="signin-form">
            <h2 class="text-center mb-4 form-title">SIGN UP</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <!-- Form fields remain the same as in the original HTML, just add name attributes -->
                <!-- Example for the first few fields: -->
                <div class="mb-3">
                    <label for="studentId" class="form-label">Student ID</label>
                    <input type="text" class="form-control" name="studentId" id="studentId" placeholder="Enter your student ID" required>
                </div>
                <div class="mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="lastName" id="lastName" placeholder="Enter your last name" required>
                </div>
                <div class="mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" name="firstName" id="firstName" placeholder="Enter your first name" required>
                </div>
                <div class="mb-3">
                    <label for="middleName" class="form-label">Middle Name</label>
                    <input type="text" class="form-control" name="middleName" id="middleName" placeholder="Enter your middle name (optional)">
                </div>
                <div class="mb-3">
                    <label for="course" class="form-label">Course</label>
                    <select class="form-select" name="course" id="course" required>
                        <option value="">Select a course</option>
                        <option value="BSIT">BS in Information Technology</option>
                        <option value="BSAIS">BS in Accounting Information System</option>
                        <option value="BSEE">BS in Elementary Education</option>
                        <option value="BSSE">BS in Secondary Education</option>
                        <option value="BSBA">BS in Business Administration</option>
                        <option value="BSECE">BS in Early Childhood Education</option>
                        <option value="BSE">BS in Entrepreneurship</option>
                    </select>
                </div>
                <div class="mb-3" id="majorContainer" style="display: none;">
                    <label for="major" class="form-label">Major</label>
                    <select class="form-select" name="major" id="major">
                        <option value="">Select a major</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="year" class="form-label">Year</label>
                    <select class="form-select" name="year" id="year" required>
                        <option value="">Select a year</option>
                        <option value="1">1st year</option>
                        <option value="2">2nd year</option>
                        <option value="3">3rd year</option>
                        <option value="4">4th year</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="section" class="form-label">Section</label>
                    <input type="text" class="form-control" name="section" id="section" placeholder="Enter your section">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email address" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Confirm your password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-person-plus-fill me-2"></i>Sign Up
                </button>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="signup.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset Form
                    </a>
                    <a href="login.php" class="btn btn-outline-secondary">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/signup.js"></script>
</body>
</html>
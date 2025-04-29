<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCSFP BSE - User Home</title>
    <!-- Bootstrap CSS -->
    <link href="assets/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="assets/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/home-styles.css" rel="stylesheet">
</head>
<body>
    <header class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="assets/image/CCSFP.png" alt="CCSFP Logo" height="30" class="d-inline-block align-top">
                <img src="assets/image/BSE.png" alt="CCSFP BSE Logo" height="30" class="d-inline-block align-top">
                <span class="d-none d-md-inline">CCSFP Board of Student Election</span>
                <span class="d-inline d-md-none">CCSFP BSE</span>
            </a>
            <div class="d-flex align-items-center">
                <div class="dropdown me-3">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="assets/image/user.jpeg" alt="User" width="32" height="32" class="rounded-circle">
                        <span class="d-none d-md-inline ms-2"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-small" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#userProfileModal">View Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Log out</a></li>
                    </ul>
                </div>
                <a href="logout.php" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-box-arrow-right"></i> <span class="d-none d-md-inline">Log out</span>
                </a>
            </div>
        </div>
    </header>
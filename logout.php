<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once 'includes/functions.php';

// Log the logout action if needed
if (isset($_SESSION['user_id'])) {
    // You could log the logout action here if desired
    // logUserAction($_SESSION['user_id'], 'logout');
}

// Clear all session variables
$_SESSION = array();

// If a session cookie is used, destroy it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Clear the remember me cookie if it exists
if (isset($_COOKIE['remembered_user'])) {
    setcookie('remembered_user', '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
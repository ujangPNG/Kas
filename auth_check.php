<?php
// Start session if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Get the current page URL
    $current_page = basename($_SERVER['PHP_SELF']);
    
    // Skip redirect for index.php and login.php
    if ($current_page != 'index.php' && $current_page != 'login.php') {
        header("Location: index.php");
        exit();
    }
}
?> 
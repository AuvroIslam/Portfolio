<?php
session_start();

// Clear remember me cookie and token from database
if (isset($_COOKIE['remember_admin'])) {
    $conn = new mysqli("localhost", "root", "", "portfolio");
    if (!$conn->connect_error) {
        $remember_token = $_COOKIE['remember_admin'];
        
        // Clear token from database
        $stmt = $conn->prepare("UPDATE admins SET remember_token=NULL, remember_token_created=NULL WHERE remember_token=?");
        $stmt->bind_param("s", $remember_token);
        $stmt->execute();
        $conn->close();
    }
    
    // Clear the cookie
    setcookie('remember_admin', '', time() - 3600, '/', '', false, true);
}

session_destroy();
header("Location: /Portfolio/admin/login.php");
exit;
?>
<?php
session_start(); // Start the session

// Check if the user is logged in
if(isset($_SESSION['user_id'])) {
    // Unset all of the session variables
    $_SESSION = array();

    // Destroy the session cookie
    if(isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }

    // Destroy the session
    session_destroy();
}

// Redirect the user to the login page or any other desired page after logout
header("Location: login.php");
exit;
?>
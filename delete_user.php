<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Database connection

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // Delete the user from the database
    $sql = "DELETE FROM users WHERE user_id = $user_id";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "User deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting user: " . mysqli_error($conn);
    }

    // Redirect back to the admin dashboard
    header("Location: admin_dashboard.php");
    exit();
} else {
    // If no user ID is provided, redirect back to the admin dashboard
    header("Location: admin_dashboard.php");
    exit();
}
?>

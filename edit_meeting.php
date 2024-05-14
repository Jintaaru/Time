<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Database connection

$user_id = $_SESSION['user_id'];

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Edit Meeting
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_meeting'])) {
    $meeting_id = sanitize_input($_POST['meeting_id']);
    $title = sanitize_input($_POST['title']);
    $start_date = sanitize_input($_POST['start']);
    $end_date = sanitize_input($_POST['end']);
    $description = sanitize_input($_POST['description']);

    // Update meeting in the database
    $sql = "UPDATE meetings SET title='$title', start_date='$start_date', end_date='$end_date', description='$description' WHERE id='$meeting_id' AND user_id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "Meeting updated successfully.";
    } else {
        echo "Error updating meeting: " . mysqli_error($conn);
    }
}

// Delete Meeting
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_meeting'])) {
    $meeting_id = sanitize_input($_POST['meeting_id']);

    // Delete meeting from the database
    $sql = "DELETE FROM meetings WHERE id='$meeting_id' AND user_id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "Meeting deleted successfully.";
    } else {
        echo "Error deleting meeting: " . mysqli_error($conn);
    }
}
?>

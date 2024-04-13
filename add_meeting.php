<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $start = $_POST['start'];
    $end = $_POST['end'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO meetings (user_id, title, start_date, end_date) VALUES ('$user_id', '$title', '$start', '$end')";
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>

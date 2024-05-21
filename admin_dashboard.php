<?php
session_start();

// Database connection
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch list of users from the database
$sql_users = "SELECT * FROM users";
$result_users = mysqli_query($conn, $sql_users);

// Function to fetch all users
function getUsers($conn)
{
    $sql = "SELECT id, email, username FROM users";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        return $users;
    } else {
        return [];
    }
}

// Function to add a new user
function addUser($conn, $username, $password, $email)
{
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$hashedPassword', '$email')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "User added successfully!";
    } else {
        echo "Error adding user: " . mysqli_error($conn);
    }
}

// Function to delete a user
function deleteUser($conn, $user_id)
{
    $sql = "DELETE FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "User deleted successfully!";
    } else {
        echo "Error deleting user: " . mysqli_error($conn);
    }
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_user"])) {
        $newUsername = $_POST["new_username"];
        $newPassword = $_POST["new_password"];
        $newEmail = $_POST["new_email"];
        addUser($conn, $newUsername, $newPassword, $newEmail);
    } elseif (isset($_POST["delete_user"])) {
        $userIdToDelete = $_POST["user_id_to_delete"];
        deleteUser($conn, $userIdToDelete);
    }
}

// Fetch all users
$users = getUsers($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Your HTML content goes here -->

    <!-- Display Users -->
    <h3>Users:</h3>
    <?php if (!empty($users)) : ?>
        <ul>
            <?php foreach ($users as $user) : ?>
                <li>ID: <?php echo $user['id']; ?>, Username: <?php echo $user['username']; ?>, Email: <?php echo $user['email']; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No users found.</p>
    <?php endif; ?>

    <!-- Add User Form -->
    <h3 class="add-user-title">Add User:</h3>
<form class="add-user-form" method="post">
    <label for="new_username" class="username-label">Username:</label>
    <input type="text" name="new_username" class="username-input" required>
    <label for="new_password" class="password-label">Password:</label>
    <input type="password" name="new_password" class="password-input" required>
    <label for="new_email" class="email-label">Email:</label>
    <input type="email" name="new_email" class="email-input" required>
    <input type="submit" name="add_user" value="Add User" class="add-user-button">
</form>

<!-- Delete User Form -->
<h3 class="delete-user-title">Delete User:</h3>
<form class="delete-user-form" method="post">
    <label for="user_id_to_delete" class="user-id-label">User ID to Delete:</label>
    <input type="number" name="user_id_to_delete" class="user-id-input" required>
    <input type="submit" name="delete_user" value="Delete User" class="delete-user-button">
</form>

<a href="index.php" id="Return_admin">Return to Calander</a>

</body>

</html>

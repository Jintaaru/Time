<!DOCTYPE html>
<html>

<head>
    <title>User Registration</title>
</head>

<body>
    <?php
    session_start();
    include 'db.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = 'user'; // Default role for new users
    
        $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Registration successful!";
            header("location: login.php");
        } else {
            $_SESSION['message'] = "Registration failed: " . mysqli_error($conn);
        }
    }
    ?>

    <h2>Register</h2>
    <form method="post" action="">
        <label>Username:</label>
        <input type="text" name="username" required><br>
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Register</button>
    </form>
    <?php
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    ?>
</body>

</html>
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}



if ($_SESSION['role'] == 'admin') {
    echo "You are logged in as an admin.";
}

include 'db.php'; // Database connection

$user_id = $_SESSION['user_id'];

// Fetch meetings where the current user is either the creator or the other user
$sql = "SELECT m.*, u.username as other_username 
        FROM meetings m
        JOIN users u ON (m.user_id = user_id)
        WHERE m.user_id = $user_id 
        UNION
        SELECT m.*, u.username as other_username
        FROM meetings m
        JOIN users u ON (m.user_id != $user_id AND m.user_id = user_id)";
$result = mysqli_query($conn, $sql);

// Fetch list of users from the database
$sql_users = "SELECT * FROM users WHERE username != $user_id"; // Exclude current user
$result_users = mysqli_query($conn, $sql_users);

// Check for SQL errors
if (!$result) {
    die("Error fetching meetings: " . mysqli_error($conn));
}
// Format data for dropdown menu
$users = array();
while ($row_users = mysqli_fetch_assoc($result_users)) {
    $users[] = $row_users;
}



// Format data for FullCalendar
$events = array();
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = array(
        'id' => $row['id'],
        'title' => $row['title'],
        'start' => $row['start_date'],
        'end' => $row['end_date'],
        'description' => $row['description'], // Comments
        'other_username' => $row['other_username'] // Username of the other user
    );
}
?>

<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='utf-8' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <!-- Other head elements go here -->
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-logo">
            <a href="home.php"><img src="logo.png" alt="Logo"></a>
        </div>
        <ul class="navbar-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
            <button><a href="logout.php">Logout</a></button>
        </ul>
    </nav>

    <!-- Sidebar and Calendar Container -->
    <div id="sidebar" class="sidebar">
        <ul>
            <li><a href="#calendar">Calendar</a></li>
            <li><a href="#" id="meetingsLink">Meetings</a></li>
            <!-- Add more navigation links as needed -->
        </ul>
    </div>

    <!-- Main content -->
    <div id="mainContent" class="main-content">
        <form id="createMeetingForm" action="create_meeting.php" method="POST">
            <label for="meetingTitle">Title:</label>
            <input type="text" id="meetingTitle" name="meetingTitle" required><br>
            <label for="meetingStart">Start:</label>
            <input type="datetime-local" id="meetingStart" name="meetingStart" required><br>
            <label for="meetingEnd">End:</label>
            <input type="datetime-local" id="meetingEnd" name="meetingEnd" required><br>
            <label for="meetingUser">Select User:</label>
            <select id="meetingUser" name="meetingUser" required>
                <?php foreach ($users as $user) { ?>
                    <option value="<?php echo $user['user_id']; ?>">
                        <?php echo $user['username']; ?>
                    </option>
                <?php } ?>
            </select><br>
            <button type="submit">Create Meeting</button>
        </form>

        <div id='calendar'></div>
    </div>
    </div>

    <!-- FullCalendar JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: <?php echo json_encode($events); ?>, // Pass meeting data to FullCalendar
                eventClick: function (info) {
                    var description = info.event.extendedProps.description;
                    var otherUsername = info.event.extendedProps.other_username;
                    if (description) {
                        alert("Comments: " + description + "\nOther User: " + otherUsername);
                    }
                }
            });
            calendar.render();

            var sidebar = document.getElementById('sidebar');
            var mainContent = document.getElementById('mainContent');

            // Toggle sidebar width and adjust main content position
            document.getElementById('toggleSidebar').addEventListener('click', function () {
                if (sidebar.classList.contains('collapsed')) {
                    sidebar.style.width = '200px'; // Expand sidebar
                    mainContent.style.marginLeft = '200px'; // Adjust main content position
                } else {
                    sidebar.style.width = '50px'; // Collapse sidebar
                    mainContent.style.marginLeft = '50px'; // Adjust main content position
                }
                sidebar.classList.toggle('collapsed');
            });

            // Open modal when "Meetings" link is clicked
            document.getElementById('meetingsLink').addEventListener('click', function (e) {
                e.preventDefault();
                document.getElementById('createMeetingForm').style.display = 'block';
            });
        });
    </script>
</body>

</html>
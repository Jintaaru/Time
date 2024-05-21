<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}



if ($_SESSION['role'] == 'admin') {
    echo "You are logged in as an admin. <a href='admin_dashboard.php'>Admin Dashboard</a>
    ";
}

include 'db.php'; // Database connection

$user_id = $_SESSION['user_id'];

// Fetch list of users from the database
$sql_users = "SELECT * FROM users WHERE username != $user_id"; // Exclude current user
$result_users = mysqli_query($conn, $sql_users);

// Fetch meetings where the current user is either the creator or the other user


$sql = "SELECT * FROM meetings WHERE user_id = $user_id";

$result = mysqli_query($conn, $sql);




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
            <a href="indexp.php"><img src="images/logo.png" alt="Logo"></a>
        </div>
        <ul class="navbar-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="contact.html">Contact</a></li>
            <button><a href="logout.php">Logout</a></button>
        </ul>
    </nav>

    <!-- Main content -->
    <div id="mainContent" class="main-content">
        <form id="createMeetingForm" action="add_meeting.php" method="POST">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required><br>
            <label for="start">Start:</label>
            <input type="datetime-local" id="start" name="start" required><br>
            <label for="end">End:</label>
            <input type="datetime-local" id="end" name="end" required><br>
            <button id="navbarlogout" type="submit">Add Meeting</button>
        </form>

        <div id='calendar'></div>

        <!-- Edit Meeting Formx -->
        <div id="editMeetingModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <form id="editMeetingForm" action="edit_meeting.php" method="POST">
                    <input type="hidden" id="editMeetingId" name="meeting_id">
                    <label for="editTitle">Title:</label>
                    <input type="text" id="editTitle" name="title" required><br>
                    <label for="editStart">Start:</label>
                    <input type="datetime-local" id="editStart" name="start" ><br>
                    <label for="editEnd">End:</label>
                    <input type="datetime-local" id="editEnd" name="end" ><br>
                    <label for="editDescription">Description:</label>
                    <textarea id="editDescription" name="description"></textarea><br>
                    <button type="submit" name="edit_meeting">Save Changes</button>
                    <button type="submit" name="delete_meeting">Delete Meeting</button>
                </form>
            </div>
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
                    var meeting = info.event;
                    document.getElementById('editMeetingId').value = meeting.id;
                    document.getElementById('editTitle').value = meeting.title;
                    document.getElementById('editStart').value = meeting.startStr;
                    document.getElementById('editEnd').value = meeting.endStr;
                    document.getElementById('editDescription').value = meeting.extendedProps.description;
                    document.getElementById('editMeetingModal').style.display = 'block';
                }
            });
            calendar.render();

            // Close modal when close button is clicked
            document.getElementsByClassName('close')[0].addEventListener('click', function () {
                document.getElementById('editMeetingModal').style.display = 'none';
            });
        });
    </script>
</body>

</html>




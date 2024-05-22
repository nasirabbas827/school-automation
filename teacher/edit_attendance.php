<?php
session_start();
include('config.php');

// Check if the user is logged in as a teacher
if (!isset($_SESSION["teacher_id"])) {
    header("Location: teacher_login.php");
    exit;
}

// Check if attendance ID is provided in the URL
if (!isset($_GET['attendance_id'])) {
    header("Location: view_attendance.php");
    exit;
}

$attendance_id = $_GET['attendance_id'];

// Fetch attendance record details
$sql = "SELECT * FROM Attendance WHERE attendance_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $attendance_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $status = $_POST['status'];

    // Update attendance record
    $sql = "UPDATE Attendance SET date = ?, status = ? WHERE attendance_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssi", $date, $status, $attendance_id);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect back to view attendance page
    header("Location: view_attendance.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Attendance</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>

<?php include('navbar.php'); ?>
<div class="container">
    <h2>Edit Attendance</h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" class="form-control" id="date" name="date" value="<?php echo $attendance['date']; ?>" required>
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status" required>
                <option value="Present" <?php if ($attendance['status'] == 'Present') echo 'selected'; ?>>Present</option>
                <option value="Absent" <?php if ($attendance['status'] == 'Absent') echo 'selected'; ?>>Absent</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

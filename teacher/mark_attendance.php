<?php
session_start();
include('config.php');

// Check if the user is logged in as a teacher
if (!isset($_SESSION["teacher_id"])) {
    header("Location: teacher_login.php");
    exit;
}

// Check if student ID is provided in the URL
if (!isset($_GET['student_id'])) {
    header("Location: teacher_dashboard.php");
    exit;
}

$student_id = $_GET['student_id'];

// Fetch student details
$sql = "SELECT * FROM Students WHERE student_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST['status'];
    $date = date("Y-m-d");

    // Insert attendance record
    $sql = "INSERT INTO Attendance (student_id, date, status) VALUES (?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iss", $student_id, $date, $status);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect back to the teacher dashboard
    header("Location: teacher_dashboard.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mark Attendance</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>

<?php include('navbar.php'); ?>

<div class="container">
    <h2>Mark Attendance for <?php echo $student['first_name'] . " " . $student['last_name']; ?></h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status" required>
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

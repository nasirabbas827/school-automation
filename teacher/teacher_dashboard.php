<?php
session_start();
include('config.php');

// Check if the user is logged in as a teacher
if (!isset($_SESSION["teacher_id"])) {
    header("Location: teacher_login.php");
    exit;
}

$teacher_id = $_SESSION["teacher_id"];

// Fetch the assigned class details
$sql = "SELECT * FROM Classes WHERE teacher_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $class = $result->fetch_assoc();
    $stmt->close();
}

// Fetch students of the assigned class
$students = [];
if ($class) {
    $sql = "SELECT * FROM Students WHERE class_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $class['class_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container">
    <h2>Welcome, <?php echo $_SESSION["teacher_name"]; ?></h2>
    <?php if ($class): ?>
        <h3>Assigned Class: <?php echo $class['class_name'] . " - " . $class['section']; ?></h3>
        <h4>Students:</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Profile Pic</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><img src="../admin/student_uploads/<?php echo $student['profile_pic']; ?>" alt="Profile Pic" style="width:50px;height:50px;"></td>
                        <td><?php echo $student['first_name']; ?></td>
                        <td><?php echo $student['last_name']; ?></td>
                        <td><?php echo $student['dob']; ?></td>
                        <td><?php echo $student['gender']; ?></td>
                        <td><?php echo $student['address']; ?></td>
                        <td><?php echo $student['phone']; ?></td>
                        <td><?php echo $student['email']; ?></td>
                        <td><a href="mark_attendance.php?student_id=<?php echo $student['student_id']; ?>" class="btn btn-primary">Mark Attendance</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You are not assigned to any class.</p>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

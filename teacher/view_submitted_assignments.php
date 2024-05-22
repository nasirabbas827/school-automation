<?php
session_start();
include('config.php');

// Check if the user is logged in as a teacher
if (!isset($_SESSION["teacher_id"])) {
    header("Location: teacher_login.php");
    exit;
}

// Fetch teacher details
$teacher_id = $_SESSION["teacher_id"];

// Check if assignment_id is provided in the URL
if (!isset($_GET["assignment_id"])) {
    header("Location: teacher_dashboard.php");
    exit;
}

$assignment_id = $_GET["assignment_id"];

// Fetch assignment details
$sql = "SELECT * FROM Assignments WHERE assignment_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $assignment = $result->fetch_assoc();
    $stmt->close();
}

// Fetch submitted assignments for the assignment
$submitted_assignments = [];
$sql = "SELECT sa.*, s.first_name, s.last_name FROM SubmittedAssignments sa JOIN Students s ON sa.student_id = s.student_id WHERE sa.assignment_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $submitted_assignments[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submitted Assignments</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mb-5">
    <h2>View Submitted Assignments</h2>
    <h3>Assignment: <?php echo $assignment['title']; ?></h3>
    <h4>Due Date: <?php echo $assignment['due_date']; ?></h4>
    <table class="table">
        <thead>
            <tr>
                <th>Student</th>
                <th>File Name</th>
                <th>Submission Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($submitted_assignments as $submission): ?>
                <tr>
                    <td><?php echo $submission['first_name'] . ' ' . $submission['last_name']; ?></td>
                    <td><?php echo $submission['file_name']; ?></td>
                    <td><?php echo $submission['submission_date']; ?></td>
                    <td>
                        <a href="../student/uploads/<?php echo $submission['file_name']; ?>" class="btn btn-success" download>Download</a>
                        <a href="grade_assignment.php?submission_id=<?php echo $submission['submission_id']; ?>" class="btn btn-primary">Grade</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

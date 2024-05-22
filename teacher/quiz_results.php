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
$sql = "SELECT class_id FROM Classes WHERE teacher_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $class = $result->fetch_assoc();
    $stmt->close();
}

// Fetch the courses assigned to the teacher's class
$courses = [];
if ($class) {
    $sql = "SELECT * FROM Courses WHERE class_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $class['class_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $courses = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

// Fetch quiz results with student and course names
$quiz_results = [];
if (!empty($class)) {
    $sql = "SELECT qr.*, s.first_name AS student_first_name, s.last_name AS student_last_name, c.course_name
            FROM Quiz_Results qr
            JOIN Students s ON qr.student_id = s.student_id
            JOIN Courses c ON qr.course_id = c.course_id
            WHERE c.class_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $class['class_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $quiz_results = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Quiz Results</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container">
    <h2>View Quiz Results</h2>
    <?php if (!empty($quiz_results)): ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Result ID</th>
                        <th>Student Name</th>
                        <th>Course Name</th>
                        <th>Total Questions</th>
                        <th>Correct Answers</th>
                        <th>Score</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quiz_results as $result): ?>
                        <tr>
                            <td><?php echo $result['result_id']; ?></td>
                            <td><?php echo $result['student_first_name'] . ' ' . $result['student_last_name']; ?></td>
                            <td><?php echo $result['course_name']; ?></td>
                            <td><?php echo $result['total_questions']; ?></td>
                            <td><?php echo $result['correct_answers']; ?></td>
                            <td><?php echo $result['score']; ?></td>
                            <td><?php echo $result['created_at']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No quiz results found.</p>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

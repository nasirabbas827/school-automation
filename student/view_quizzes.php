<?php
session_start();
include('config.php');

// Check if the user is logged in as a student
if (!isset($_SESSION["student_id"])) {
    header("Location: student_login.php");
    exit;
}

// Fetch student details
$student_id = $_SESSION["student_id"];
$sql = "SELECT * FROM Students WHERE student_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
}

// Check if course_id is provided in the URL
if (!isset($_GET["course_id"])) {
    header("Location: student_dashboard.php");
    exit;
}

$course_id = $_GET["course_id"];

// Fetch course details
$sql = "SELECT * FROM Courses WHERE course_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Quizzes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container">
    <h2>View Quizzes for <?php echo $course['course_name']; ?></h2>
    <h4>Course Description: <?php echo $course['description']; ?></h4>
    <hr>
    <h3>Quizzes:</h3>
    <ul>
        <!-- Replace these with actual quiz data fetched from the database -->
        <li>
            Quiz 1
            <a href="start_quiz.php?course_id=<?php echo $course['course_id']; ?>&quiz_id=1" class="btn btn-primary">Start Quiz</a>
        </li>
    </ul>
    <p>Instructions: You can attempt each quiz only once.</p>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

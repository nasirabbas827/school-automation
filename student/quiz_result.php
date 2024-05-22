<?php
session_start();
include('config.php');

// Check if the user is logged in as a student
if (!isset($_SESSION["student_id"])) {
    header("Location: student_login.php");
    exit;
}

// Check if all necessary parameters are provided in the URL
if (!isset($_GET["score"]) || !isset($_GET["total_questions"]) || !isset($_GET["correct_answers"]) || !isset($_GET["course_id"])) {
    header("Location: student_dashboard.php");
    exit;
}

// Extract quiz results from the URL parameters
$score = $_GET["score"];
$total_questions = $_GET["total_questions"];
$correct_answers = $_GET["correct_answers"];
$course_id = $_GET["course_id"];

// Fetch course details
$sql = "SELECT course_name FROM Courses WHERE course_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $course_name = $row["course_name"];
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
    <title>Quiz Result</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container">
    <h2>Quiz Result</h2>
    <p>Course: <?php echo $course_name; ?></p>
    <p>Total Questions: <?php echo $total_questions; ?></p>
    <p>Correct Answers: <?php echo $correct_answers; ?></p>
    <p>Score: <?php echo $score; ?>%</p>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

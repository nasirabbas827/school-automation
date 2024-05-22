<?php
session_start();
include('config.php');

// Check if the user is logged in as a student
if (!isset($_SESSION["student_id"])) {
    header("Location: student_login.php");
    exit;
}

$student_id = $_SESSION["student_id"];

// Fetch the student's quiz results with course information
$sql = "SELECT qr.*, c.course_name 
        FROM Quiz_Results qr
        INNER JOIN Courses c ON qr.course_id = c.course_id
        WHERE qr.student_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $quiz_results = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
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

<?php
session_start();
include('config.php');

// Check if the user is logged in as a student
if (!isset($_SESSION["student_id"])) {
    header("Location: student_login.php");
    exit;
}

// Check if course_id is provided in the URL
if (!isset($_GET["course_id"])) {
    header("Location: student_dashboard.php");
    exit;
}

// Extract course_id from the URL
$course_id = $_GET["course_id"];

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

// Check if quizzes are already submitted
$sql = "SELECT * FROM quiz_results WHERE student_id = ? AND course_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $student_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Quizzes are already submitted, redirect to quiz result page
        echo "You Already Submitted the Quizes";
        exit;
    }
    $stmt->close();
}

// Fetch quizzes for the course
$quizzes = [];
$sql = "SELECT * FROM Quizes WHERE course_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = $row;
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
    <title>Start Quiz</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mb-5">
    <h2>Start Quiz</h2>
    <?php if (!empty($quizzes)): ?>
        <form action="submit_quiz.php?course_id=<?php echo $course_id; ?>" method="post">
            <?php foreach ($quizzes as $quiz): ?>
                <input type="hidden" name="quiz_id[]" value="<?php echo $quiz['quiz_id']; ?>">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $quiz['question_text']; ?></h5>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="selected_option[<?php echo $quiz['quiz_id']; ?>]" value="1" required>
                            <label class="form-check-label"><?php echo $quiz['option_one']; ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="selected_option[<?php echo $quiz['quiz_id']; ?>]" value="2" required>
                            <label class="form-check-label"><?php echo $quiz['option_two']; ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="selected_option[<?php echo $quiz['quiz_id']; ?>]" value="3" required>
                            <label class="form-check-label"><?php echo $quiz['option_three']; ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="selected_option[<?php echo $quiz['quiz_id']; ?>]" value="4" required>
                            <label class="form-check-label"><?php echo $quiz['option_four']; ?></label>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary">Submit Quiz</button>
        </form>
    <?php else: ?>
        <p>No quizzes available for this course.</p>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

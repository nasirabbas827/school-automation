<?php
session_start();
include('config.php');

// Check if the user is logged in as a teacher
if (!isset($_SESSION["teacher_id"])) {
    header("Location: teacher_login.php");
    exit;
}

// Fetch quiz details
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];
    $sql = "SELECT Quizes.*, Courses.course_name FROM Quizes INNER JOIN Courses ON Quizes.course_id = Courses.course_id WHERE Quizes.quiz_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $quiz_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $quiz = $result->fetch_assoc();
        $stmt->close();
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quiz_id = $_POST['quiz_id'];
    $question_text = $_POST['question_text'];
    $option_one = $_POST['option_one'];
    $option_two = $_POST['option_two'];
    $option_three = $_POST['option_three'];
    $option_four = $_POST['option_four'];
    $correct_option = $_POST['correct_option'];

    // Update quiz
    $sql = "UPDATE Quizes SET question_text=?, option_one=?, option_two=?, option_three=?, option_four=?, correct_option=? WHERE quiz_id=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssi", $question_text, $option_one, $option_two, $option_three, $option_four, $correct_option, $quiz_id);
        $stmt->execute();
        $stmt->close();
        // Redirect back to view quizzes page
        header("Location: view_quizzes.php");
        exit;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Quiz</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container">
    <h2>Edit Quiz</h2>
    <form action="" method="post">
        <input type="hidden" name="quiz_id" value="<?php echo $quiz['quiz_id']; ?>">
        <div class="form-group">
            <label for="question_text">Question:</label>
            <textarea class="form-control" id="question_text" name="question_text" rows="3" required><?php echo $quiz['question_text']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="option_one">Option One:</label>
            <input type="text" class="form-control" id="option_one" name="option_one" value="<?php echo $quiz['option_one']; ?>" required>
        </div>
        <div class="form-group">
            <label for="option_two">Option Two:</label>
            <input type="text" class="form-control" id="option_two" name="option_two" value="<?php echo $quiz['option_two']; ?>" required>
        </div>
        <div class="form-group">
            <label for="option_three">Option Three:</label>
            <input type="text" class="form-control" id="option_three" name="option_three" value="<?php echo $quiz['option_three']; ?>" required>
        </div>
        <div class="form-group">
            <label for="option_four">Option Four:</label>
            <input type="text" class="form-control" id="option_four" name="option_four" value="<?php echo $quiz['option_four']; ?>" required>
        </div>
        <div class="form-group">
            <label for="correct_option">Correct Option:</label>
            <input type="number" class="form-control" id="correct_option" name="correct_option" min="1" max="4" value="<?php echo $quiz['correct_option']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Quiz</button>
        <a class="btn btn-outline-dark" href="view_quizzes.php">Cancel</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

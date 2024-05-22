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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];
    $question_text = $_POST['question_text'];
    $option_one = $_POST['option_one'];
    $option_two = $_POST['option_two'];
    $option_three = $_POST['option_three'];
    $option_four = $_POST['option_four'];
    $correct_option = $_POST['correct_option'];

    // Insert new quiz
    $sql = "INSERT INTO Quizes (course_id, question_text, option_one, option_two, option_three, option_four, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("isssssi", $course_id, $question_text, $option_one, $option_two, $option_three, $option_four, $correct_option);
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
    <title>Add Quiz</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container">
    <h2>Add Quiz</h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="course_id">Select Course:</label>
            <select class="form-control" id="course_id" name="course_id" required>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>"><?php echo $course['course_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="question_text">Question:</label>
            <textarea class="form-control" id="question_text" name="question_text" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="option_one">Option One:</label>
            <input type="text" class="form-control" id="option_one" name="option_one" required>
        </div>
        <div class="form-group">
            <label for="option_two">Option Two:</label>
            <input type="text" class="form-control" id="option_two" name="option_two" required>
        </div>
        <div class="form-group">
            <label for="option_three">Option Three:</label>
            <input type="text" class="form-control" id="option_three" name="option_three" required>
        </div>
        <div class="form-group">
            <label for="option_four">Option Four:</label>
            <input type="text" class="form-control" id="option_four" name="option_four" required>
        </div>
        <div class="form-group">
            <label for="correct_option">Correct Option:</label>
            <input type="number" class="form-control" id="correct_option" name="correct_option" min="1" max="4" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Quiz</button>
        <a class="btn btn-outline-dark" href="view_quizzes.php"> View Quizes</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

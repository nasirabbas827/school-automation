<?php
session_start();
include('config.php');

// Check if the user is logged in as a teacher
if (!isset($_SESSION["teacher_id"])) {
    header("Location: teacher_login.php");
    exit;
}

// Function to delete a quiz
function deleteQuiz($conn, $quiz_id) {
    $sql = "DELETE FROM Quizes WHERE quiz_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $quiz_id);
        $stmt->execute();
        $stmt->close();
        return true;
    } else {
        return false;
    }
}

// Fetch quizzes with subject names
$sql = "SELECT Quizes.*, Courses.course_name FROM Quizes INNER JOIN Courses ON Quizes.course_id = Courses.course_id";
$result = $conn->query($sql);

$quizzes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = $row;
    }
}

// Handle quiz deletion
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_quiz'])) {
    $quiz_id = $_GET['delete_quiz'];
    if (deleteQuiz($conn, $quiz_id)) {
        header("Location: view_quizzes.php");
        exit;
    } else {
        echo "Error deleting quiz.";
    }
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
    <h2>View Quizzes</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Quiz ID</th>
                <th>Course Name</th>
                <th>Question</th>
                <th>Option One</th>
                <th>Option Two</th>
                <th>Option Three</th>
                <th>Option Four</th>
                <th>Correct Option</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($quizzes as $quiz): ?>
                <tr>
                    <td><?php echo $quiz['quiz_id']; ?></td>
                    <td><?php echo $quiz['course_name']; ?></td>
                    <td><?php echo $quiz['question_text']; ?></td>
                    <td><?php echo $quiz['option_one']; ?></td>
                    <td><?php echo $quiz['option_two']; ?></td>
                    <td><?php echo $quiz['option_three']; ?></td>
                    <td><?php echo $quiz['option_four']; ?></td>
                    <td><?php echo $quiz['correct_option']; ?></td>
                    <td>
                        <a href="edit_quiz.php?quiz_id=<?php echo $quiz['quiz_id']; ?>" class="mb-2 btn btn-primary">Edit</a>
                        <a href="?delete_quiz=<?php echo $quiz['quiz_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this quiz?')">Delete</a>
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

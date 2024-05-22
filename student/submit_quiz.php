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

// Fetch student ID from session
$student_id = $_SESSION["student_id"];

// Check if all form fields are submitted
if (!isset($_POST["quiz_id"]) || !isset($_POST["selected_option"]) || !is_array($_POST["quiz_id"]) || !is_array($_POST["selected_option"])) {
    header("Location: start_quiz.php?course_id=$course_id");
    exit;
}

// Initialize variables to store quiz results
$total_questions = 0;
$correct_answers = 0;

// Loop through submitted answers and calculate correct answers
foreach ($_POST["quiz_id"] as $quiz_id) {
    // Check if quiz_id is integer
    if (!is_numeric($quiz_id)) {
        continue;
    }

    // Increment total questions count
    $total_questions++;

    // Check if selected_option is provided for this quiz
    if (isset($_POST["selected_option"][$quiz_id]) && is_numeric($_POST["selected_option"][$quiz_id])) {
        // Fetch correct option for this quiz from the database
        $sql = "SELECT correct_option FROM Quizes WHERE quiz_id = ? AND course_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ii", $quiz_id, $course_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                // Check if the selected option matches the correct option
                if ($_POST["selected_option"][$quiz_id] == $row["correct_option"]) {
                    $correct_answers++;
                }
            }
            $stmt->close();
        }
    }
}

// Calculate the score
$score = ($correct_answers / $total_questions) * 100;

// Insert quiz results into the database
$sql = "INSERT INTO quiz_results (student_id, course_id, total_questions, correct_answers, score) VALUES (?, ?, ?, ?, ?)";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("iiiii", $student_id, $course_id, $total_questions, $correct_answers, $score);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

// Redirect to a page showing the quiz results
header("Location: quiz_result.php?score=$score&total_questions=$total_questions&correct_answers=$correct_answers&course_id=$course_id");
exit;
?>

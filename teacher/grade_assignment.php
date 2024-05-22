<?php
session_start();
include('config.php');

// Check if the user is logged in as a teacher
if (!isset($_SESSION["teacher_id"])) {
    header("Location: teacher_login.php");
    exit;
}

// Check if submission_id is provided in the URL
if (!isset($_GET["submission_id"])) {
    header("Location: teacher_dashboard.php");
    exit;
}

$submission_id = $_GET["submission_id"];

// Fetch submitted assignment details
$sql = "SELECT sa.*, s.first_name, s.last_name FROM SubmittedAssignments sa JOIN Students s ON sa.student_id = s.student_id WHERE sa.submission_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $submission_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $submission = $result->fetch_assoc();
    $stmt->close();
}

// Initialize variables to store existing grade and comments
$existing_grade = "";
$existing_comments = "";

// Check if the submission already has a grade and comments
if (!empty($submission['grade'])) {
    $existing_grade = $submission['grade'];
}
if (!empty($submission['comments'])) {
    $existing_comments = $submission['comments'];
}

// Handle form submission for grading
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $grade = $_POST['grade'];
    $comments = $_POST['comments'];

    // Update the submitted assignment with grade and comments
    $sql = "UPDATE SubmittedAssignments SET grade = ?, comments = ? WHERE submission_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssi", $grade, $comments, $submission_id);
        $stmt->execute();
        $stmt->close();
        // Redirect back to view submitted assignments page
        header("Location: view_submitted_assignments.php?assignment_id=".$submission['assignment_id']);
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
    <title>Grade Assignment</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Grade Assignment</h2>
    <h3>Student: <?php echo $submission['first_name'] . ' ' . $submission['last_name']; ?></h3>
    <h4>Submission Date: <?php echo $submission['submission_date']; ?></h4>
    <form action="" method="post">
        <div class="form-group">
            <label for="grade">Grade:</label>
            <input type="text" class="form-control" id="grade" name="grade" value="<?php echo $existing_grade; ?>" required>
        </div>
        <div class="form-group">
            <label for="comments">Comments:</label>
            <textarea class="form-control" id="comments" name="comments" rows="3" required><?php echo $existing_comments; ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Grade</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
session_start();
include('config.php');

// Check if the user is logged in as a parent
if (!isset($_SESSION["parent_id"])) {
    header("Location: parent_login.php");
    exit;
}

$parent_id = $_SESSION["parent_id"];
$error = '';
$success = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ratings = $_POST['ratings'];
    $feedback_comment = $_POST['feedback_comment'];
    $date = date('Y-m-d');

    // Insert feedback into database
    $sql = "INSERT INTO Feedbacks (parent_id, ratings, feedback_comment, date) VALUES (?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iiss", $parent_id, $ratings, $feedback_comment, $date);
        if ($stmt->execute()) {
            $success = 'Feedback submitted successfully.';
        } else {
            $error = 'Error submitting feedback.';
        }
        $stmt->close();
    } else {
        $error = 'Database query error.';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container">
    <h2>Submit Feedback</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form action="add_feedback.php" method="post">
        <div class="form-group">
            <label for="ratings">Ratings (1 to 5):</label>
            <input type="number" class="form-control" id="ratings" name="ratings" min="1" max="5" required>
        </div>
        <div class="form-group">
            <label for="feedback_comment">Feedback Comment:</label>
            <textarea class="form-control" id="feedback_comment" name="feedback_comment" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Feedback</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

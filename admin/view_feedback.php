<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch all feedback
$sql = "SELECT Feedbacks.feedback_id, Feedbacks.ratings, Feedbacks.feedback_comment, Feedbacks.date, Parents.first_name, Parents.last_name 
        FROM Feedbacks 
        JOIN Parents ON Feedbacks.parent_id = Parents.parent_id";
$result = $conn->query($sql);

// Check if there is feedback
$feedbacks = [];
if ($result->num_rows > 0) {
    $feedbacks = $result->fetch_all(MYSQLI_ASSOC);
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Feedback</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container">
    <h2>View Feedback</h2>
    <?php if (!empty($feedbacks)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Feedback ID</th>
                    <th>Parent Name</th>
                    <th>Ratings</th>
                    <th>Feedback Comment</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedbacks as $feedback): ?>
                    <tr>
                        <td><?php echo $feedback['feedback_id']; ?></td>
                        <td><?php echo $feedback['first_name'] . ' ' . $feedback['last_name']; ?></td>
                        <td><?php echo $feedback['ratings']; ?></td>
                        <td><?php echo $feedback['feedback_comment']; ?></td>
                        <td><?php echo $feedback['date']; ?></td>
                        <td>
                            <form action="delete_feedback.php" method="post" style="display: inline-block;">
                                <input type="hidden" name="feedback_id" value="<?php echo $feedback['feedback_id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this feedback?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No feedback found.</p>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if feedback_id is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['feedback_id'])) {
    $feedback_id = $_POST['feedback_id'];

    // Delete the feedback
    $sql = "DELETE FROM Feedbacks WHERE feedback_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $feedback_id);
        if ($stmt->execute()) {
            // Redirect back to the view feedback page
            header("Location: view_feedback.php?message=Feedback+deleted+successfully");
            exit;
        } else {
            // Redirect back to the view feedback page with an error message
            header("Location: view_feedback.php?error=Error+deleting+feedback");
            exit;
        }
        $stmt->close();
    }
}

$conn->close();
?>

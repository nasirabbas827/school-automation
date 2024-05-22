<?php
session_start();
include('config.php');

// Check if the user is logged in as a teacher
if (!isset($_SESSION["teacher_id"])) {
    header("Location: teacher_login.php");
    exit;
}

$teacher_id = $_SESSION["teacher_id"];

// Check if assignment ID is provided in the URL
if (!isset($_GET['assignment_id'])) {
    header("Location: view_assignments.php");
    exit;
}

$assignment_id = $_GET['assignment_id'];

// Fetch the assignment details
$sql = "SELECT * FROM Assignments WHERE assignment_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $assignment = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $upload_date = date('Y-m-d');
    $due_date = $_POST['due_date'];

    // Update assignment details
    $sql = "UPDATE Assignments SET title = ?, description = ?, upload_date = ?, due_date = ? WHERE assignment_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssi", $title, $description, $upload_date, $due_date, $assignment_id);
        $stmt->execute();
        $stmt->close();
        // Redirect back to view assignments page
        header("Location: view_assignments.php");
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
    <title>Edit Assignment</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container">
    <h2>Edit Assignment</h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo $assignment['title']; ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $assignment['description']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="upload_file">Uploaded File:</label>
            <p><?php echo $assignment['upload_file']; ?></p>
            <input type="file" class="form-control-file" id="upload_file" name="upload_file">
        </div>
        <div class="form-group">
            <label for="due_date">Due Date:</label>
            <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo $assignment['due_date']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
session_start();
include('config.php');

// Check if the user is logged in as a teacher
if (!isset($_SESSION["teacher_id"])) {
    header("Location: teacher_login.php");
    exit;
}

$teacher_id = $_SESSION["teacher_id"];

// Fetch the assignments assigned to the teacher
$sql = "SELECT * FROM Assignments WHERE teacher_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $assignments = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Handle assignment deletion
if (isset($_POST['delete_assignment'])) {
    $assignment_id = $_POST['assignment_id'];
    $sql = "DELETE FROM Assignments WHERE assignment_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $assignment_id);
        $stmt->execute();
        $stmt->close();
        // Redirect to refresh the page
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
    <title>View Assignments</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container">
    <h2>View Assignments</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Upload Date</th>
                <th>Due Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($assignments as $assignment): ?>
                <tr>
                    <td><?php echo $assignment['title']; ?></td>
                    <td><?php echo $assignment['description']; ?></td>
                    <td><?php echo $assignment['upload_date']; ?></td>
                    <td><?php echo $assignment['due_date']; ?></td>
                    <td>
                        <a href="edit_assignment.php?assignment_id=<?php echo $assignment['assignment_id']; ?>" class="btn btn-primary">Edit</a>
                        <a href="view_submitted_assignments.php?assignment_id=<?php echo $assignment['assignment_id']; ?>" class="btn btn-success">View Submissions</a>
                        <form method="post" style="display: inline-block;">
                            <input type="hidden" name="assignment_id" value="<?php echo $assignment['assignment_id']; ?>">
                            <button type="submit" class="btn btn-danger" name="delete_assignment" onclick="return confirm('Are you sure you want to delete this assignment?')">Delete</button>
                        </form>
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

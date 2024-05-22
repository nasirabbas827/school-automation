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

$student_id = $_SESSION["student_id"];
$course_id = $_GET["course_id"];

// Fetch student details
$sql = "SELECT * FROM Students WHERE student_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
}

// Fetch course details
$sql = "SELECT * FROM Courses WHERE course_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
    $stmt->close();
}

// Fetch assignments for the course
$assignments = [];
$current_date = date('Y-m-d');
$sql = "SELECT a.*, sa.grade, sa.comments 
        FROM Assignments a 
        LEFT JOIN SubmittedAssignments sa ON a.assignment_id = sa.assignment_id AND sa.student_id = ?
        WHERE a.course_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $student_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        // Check if due date has not passed
        if ($row['due_date'] >= $current_date) {
            $row['upload_enabled'] = true;
        } else {
            $row['upload_enabled'] = false;
        }
        $assignments[] = $row;
    }
    $stmt->close();
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
    <h2>View Assignments for <?php echo $course['course_name']; ?></h2>
    <h4>Course Description: <?php echo $course['description']; ?></h4>
    <hr>
    <?php if (!empty($assignments)): ?>
        <h3>Assignments:</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Assignment ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Upload Date</th>
                        <th>Due Date</th>
                        <th>Download</th>
                        <th>Grade</th>
                        <th>Comments</th>
                        <th>Upload Assignment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignments as $assignment): ?>
                        <tr>
                            <td><?php echo $assignment['assignment_id']; ?></td>
                            <td><?php echo $assignment['title']; ?></td>
                            <td><?php echo $assignment['description']; ?></td>
                            <td><?php echo $assignment['upload_date']; ?></td>
                            <td><?php echo $assignment['due_date']; ?></td>
                            <td>
                                <?php if (!empty($assignment['upload_file'])): ?>
                                    <a href="../teacher/<?php echo $assignment['upload_file']; ?>" download>Download</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo $assignment['grade'] ?? 'N/A'; ?></td>
                            <td><?php echo $assignment['comments'] ?? 'N/A'; ?></td>
                            <td>
                                <?php if ($assignment['upload_enabled']): ?>
                                    <form action="upload_assignment.php" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <input type="file" class="form-control-file" name="file" required>
                                            <input type="hidden" name="assignment_id" value="<?php echo $assignment['assignment_id']; ?>">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge badge-danger">Due Date Passed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No assignments found for this course.</p>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

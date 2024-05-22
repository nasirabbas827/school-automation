<?php
session_start();
include('config.php');

// Check if the user is logged in as a teacher
if (!isset($_SESSION["teacher_id"])) {
    header("Location: teacher_login.php");
    exit;
}

// Fetch the assigned class details
$teacher_id = $_SESSION["teacher_id"];
$sql = "SELECT * FROM Classes WHERE teacher_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $classes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Fetch all courses assigned to the teacher's classes
$courses = [];
if (!empty($classes)) {
    $class_ids = array_column($classes, 'class_id');
    $class_ids_str = implode(',', $class_ids);
    $sql = "SELECT * FROM Courses WHERE class_id IN ($class_ids_str)";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
    }
}

// Handle course deletion
if (isset($_POST['delete_course'])) {
    $course_id = $_POST['course_id'];
    $sql = "DELETE FROM Courses WHERE course_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $stmt->close();
        // Redirect to refresh the page
        header("Location: view_courses.php");
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
    <title>View Courses</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container">
    <h2>View Courses</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Description</th>
                <th>Class</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?php echo $course['course_name']; ?></td>
                    <td><?php echo $course['description']; ?></td>
                    <td><?php echo $classes[array_search($course['class_id'], array_column($classes, 'class_id'))]['class_name'] . " - " . $classes[array_search($course['class_id'], array_column($classes, 'class_id'))]['section']; ?></td>
                    <td>
                        <a href="edit_course.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-primary">Edit</a>
                        <form method="post" style="display: inline-block;">
                            <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                            <button type="submit" class="btn btn-danger" name="delete_course" onclick="return confirm('Are you sure you want to delete this course?')">Delete</button>
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

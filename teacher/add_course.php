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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];
    $class_id = $_POST['class_id'];

    // Insert new course
    $sql = "INSERT INTO Courses (course_name, description, class_id) VALUES (?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssi", $course_name, $description, $class_id);
        $stmt->execute();
        $stmt->close();
        // Redirect back to view courses page
        header("Location: view_courses.php");
        exit;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Course</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container">
    <h2>Add Course</h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="course_name">Course Name:</label>
            <input type="text" class="form-control" id="course_name" name="course_name" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="class_id">Select Class:</label>
            <select class="form-control" id="class_id" name="class_id" required>
                <?php foreach ($classes as $class): ?>
                    <option value="<?php echo $class['class_id']; ?>"><?php echo $class['class_name'] . " - " . $class['section']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Course</button>
        <a class="btn btn-outline-success" href="view_courses.php">View Courses</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

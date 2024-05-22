<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Get class details
if (isset($_GET['id'])) {
    $class_id = $_GET['id'];
    $sql = "SELECT * FROM Classes WHERE class_id = $class_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $class = $result->fetch_assoc();
    } else {
        echo "No class found with this ID.";
        exit;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_id = $_POST["class_id"];
    $class_name = $_POST["class_name"];
    $section = $_POST["section"];
    $teacher_id = $_POST["teacher_id"];

    $sql = "UPDATE Classes SET class_name='$class_name', section='$section', teacher_id='$teacher_id' WHERE class_id=$class_id";

    if ($conn->query($sql) === TRUE) {
        echo "Class updated successfully";
        header("Location: view_classes.php");
        exit;
    } else {
        echo "Error updating class: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Class</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container">
    <h2>Edit Class</h2>
    <form action="edit_class.php" method="post">
        <input type="hidden" name="class_id" value="<?php echo $class['class_id']; ?>">
        <div class="form-group">
            <label for="class_name">Class Name:</label>
            <input type="text" class="form-control" id="class_name" name="class_name" value="<?php echo $class['class_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="section">Section:</label>
            <input type="text" class="form-control" id="section" name="section" value="<?php echo $class['section']; ?>" required>
        </div>
        <div class="form-group">
            <label for="teacher_id">Teacher:</label>
            <select class="form-control" id="teacher_id" name="teacher_id" required>
                <?php
                $sql = "SELECT teacher_id, CONCAT(first_name, ' ', last_name) AS name FROM Teachers";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $selected = ($row['teacher_id'] == $class['teacher_id']) ? "selected" : "";
                        echo "<option value='" . $row['teacher_id'] . "' $selected>" . $row['name'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No teachers available</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Class</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = $_POST["class_name"];
    $section = $_POST["section"];
    $teacher_id = $_POST["teacher_id"];

    $sql = "INSERT INTO Classes (class_name, section, teacher_id) VALUES ('$class_name', '$section', '$teacher_id')";

    if ($conn->query($sql) === TRUE) {
        echo "New class added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Class</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container">
    <h2>Add Class</h2>
    <form action="add_class.php" method="post">
        <div class="form-group">
            <label for="class_name">Class Name:</label>
            <input type="text" class="form-control" id="class_name" name="class_name" required>
        </div>
        <div class="form-group">
            <label for="section">Section:</label>
            <input type="text" class="form-control" id="section" name="section" required>
        </div>
        <div class="form-group">
            <label for="teacher_id">Teacher:</label>
            <select class="form-control" id="teacher_id" name="teacher_id" required>
                <?php
                $sql = "SELECT teacher_id, CONCAT(first_name, ' ', last_name) AS name FROM Teachers";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['teacher_id'] . "'>" . $row['name'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No teachers available</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Class</button>
        <a class="btn btn-outline-dark" href="view_classes.php"> View Classes</a>

    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

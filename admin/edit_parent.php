<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Get parent ID from URL
if (isset($_GET['edit_id'])) {
    $parent_id = $_GET['edit_id'];
    
    // Fetch parent details
    $sql = "SELECT * FROM Parents WHERE parent_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $parent = $result->fetch_assoc();
        $stmt->close();
    }
    
    // Fetch parent-child relationships
    $sql = "SELECT child_id FROM Parent_Child WHERE parent_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $child_ids = [];
        while ($row = $result->fetch_assoc()) {
            $child_ids[] = $row['child_id'];
        }
        $stmt->close();
    }
}

// Fetch all students
$students = $conn->query("SELECT student_id, first_name, last_name FROM Students");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parent_id = $_POST['parent_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Note: Password should be hashed in a real application
    $child_ids = $_POST['child_ids'];

    // Update parent details in Parents table
    $sql = "UPDATE Parents SET first_name=?, last_name=?, phone=?, email=?, password=? WHERE parent_id=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssi", $first_name, $last_name, $phone, $email, $password, $parent_id);
        $stmt->execute();
        $stmt->close();
    }

    // Update parent-child relationships
    $sql = "DELETE FROM Parent_Child WHERE parent_id=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();
        $stmt->close();
    }
    
    foreach ($child_ids as $child_id) {
        $sql = "INSERT INTO Parent_Child (parent_id, child_id) VALUES (?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ii", $parent_id, $child_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    header("Location: view_parents.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Parent</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php
include('admin_navbar.php');
?>

<div class="container">
    <h2>Edit Parent</h2>
    <form action="" method="post">
        <input type="hidden" name="parent_id" value="<?php echo $parent['parent_id']; ?>">
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $parent['first_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $parent['last_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $parent['phone']; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $parent['email']; ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" value="<?php echo $parent['password']; ?>" required>
        </div>
        <div class="form-group">
            <label for="child_ids">Select Children:</label>
            <select multiple class='form-control' id='child_ids' name='child_ids[]' required>
                <?php
                while ($student = $students->fetch_assoc()) {
                    $selected = in_array($student['student_id'], $child_ids) ? 'selected' : '';
                    echo "<option value='" . $student['student_id'] . "' $selected>" . $student['first_name'] . " " . $student['last_name'] . "</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Parent</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>

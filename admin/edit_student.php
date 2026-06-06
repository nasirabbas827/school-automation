<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Get student details
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];
    $sql = "SELECT * FROM Students WHERE student_id = $student_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        echo "No student found with this ID.";
        exit;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST["student_id"];
    $profile_pic = $_FILES["profile_pic"]["name"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];
    $address = $_POST["address"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $class_id = $_POST["class_id"];

    // Handle profile picture upload
    if (!empty($profile_pic)) {
        $target_dir = "student_uploads/";
        $target_file = $target_dir . basename($profile_pic);
        move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);
        $sql = "UPDATE Students SET profile_pic='$profile_pic', first_name='$first_name', last_name='$last_name', dob='$dob', gender='$gender', address='$address', phone='$phone', email='$email', password="YOUR_OWN_API_KEY", class_id='$class_id' WHERE student_id=$student_id";
    } else {
        if (!empty($password)) {
            $sql = "UPDATE Students SET first_name='$first_name', last_name='$last_name', dob='$dob', gender='$gender', address='$address', phone='$phone', email='$email', password="YOUR_OWN_API_KEY", class_id='$class_id' WHERE student_id=$student_id";
        } else {
            $sql = "UPDATE Students SET first_name='$first_name', last_name='$last_name', dob='$dob', gender='$gender', address='$address', phone='$phone', email='$email', class_id='$class_id' WHERE student_id=$student_id";
        }
    }

    if ($conn->query($sql) === TRUE) {
        echo "Student updated successfully";
        header("Location: view_students.php");
        exit;
    } else {
        echo "Error updating student: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mb-5">
    <h2>Edit Student</h2>
    <form action="edit_student.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
        <div class="form-group">
            <label for="profile_pic">Profile Picture:</label>
            <input type="file" class="form-control" id="profile_pic" name="profile_pic">
        </div>
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $student['first_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $student['last_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth:</label>
            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $student['dob']; ?>" required>
        </div>
        <div class="form-group">
            <label for="gender">Gender:</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="Male" <?php if($student['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if($student['gender'] == 'Female') echo 'selected'; ?>>Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" class="form-control" id="address" name="address" value="<?php echo $student['address']; ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $student['phone']; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $student['email']; ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password (leave blank if you want the same password):</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="class_id">Class:</label>
            <select class="form-control" id="class_id" name="class_id" required>
                <?php
                $sql = "SELECT class_id, class_name, section FROM Classes";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $selected = ($student['class_id'] == $row['class_id']) ? 'selected' : '';
                        echo "<option value='" . $row['class_id'] . "' $selected>" . $row['class_name'] . " - " . $row['section'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No classes available</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Student</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

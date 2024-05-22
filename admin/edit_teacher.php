<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Get teacher details
if (isset($_GET['id'])) {
    $teacher_id = $_GET['id'];
    $sql = "SELECT * FROM Teachers WHERE teacher_id = $teacher_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $teacher = $result->fetch_assoc();
    } else {
        echo "No teacher found with this ID.";
        exit;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_id = $_POST["teacher_id"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];
    $address = $_POST["address"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Check if a new profile pic is uploaded
    if ($_FILES["profile_pic"]["name"]) {
        $profile_pic = $_FILES["profile_pic"]["name"];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($profile_pic);
        move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);
        $sql = "UPDATE Teachers SET profile_pic='$profile_pic', first_name='$first_name', last_name='$last_name', dob='$dob', gender='$gender', address='$address', phone='$phone', email='$email' WHERE teacher_id=$teacher_id";
    } else {
        $sql = "UPDATE Teachers SET first_name='$first_name', last_name='$last_name', dob='$dob', gender='$gender', address='$address', phone='$phone', email='$email' WHERE teacher_id=$teacher_id";
    }

    if (!empty($password)) {
        $sql = "UPDATE Teachers SET password='$password' WHERE teacher_id=$teacher_id";
    }

    if ($conn->query($sql) === TRUE) {
        echo "Teacher updated successfully";
        header("Location: view_teachers.php");
        exit;
        
    } else {
        echo "Error updating teacher: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Teacher</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mb-5">
    <h2>Edit Teacher</h2>
    <form action="edit_teacher.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="teacher_id" value="<?php echo $teacher['teacher_id']; ?>">
        <div class="form-group">
            <label for="profile_pic">Profile Picture:</label>
            <input type="file" class="form-control" id="profile_pic" name="profile_pic">
            <img src="uploads/<?php echo $teacher['profile_pic']; ?>" alt="Profile Pic" width="50" height="50">
        </div>
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $teacher['first_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $teacher['last_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth:</label>
            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $teacher['dob']; ?>" required>
        </div>
        <div class="form-group">
            <label for="gender">Gender:</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="Male" <?php if ($teacher['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($teacher['gender'] == 'Female') echo 'selected'; ?>>Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" class="form-control" id="address" name="address" value="<?php echo $teacher['address']; ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $teacher['phone']; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $teacher['email']; ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password">
            <small class="form-text text-muted">Leave blank if you want the same password</small>
        </div>
        <button type="submit" class="btn btn-primary">Update Teacher</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

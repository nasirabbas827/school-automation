<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Get staff ID from URL
if (isset($_GET['edit_id'])) {
    $staff_id = $_GET['edit_id'];
    
    // Fetch staff details
    $sql = "SELECT * FROM Staff WHERE staff_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $staff = $result->fetch_assoc();
        $stmt->close();
    }
}

// Update staff details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_id = $_POST['staff_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $position = $_POST['position'];
    $password = $_POST['password'];

    // If password is left blank, don't update it
    if (empty($password)) {
        $sql = "UPDATE Staff SET first_name=?, last_name=?, dob=?, gender=?, address=?, phone=?, email=?, position=? WHERE staff_id=?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssssssi", $first_name, $last_name, $dob, $gender, $address, $phone, $email, $position, $staff_id);
            $stmt->execute();
            $stmt->close();
            header("Location: view_staff.php");
            exit;
        }
    } else {
        $sql = "UPDATE Staff SET first_name=?, last_name=?, dob=?, gender=?, address=?, phone=?, email=?, password=?, position=? WHERE staff_id=?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssssssi", $first_name, $last_name, $dob, $gender, $address, $phone, $email, $password, $position, $staff_id);
            $stmt->execute();
            $stmt->close();
            header("Location: view_staff.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Staff</title>
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
    <h2>Edit Staff</h2>
    <form action="" method="post">
        <input type="hidden" name="staff_id" value="<?php echo $staff['staff_id']; ?>">
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $staff['first_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $staff['last_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth:</label>
            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $staff['dob']; ?>" required>
        </div>
        <div class="form-group">
            <label for="gender">Gender:</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="Male" <?php if($staff['gender'] == "Male") echo "selected"; ?>>Male</option>
                <option value="Female" <?php if($staff['gender'] == "Female") echo "selected"; ?>>Female</option>
                <option value="Other" <?php if($staff['gender'] == "Other") echo "selected"; ?>>Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" class="form-control" id="address" name="address" value="<?php echo $staff['address']; ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $staff['phone']; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $staff['email']; ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep the same password">
        </div>
        <div class="form-group">
            <label for="position">Position:</label>
            <input type="text" class="form-control" id="position" name="position" value="<?php echo $staff['position']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Staff</button>
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

<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch teachers and staff for selection
$teachers = $conn->query("SELECT teacher_id, first_name, last_name FROM Teachers");
$staff = $conn->query("SELECT staff_id, first_name, last_name FROM Staff");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_type = $_POST['user_type'];
    $user_id = $_POST['user_id'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    if ($user_type == 'teacher') {
        $sql = "INSERT INTO Salaries (teacher_id, staff_id, amount, date, status) VALUES (?, NULL, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("idss", $user_id, $amount, $date, $status);
    } else {
        $sql = "INSERT INTO Salaries (teacher_id, staff_id, amount, date, status) VALUES (NULL, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("idss", $user_id, $amount, $date, $status);
    }

    if ($stmt->execute()) {
        echo "Salary added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Salary</title>
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
    <h2>Add Salary</h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="user_type">User Type:</label>
            <select class="form-control" id="user_type" name="user_type" required>
                <option value="teacher">Teacher</option>
                <option value="staff">Staff</option>
            </select>
        </div>
        <div class="form-group">
            <label for="user_id">Select User:</label>
            <select class="form-control" id="user_id" name="user_id" required>
                <option value="">Select User</option>
                <?php
                while ($teacher = $teachers->fetch_assoc()) {
                    echo "<option class='teacher' value='" . $teacher['teacher_id'] . "'>" . $teacher['first_name'] . " " . $teacher['last_name'] . " (Teacher)</option>";
                }
                while ($staff_member = $staff->fetch_assoc()) {
                    echo "<option class='staff' value='" . $staff_member['staff_id'] . "'>" . $staff_member['first_name'] . " " . $staff_member['last_name'] . " (Staff)</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" class="form-control" id="date" name="date" required>
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status" required>
                <option value="Paid">Paid</option>
                <option value="Unpaid">Unpaid</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Salary</button>
        <a class="btn btn-outline-dark" href="view_salaries.php"> View Salaries</a>

    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function(){
        $('#user_type').change(function(){
            var userType = $(this).val();
            $('#user_id option').hide();
            $('#user_id option.' + userType).show();
        }).change();
    });
</script>
</body>
</html>

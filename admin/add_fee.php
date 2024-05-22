<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Define an empty array to store error messages
$errors = array();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input data
    $student_id = $_POST["student_id"];
    $amount = $_POST["amount"];
    $due_date = $_POST["due_date"];
    $status = $_POST["status"];

    // Check if all fields are filled
    if (empty($student_id) || empty($amount) || empty($due_date) || empty($status)) {
        $errors[] = "All fields are required";
    } else {
        // Insert the fee voucher into the database
        $sql = "INSERT INTO Fees (student_id, amount, due_date, status) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("iiss", $student_id, $amount, $due_date, $status);
            if ($stmt->execute()) {
                $stmt->close();
                // Redirect to admin dashboard after successful insertion
                header("Location: view_fee.php");
                exit;
            } else {
                $errors[] = "Error occurred while inserting fee voucher";
            }
        } else {
            $errors[] = "Error occurred while preparing statement";
        }
    }
}

// Fetch all students for dropdown list
$sql = "SELECT student_id, first_name, last_name FROM Students";
$result = $conn->query($sql);
$students = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>Add Fee Voucher</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" role="alert">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="student_id">Student:</label>
            <select class="form-control" id="student_id" name="student_id">
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo $student['student_id']; ?>"><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="text" class="form-control" id="amount" name="amount">
        </div>
        <div class="form-group">
            <label for="due_date">Due Date:</label>
            <input type="date" class="form-control" id="due_date" name="due_date">
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status">
                <option value="Paid">Paid</option>
                <option value="Unpaid">Unpaid</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
        <a class="btn btn-outline-dark" href="view_fee.php">View Fee</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

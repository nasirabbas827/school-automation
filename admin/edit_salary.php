<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Get salary ID from URL
if (isset($_GET['edit_id'])) {
    $salary_id = $_GET['edit_id'];
    
    // Fetch salary details
    $sql = "SELECT * FROM Salaries WHERE salary_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $salary_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $salary = $result->fetch_assoc();
        $stmt->close();
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $salary_id = $_POST['salary_id'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    $sql = "UPDATE Salaries SET amount=?, date=?, status=? WHERE salary_id=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssi", $amount, $date, $status, $salary_id);
        $stmt->execute();
        $stmt->close();
        header("Location: view_salaries.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Salary</title>
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
    <h2>Edit Salary</h2>
    <form action="" method="post">
        <input type="hidden" name="salary_id" value="<?php echo $salary['salary_id']; ?>">
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?php echo $salary['amount']; ?>" required>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" class="form-control" id="date" name="date" value="<?php echo $salary['date']; ?>" required>
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status" required>
                <option value="Paid" <?php if($salary['status'] == "Paid") echo "selected"; ?>>Paid</option>
                <option value="Unpaid" <?php if($salary['status'] == "Unpaid") echo "selected"; ?>>Unpaid</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Salary</button>
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

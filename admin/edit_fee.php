<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if fee_id is provided
if (!isset($_GET["fee_id"])) {
    header("Location: view_fee.php"); // Redirect to view_fee.php if fee_id is not provided
    exit;
}

$fee_id = $_GET["fee_id"];

// Fetch the fee voucher details
$sql = "SELECT * FROM Fees WHERE fee_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $fee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $fee = $result->fetch_assoc();
    $stmt->close();
}

// Update fee voucher details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST['status'];
    $paid_date = $_POST['paid_date'];

    $sql = "UPDATE Fees SET status = ?, paid_date = ? WHERE fee_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssi", $status, $paid_date, $fee_id);
        $stmt->execute();
        $stmt->close();
        header("Location: view_fee.php");
        exit;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Fee Voucher</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container">
    <h2>Edit Fee Voucher</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status" required>
                <option value="Paid" <?php if ($fee['status'] == "Paid") echo "selected"; ?>>Paid</option>
                <option value="Unpaid" <?php if ($fee['status'] == "Unpaid") echo "selected"; ?>>Unpaid</option>
            </select>
        </div>
        <div class="form-group">
            <label for="paid_date">Paid Date:</label>
            <input type="date" class="form-control" id="paid_date" name="paid_date" value="<?php echo $fee['paid_date']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

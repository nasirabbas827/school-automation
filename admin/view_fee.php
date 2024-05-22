<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Function to format date
function formatDate($date) {
    return date("Y-m-d", strtotime($date));
}

// Fetch all fee vouchers with student names
$sql = "SELECT Fees.*, Students.first_name, Students.last_name FROM Fees INNER JOIN Students ON Fees.student_id = Students.student_id";
$result = $conn->query($sql);

// Check if there are fee vouchers
if ($result->num_rows > 0) {
    $fees = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $fees = [];
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Fee Vouchers</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container">
    <h2>View Fee Vouchers</h2>
    <?php if (!empty($fees)): ?>
        <table id="feeTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Fee ID</th>
                    <th>Student Name</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Paid Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fees as $fee): ?>
                    <tr>
                        <td><?php echo $fee['fee_id']; ?></td>
                        <td><?php echo $fee['first_name']; ?></td>
                        <td><?php echo $fee['amount']; ?></td>
                        <td><?php echo formatDate($fee['due_date']); ?></td>
                        <td><?php echo formatDate($fee['paid_date']); ?></td>
                        <td><?php echo $fee['status']; ?></td>
                        <td>
                            <a href="edit_fee.php?fee_id=<?php echo $fee['fee_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <form action="delete_fee.php" method="post" style="display: inline-block;">
                                <input type="hidden" name="fee_id" value="<?php echo $fee['fee_id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this fee voucher?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button id="exportBtn" class="btn btn-success">Export as Excel</button>
    <?php else: ?>
        <p>No fee vouchers found.</p>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>
<script>
    document.getElementById('exportBtn').addEventListener('click', function() {
        // Create a new workbook
        var wb = XLSX.utils.book_new();
        
        // Convert fee data to worksheet
        var ws = XLSX.utils.json_to_sheet(<?php echo json_encode($fees); ?>);

        // Add worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, 'Fee Vouchers');

        // Save workbook as Excel file
        XLSX.writeFile(wb, 'fee_vouchers.xlsx');
    });
</script>
</body>
</html>

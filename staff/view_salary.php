<?php
session_start();
include('config.php');

// Check if the user is logged in as staff
if (!isset($_SESSION["staff_id"])) {
    header("Location: staff_login.php");
    exit;
}

$staff_id = $_SESSION["staff_id"];

// Fetch the staff's salary information
$sql = "SELECT * FROM Salaries WHERE staff_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $salaries = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Staff Salaries</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container">
    <h2>Staff Salary Details</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Salary ID</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
                <th>Print</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($salaries as $salary): ?>
                <tr>
                    <td><?php echo $salary['salary_id']; ?></td>
                    <td><?php echo $salary['amount']; ?></td>
                    <td><?php echo $salary['date']; ?></td>
                    <td><?php echo $salary['status']; ?></td>
                    <td>
                        <form action="print_salary.php" method="post" target="_blank">
                            <input type="hidden" name="salary_id" value="<?php echo $salary['salary_id']; ?>">
                            <button type="submit" class="btn btn-primary">Print</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>

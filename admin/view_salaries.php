<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Delete salary record
if (isset($_GET['delete_id'])) {
    $salary_id = $_GET['delete_id'];
    $sql = "DELETE FROM Salaries WHERE salary_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $salary_id);
        $stmt->execute();
        $stmt->close();
        echo "Salary record deleted successfully.";
    } else {
        echo "Error: Could not prepare query: $sql. " . $conn->error;
    }
}

// Fetch all salaries
$sql = "SELECT s.salary_id, t.first_name AS teacher_first_name, t.last_name AS teacher_last_name, 
               st.first_name AS staff_first_name, st.last_name AS staff_last_name, s.amount, s.date, s.status 
        FROM Salaries s
        LEFT JOIN Teachers t ON s.teacher_id = t.teacher_id
        LEFT JOIN Staff st ON s.staff_id = st.staff_id";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Salaries</title>
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
    <h2>Salaries List</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User Type</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $user_type = $row['teacher_first_name'] ? 'Teacher' : 'Staff';
                    $first_name = $row['teacher_first_name'] ? $row['teacher_first_name'] : $row['staff_first_name'];
                    $last_name = $row['teacher_last_name'] ? $row['teacher_last_name'] : $row['staff_last_name'];
                    
                    echo "<tr>";
                    echo "<td>" . $user_type . "</td>";
                    echo "<td>" . $first_name . "</td>";
                    echo "<td>" . $last_name . "</td>";
                    echo "<td>" . $row['amount'] . "</td>";
                    echo "<td>" . $row['date'] . "</td>";
                    echo "<td>" . $row['status'] . "</td>";
                    echo "<td>";
                    echo "<a href='edit_salary.php?edit_id=" . $row['salary_id'] . "' class='btn btn-primary btn-sm'>Edit</a> ";
                    echo "<a href='view_salaries.php?delete_id=" . $row['salary_id'] . "' class='btn btn-danger btn-sm'>Delete</a> ";
                    echo "<a href='print_salary.php?print_id=" . $row['salary_id'] . "' class='btn btn-secondary btn-sm'>Print</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No salary records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>

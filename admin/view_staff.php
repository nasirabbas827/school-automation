<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Delete staff member
if (isset($_GET['delete_id'])) {
    $staff_id = $_GET['delete_id'];
    $sql = "DELETE FROM Staff WHERE staff_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $staff_id);
        $stmt->execute();
        $stmt->close();
        echo "Staff member deleted successfully.";
    } else {
        echo "Error: Could not prepare query: $sql. " . $conn->error;
    }
}

// Fetch all staff members
$sql = "SELECT * FROM Staff";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Staff</title>
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
    <h2>Staff List</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Profile Pic</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Password</th>
                <th>Position</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><img src='" . $row['profile_pic'] . "' width='50'></td>";
                    echo "<td>" . $row['first_name'] . "</td>";
                    echo "<td>" . $row['last_name'] . "</td>";
                    echo "<td>" . $row['dob'] . "</td>";
                    echo "<td>" . $row['gender'] . "</td>";
                    echo "<td>" . $row['address'] . "</td>";
                    echo "<td>" . $row['phone'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['password'] . "</td>";
                    echo "<td>" . $row['position'] . "</td>";
                    echo "<td>";
                    echo "<a href='edit_staff.php?edit_id=" . $row['staff_id'] . "' class='mb-2 btn btn-primary btn-sm'>Edit</a> ";
                    echo "<a href='view_staff.php?delete_id=" . $row['staff_id'] . "' class='btn btn-danger btn-sm'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='11'>No staff members found.</td></tr>";
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

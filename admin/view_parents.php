<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Delete parent record
if (isset($_GET['delete_id'])) {
    $parent_id = $_GET['delete_id'];
    
    // First, delete from Parent_Child table to maintain referential integrity
    $sql = "DELETE FROM Parent_Child WHERE parent_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();
        $stmt->close();
    }
    
    // Then, delete from Parents table
    $sql = "DELETE FROM Parents WHERE parent_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();
        $stmt->close();
        echo "Parent record deleted successfully.";
    } else {
        echo "Error: Could not prepare query: $sql. " . $conn->error;
    }
}

// Fetch all parents with their children
$sql = "SELECT p.parent_id, p.first_name, p.last_name, p.phone, p.email, GROUP_CONCAT(CONCAT(s.first_name, ' ', s.last_name) SEPARATOR ', ') AS children
        FROM Parents p
        LEFT JOIN Parent_Child pc ON p.parent_id = pc.parent_id
        LEFT JOIN Students s ON pc.child_id = s.student_id
        GROUP BY p.parent_id";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Parents</title>
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
    <h2>Parents List</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Children</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['first_name'] . "</td>";
                    echo "<td>" . $row['last_name'] . "</td>";
                    echo "<td>" . $row['phone'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['children'] . "</td>";
                    echo "<td>";
                    echo "<a href='edit_parent.php?edit_id=" . $row['parent_id'] . "' class='btn btn-primary btn-sm'>Edit</a> ";
                    echo "<a href='view_parents.php?delete_id=" . $row['parent_id'] . "' class='btn btn-danger btn-sm'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No parent records found.</td></tr>";
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

<?php
session_start();

// Check if the user is logged in as staff
if (!isset($_SESSION["staff_id"])) {
    header("Location: staff_login.php");
    exit;
}

// Include the database configuration
include('config.php');

// Fetch staff details
$staff_id = $_SESSION["staff_id"];
$sql = "SELECT * FROM Staff WHERE staff_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $staff = $result->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container">
    <h2>Welcome, <?php echo $staff['first_name']; ?> <?php echo $staff['last_name']; ?></h2>
    <p>Email: <?php echo $staff['email']; ?></p>
    <p>Position: <?php echo $staff['position']; ?></p>
    <!-- Display other staff details as needed -->
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
session_start();
include('config.php');

// Check if the user is logged in as a parent
if (!isset($_SESSION["parent_id"])) {
    header("Location: parent_login.php");
    exit;
}

// Fetch parent details
$parent_id = $_SESSION["parent_id"];
$sql = "SELECT * FROM Parents WHERE parent_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $parent_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $parent = $result->fetch_assoc();
    $stmt->close();
}

// Fetch child's details
$children = [];
$sql = "SELECT s.* FROM Students s 
        JOIN Parent_Child pc ON s.student_id = pc.child_id 
        WHERE pc.parent_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $parent_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $children[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container">
    <h2>Welcome, <?php echo $parent['first_name'] . ' ' . $parent['last_name']; ?></h2>
    <hr>
    <h4>Your Information:</h4>
    <p><strong>Phone:</strong> <?php echo $parent['phone']; ?></p>
    <p><strong>Email:</strong> <?php echo $parent['email']; ?></p>
    <hr>
    <h4>Your Children:</h4>
    <div class="row">
        <?php foreach ($children as $child): ?>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $child['first_name'] . ' ' . $child['last_name']; ?></h5>
                    <p class="card-text"><strong>Date of Birth:</strong> <?php echo $child['dob']; ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

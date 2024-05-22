<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Function to get total count from the database
function getTotalCount($conn, $table) {
    $sql = "SELECT COUNT(*) AS total FROM $table";
    $result = $conn->query($sql);
    return ($result->num_rows > 0) ? $result->fetch_assoc()['total'] : 0;
}

// Get total counts
$totalTeachers = getTotalCount($conn, 'Teachers');
$totalClasses = getTotalCount($conn, 'Classes');
$totalCourses = getTotalCount($conn, 'Courses');
$totalParents = getTotalCount($conn, 'Parents');
$totalStudents = getTotalCount($conn, 'Students');
$totalStaff = getTotalCount($conn, 'Staff');
$totalSalaries = getTotalCount($conn, 'Salaries');
$totalFees = getTotalCount($conn, 'Fees');

// Get total students of each class with class name
$totalStudentsPerClass = [];
$sql = "SELECT c.class_name, COUNT(s.student_id) AS total_students FROM Classes c LEFT JOIN Students s ON c.class_id = s.class_id GROUP BY c.class_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $totalStudentsPerClass[$row['class_name']] = $row['total_students'];
    }
}

// Close the connection
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

<div class="container mb-5">
    <h2>Admin Dashboard</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Teachers</h5>
                    <p class="card-text"><?php echo $totalTeachers; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Classes</h5>
                    <p class="card-text"><?php echo $totalClasses; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Courses</h5>
                    <p class="card-text"><?php echo $totalCourses; ?></p>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Parents</h5>
                    <p class="card-text"><?php echo $totalParents; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Students</h5>
                    <p class="card-text"><?php echo $totalStudents; ?></p>
                    <ul class="list-group">
                        <?php foreach ($totalStudentsPerClass as $class => $total): ?>
                            <li class="list-group-item"><?php echo "$class: $total"; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Staff</h5>
                    <p class="card-text"><?php echo $totalStaff; ?></p>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Salaries</h5>
                    <p class="card-text"><?php echo $totalSalaries; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Fees</h5>
                    <p class="card-text"><?php echo $totalFees; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

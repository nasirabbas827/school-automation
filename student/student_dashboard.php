<?php
session_start();
include('config.php');

// Check if the user is logged in as a student
if (!isset($_SESSION["student_id"])) {
    header("Location: student_login.php");
    exit;
}

// Fetch student details
$student_id = $_SESSION["student_id"];
$sql = "SELECT * FROM Students WHERE student_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
}

// Fetch courses for the student's class
$courses = [];
$sql = "SELECT * FROM Courses WHERE class_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $student['class_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    $stmt->close();
}

// Fetch teacher details for the student's class
$teacher = [];
$sql = "SELECT Teachers.* FROM Teachers 
        INNER JOIN Classes ON Teachers.teacher_id = Classes.teacher_id 
        WHERE Classes.class_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $student['class_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>



<div class="container">
<h2>Welcome, <?php echo $student['first_name']; ?></h2>
    <hr>
<h4>Your Teacher:</h4>
    <div class="card">
        <div class="row no-gutters">
            <div class="col-md-4">
                <img src="../admin/uploads/<?php echo $teacher['profile_pic']; ?>" class="card-img" alt="Teacher Profile Picture">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?></h5>
                    <p class="card-text"><strong>Date of Birth:</strong> <?php echo $teacher['dob']; ?></p>
                    <p class="card-text"><strong>Gender:</strong> <?php echo $teacher['gender']; ?></p>
                    <p class="card-text"><strong>Address:</strong> <?php echo $teacher['address']; ?></p>
                    <p class="card-text"><strong>Phone:</strong> <?php echo $teacher['phone']; ?></p>
                    <p class="card-text"><strong>Email:</strong> <?php echo $teacher['email']; ?></p>
                </div>
            </div>
        </div>
    </div>
    </div>



<div class="container">
    
    <h4>Your Courses:</h4>
    <div class="row">
        <?php foreach ($courses as $course): ?>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $course['course_name']; ?></h5>
                    <p class="card-text"><?php echo $course['description']; ?></p>
                    <a href="view_assignments.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-primary">View Assignments</a>
                    <a href="view_quizzes.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-primary">View Quizzes</a>
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

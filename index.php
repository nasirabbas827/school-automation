<?php
session_start();
include('config.php');

// Fetch courses
$courses = [];
$sql_courses = "SELECT * FROM Courses";
$result_courses = $conn->query($sql_courses);
if ($result_courses->num_rows > 0) {
    $courses = $result_courses->fetch_all(MYSQLI_ASSOC);
}

// Fetch teachers
$teachers = [];
$sql_teachers = "SELECT * FROM Teachers";
$result_teachers = $conn->query($sql_teachers);
if ($result_teachers->num_rows > 0) {
    $teachers = $result_teachers->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title>School Automation System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .jumbotron {
            height: 500px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./images/hotel.jpg');
            background-size: cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .jumbotron h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .jumbotron p {
            font-size: 1.5rem;
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="jumbotron text-center">
    <h1>Welcome to School Automation System</h1>
    <p>Enhancing Education through Technology</p>
    <a href="student_login.php" class="btn btn-primary btn-lg">Login to Explore</a>
</div>

<div class="container mt-5">
    <h2 class="text-center">Our Courses</h2>
    <div class="row">
        <?php foreach ($courses as $course): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="./images/course.jpg" class="card-img-top" alt="Course Image">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $course['course_name']; ?></h5>
                    <p class="card-text"><?php echo $course['description']; ?></p>
                    <a href="student_login.php" class="btn btn-primary">Enroll Now</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="container mt-5">
    <h2 class="text-center">Our Teachers</h2>
    <div class="row">
        <?php foreach ($teachers as $teacher): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="./admin/uploads/<?php echo $teacher['profile_pic']; ?>" class="card-img-top" alt="Teacher Image">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?></h5>
                    <p class="card-text">Email: <?php echo $teacher['email']; ?></p>
                    <p class="card-text">Phone: <?php echo $teacher['phone']; ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="container mt-5">
    <h2 class="text-center">Contact Us</h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="name">Your Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Your Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="message">Your Message:</label>
            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>

<footer class="mt-5 py-3 bg-light">
    <div class="container text-center">
        <p>&copy; 2024 School Automation System. All rights reserved.</p>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

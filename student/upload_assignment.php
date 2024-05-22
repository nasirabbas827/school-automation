<?php
session_start();
include('config.php');

// Check if the user is logged in as a student
if (!isset($_SESSION["student_id"])) {
    header("Location: student_login.php");
    exit;
}

// Check if assignment_id and file are provided
if (!isset($_POST["assignment_id"]) || !isset($_FILES["file"])) {
    header("Location: student_dashboard.php");
    exit;
}

$student_id = $_SESSION["student_id"];
$assignment_id = $_POST["assignment_id"];
$file = $_FILES["file"];

// Fetch student details
$sql = "SELECT * FROM Students WHERE student_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
}

// Fetch assignment details
$sql = "SELECT * FROM Assignments WHERE assignment_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $assignment = $result->fetch_assoc();
    $stmt->close();
}

// Check if the assignment upload is enabled
$current_date = date('Y-m-d');
if ($assignment['due_date'] >= $current_date) {
    // Upload file
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_type = $file['type'];

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($file_name);
    $uploadOk = 1;

    // Check file size
    if ($file["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow only specific file formats
    $allowed_types = array('pdf', 'doc', 'docx');
    $file_ext = pathinfo($target_file, PATHINFO_EXTENSION);
    if (!in_array($file_ext, $allowed_types)) {
        echo "Sorry, only PDF, DOC, and DOCX files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($file_tmp, $target_file)) {
            // File uploaded successfully, now insert into database
            $sql = "INSERT INTO SubmittedAssignments (assignment_id, student_id, file_name) VALUES (?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("iis", $assignment_id, $student_id, $file_name);
                $stmt->execute();
                $stmt->close();
                echo "The file " . basename($file_name) . " has been uploaded.";
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
} else {
    echo "Assignment submission is closed. Due date has passed.";
}

$conn->close();
?>

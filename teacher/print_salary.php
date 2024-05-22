<?php
session_start();
include('config.php');

// Check if the user is logged in as a teacher
if (!isset($_SESSION["teacher_id"])) {
    header("Location: teacher_login.php");
    exit;
}

// Check if salary_id is provided
if (!isset($_POST["salary_id"])) {
    header("Location: view_salary.php"); // Redirect to salaries page if salary_id is not provided
    exit;
}

$salary_id = $_POST["salary_id"];
$teacher_id = $_SESSION["teacher_id"];

// Fetch the selected salary details along with teacher's name
$sql = "SELECT s.*, t.first_name, t.last_name 
        FROM Salaries s
        JOIN Teachers t ON s.teacher_id = t.teacher_id
        WHERE s.salary_id = ? AND s.teacher_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $salary_id, $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $salary = $result->fetch_assoc();
    $stmt->close();
}

$conn->close();

// Include the FPDF library
require('../fpdf/fpdf.php');

// Define a class for the PDF document
class PDF extends FPDF
{
    // Page header
    function Header()
    {
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Title
        $this->Cell(0, 10, 'Salary Slip', 0, 1, 'C');
        // Line break
        $this->Ln(10);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

// Instantiate the PDF class
$pdf = new PDF();
$pdf->AddPage();

// Set font for the entire document
$pdf->SetFont('Arial', '', 12);

// Add school name
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'School Automation System', 0, 1, 'C');
$pdf->Ln(5);

// Add teacher's name
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Teacher Name: ' . $salary['first_name'] . ' ' . $salary['last_name'], 0, 1);
$pdf->Ln(5);

// Add salary details to the PDF
$pdf->Cell(40, 10, 'Salary ID:', 0, 0);
$pdf->Cell(0, 10, $salary['salary_id'], 0, 1);

$pdf->Cell(40, 10, 'Amount:', 0, 0);
$pdf->Cell(0, 10, $salary['amount'], 0, 1);

$pdf->Cell(40, 10, 'Date:', 0, 0);
$pdf->Cell(0, 10, $salary['date'], 0, 1);

$pdf->Cell(40, 10, 'Status:', 0, 0);
$pdf->Cell(0, 10, $salary['status'], 0, 1);

// Output the PDF
$pdf->Output();
?>

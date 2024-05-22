<?php
session_start();
include('config.php');
require('../fpdf/fpdf.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Get salary ID from URL
if (isset($_GET['print_id'])) {
    $salary_id = $_GET['print_id'];
    
    // Fetch salary details
    $sql = "SELECT s.salary_id, t.first_name AS teacher_first_name, t.last_name AS teacher_last_name, 
                   st.first_name AS staff_first_name, st.last_name AS staff_last_name, s.amount, s.date, s.status 
            FROM Salaries s
            LEFT JOIN Teachers t ON s.teacher_id = t.teacher_id
            LEFT JOIN Staff st ON s.staff_id = st.staff_id
            WHERE s.salary_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $salary_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $salary = $result->fetch_assoc();
        $stmt->close();
    }
}

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

$pdf->Cell(0, 10, 'Salary Details', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 10, 'User Type:', 0, 0);
$user_type = $salary['teacher_first_name'] ? 'Teacher' : 'Staff';
$pdf->Cell(0, 10, $user_type, 0, 1);

$first_name = $salary['teacher_first_name'] ? $salary['teacher_first_name'] : $salary['staff_first_name'];
$last_name = $salary['teacher_last_name'] ? $salary['teacher_last_name'] : $salary['staff_last_name'];
$pdf->Cell(40, 10, 'First Name:', 0, 0);
$pdf->Cell(0, 10, $first_name, 0, 1);
$pdf->Cell(40, 10, 'Last Name:', 0, 0);
$pdf->Cell(0, 10, $last_name, 0, 1);
$pdf->Cell(40, 10, 'Amount:', 0, 0);
$pdf->Cell(0, 10, $salary['amount'], 0, 1);
$pdf->Cell(40, 10, 'Date:', 0, 0);
$pdf->Cell(0, 10, $salary['date'], 0, 1);
$pdf->Cell(40, 10, 'Status:', 0, 0);
$pdf->Cell(0, 10, $salary['status'], 0, 1);

$pdf->Output();

$conn->close();
?>

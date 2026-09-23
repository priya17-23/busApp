<?php
session_start();
include("config.php");
require("libs/fpdf.php");

if (!isset($_SESSION['student'])) {
    header("Location: index.php");
    exit();
}

$usn = $conn->real_escape_string($_SESSION['student']);
$student = $conn->query("SELECT * FROM students WHERE usn='$usn'")->fetch_assoc();

if ($student['pass_generated'] != 1) {
    echo "Pass not approved yet.";
    exit();
}

$driver = ['name'=>'Not Assigned','bus_no'=>'N/A','route'=>'N/A','phone'=>'N/A'];
if (!empty($student['driver_id'])) {
    $driver = $conn->query("SELECT * FROM drivers WHERE id=".(int)$student['driver_id'])->fetch_assoc();
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'GSSSIETW Bus Pass',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Ln(5);
$pdf->Cell(0,10,'Student Name: ' . $student['name'],0,1);
$pdf->Cell(0,10,'USN: ' . $student['usn'],0,1);
$pdf->Cell(0,10,'Branch: ' . $student['branch'],0,1);
$pdf->Cell(0,10,'Bus No: ' . $driver['bus_no'],0,1);
$pdf->Cell(0,10,'Route: ' . $driver['route'],0,1);
$pdf->Cell(0,10,'Stop: ' . $student['stop_name'],0,1);
$pdf->Ln(10);

// Add photo if available
if (!empty($student['photo']) && file_exists("uploads/photos/" . $student['photo'])) {
    $pdf->Image("uploads/photos/" . $student['photo'], 150, 30, 40);
}

$pdf->Output();
?>
<?php
// Database connection
$servername = "localhost"; // or your host
$username   = "u520351775_etouch"; // your DB username
$password   = "!@#Admin@4321";  // your DB password
$dbname     = "u520351775_bestmobiles"; // your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

require 'vendor/autoload.php';  // Make sure composer is installed

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Fetch data
$sql = "SELECT * FROM Branch_Master";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $rowIndex = 1;

    // Set header
    $fields = $result->fetch_fields();
    $col = 'A';
    foreach ($fields as $field) {
        $sheet->setCellValue($col.$rowIndex, $field->name);
        $col++;
    }

    // Data rows
    while($row = $result->fetch_assoc()) {
        $rowIndex++;
        $col = 'A';
        foreach ($row as $value) {
            $sheet->setCellValue($col.$rowIndex, $value);
            $col++;
        }
    }
}

// Output Excel file
$writer = new Xlsx($spreadsheet);
$filename = "branch_data.xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
$writer->save("php://output");
exit;
?>

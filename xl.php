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

// Which table to export
if (!isset($_GET['table'])) {
    die("No table specified!");
}

$table = $_GET['table'];

// Prevent SQL injection (allow only expected tables)
$allowedTables = [
    "Branch_Master",
    "Brand_Master",
    "Staff_Master",
    "Customer_Master",
    "Insurance_Master",
    "Defect_Master",
    "Insurance_Entry",
    "Claim_Entry"
];

if (!in_array($table, $allowedTables)) {
    die("Invalid table name!");
}

// Fetch data
$sql = "SELECT * FROM $table";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Set headers for Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename={$table}_export.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Print column headers
$fields = $result->fetch_fields();
foreach ($fields as $field) {
    echo $field->name . "\t";
}
echo "\n";

// Print rows
while ($row = $result->fetch_assoc()) {
    echo implode("\t", $row) . "\n";
}

$conn->close();
exit;
?>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get form data
$defect_name = trim($_POST['defect_name']);
$defect_description = trim($_POST['defect_description']);
$defect_status = $_POST['defect_status'] ?? 1;

// Validate input
if (empty($defect_name) || empty($defect_description)) {
    die("❌ All fields are required.");
}

// Check for duplicate defect name
$check_sql = "SELECT Defect_Id FROM Defect_Master WHERE Defect_Name = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "s", $defect_name);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if (mysqli_stmt_num_rows($check_stmt) > 0) {
    echo "❌ Defect '$defect_name' already exists.";
} else {
    // Insert new defect
    $insert_sql = "INSERT INTO Defect_Master (Defect_Name, Defect_Description, Defect_Status, Created_At)
                   VALUES (?, ?, ?, NOW())";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "ssi", $defect_name, $defect_description, $defect_status);

    if (mysqli_stmt_execute($insert_stmt)) {
        echo "✅ Defect '$defect_name' added successfully!";
    } else {
        echo "❌ Error inserting defect: " . mysqli_error($conn);
    }

    mysqli_stmt_close($insert_stmt);
}

mysqli_stmt_close($check_stmt);
mysqli_close($conn);
?>

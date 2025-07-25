<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get form data
$insurance_name = trim($_POST['insurance_name']);
$insurance_description = trim($_POST['insurance_description']);
$premium_percentage = intval($_POST['premium_percentage']);
$duration = trim($_POST['duration']);
$insurance_status = $_POST['insurance_status'] ?? 1;

// Validate input
if (
    empty($insurance_name) || empty($insurance_description) || 
    empty($premium_percentage) || empty($duration)
) {
    die("❌ All fields are required.");
}

// Check for duplicate insurance name
$check_sql = "SELECT Insurance_Id FROM Insurance_Master WHERE Insurance_Name = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "s", $insurance_name);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if (mysqli_stmt_num_rows($check_stmt) > 0) {
    echo "❌ Insurance plan '$insurance_name' already exists.";
} else {
    // Insert new insurance plan
    $insert_sql = "INSERT INTO Insurance_Master 
        (Insurance_Name, Insurance_Description, Premium_Percentage, Duration, Insurance_Status, Created_At)
        VALUES (?, ?, ?, ?, ?, NOW())";

    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "ssisi", $insurance_name, $insurance_description, $premium_percentage, $duration, $insurance_status);

    if (mysqli_stmt_execute($insert_stmt)) {
        echo "✅ Insurance plan '$insurance_name' added successfully!";
    } else {
        echo "❌ Error: " . mysqli_error($conn);
    }

    mysqli_stmt_close($insert_stmt);
}

mysqli_stmt_close($check_stmt);
mysqli_close($conn);
?>

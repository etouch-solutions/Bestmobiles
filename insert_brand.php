<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include your DB connection file
include 'db.php';

// Connect to the database
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Collect form data
$brand_name = trim($_POST['brand_name']);
$is_active = $_POST['is_active'] ?? 1;

// Validate input
if (empty($brand_name)) {
    die(" Brand name is required.");
}

// Check for duplicate brand name
$check_sql = "SELECT * FROM Brands_Master WHERE Brand_Name = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "s", $brand_name);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if (mysqli_stmt_num_rows($check_stmt) > 0) {
    echo "❌ Brand '$brand_name' already exists.";
} else {
    // Insert new brand
    $insert_sql = "INSERT INTO Brands_Master (Brand_Name, Is_Active, Created_At) VALUES (?, ?, NOW())";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "si", $brand_name, $is_active);

    if (mysqli_stmt_execute($insert_stmt)) {
        echo "✅ Brand '$brand_name' added successfully!";
    } else {
        echo "❌ Error inserting brand: " . mysqli_error($conn);
    }

    mysqli_stmt_close($insert_stmt);
}

mysqli_stmt_close($check_stmt);
mysqli_close($conn);
?>

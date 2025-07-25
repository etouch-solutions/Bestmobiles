<?php
 include 'db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connect to database
$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get form data
$branch_name = $_POST['branch_name'];
$branch_head_name = $_POST['branch_head_name'];
$branch_address = $_POST['branch_address'];
$branch_cno = $_POST['branch_cno'];
$branch_status = $_POST['branch_status'];

// Insert into table
$sql = "INSERT INTO Branch_Master 
    (Branch_Name, Branch_Head_Name, Branch_Address, Branch_CNo, Branch_Status, Created_At)
    VALUES 
    (?, ?, ?, ?, ?, NOW())";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssii", $branch_name, $branch_head_name, $branch_address, $branch_cno, $branch_status);

if (mysqli_stmt_execute($stmt)) {
    echo "Branch added successfully!";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

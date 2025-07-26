<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get form data
$branch_name = $_POST['branch_name'];
$branch_head_name = $_POST['branch_head_name'];
$branch_address = $_POST['branch_address'];
$branch_cno = $_POST['branch_cno'];
$branch_status = $_POST['branch_status'];

//  Check if branch already exists
$check_sql = "SELECT * FROM Branch_Master WHERE Branch_Name = ? AND Branch_Address = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "ss", $branch_name, $branch_address);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if (mysqli_stmt_num_rows($check_stmt) > 0) {
    echo " Branch with this name and address already exists.";
} else {
    // Insert new branch
    $sql = "INSERT INTO Branch_Master 
        (Branch_Name, Branch_Head_Name, Branch_Address, Branch_CNo, Branch_Status, Created_At)
        VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssii", $branch_name, $branch_head_name, $branch_address, $branch_cno, $branch_status);

    if (mysqli_stmt_execute($stmt)) {
        echo " Branch added successfully!";
    } else {
        echo " Error inserting branch: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

mysqli_stmt_close($check_stmt);
mysqli_close($conn);
?>
 
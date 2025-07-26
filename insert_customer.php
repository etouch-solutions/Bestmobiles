<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

// Sanitize inputs
$cus_name = $_POST['cus_name'];
$cus_cno = $_POST['cus_cno'];
$cus_address = $_POST['cus_address'];
$cus_email = $_POST['cus_email'];
$cus_ref = $_POST['cus_ref'];
$cus_ref_cno = $_POST['cus_ref_cno'];
$branch_id = $_POST['branch_id'];
$cus_status = $_POST['cus_status'] ?? 1;

// Handle file uploads
$photo = file_get_contents($_FILES['cus_photo']['tmp_name']);
$id_copy = file_get_contents($_FILES['cus_id_copy']['tmp_name']);

$sql = "INSERT INTO Customer_Master 
(Cus_Name, Cus_CNo, Cus_Address, Cus_Email, Cus_Ref_Name, Cus_Ref_CNo, Branch_Id, Cus_Photo, Cus_Id_Copy, Cus_Status) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sisssiibbi",
    $cus_name, $cus_cno, $cus_address, $cus_email, $cus_ref, $cus_ref_cno,
    $branch_id, $photo, $id_copy, $cus_status
);

if (mysqli_stmt_execute($stmt)) {
    echo "✅ Customer added successfully!";
} else {
    echo "❌ Error: " . mysqli_error($conn);
}
?>

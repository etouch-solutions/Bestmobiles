<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

// Get form data
$cus_name = $_POST['cus_name'];
$cus_cno = $_POST['cus_cno'];
$cus_address = $_POST['cus_address'];
$cus_email = $_POST['cus_email'];
$cus_ref = $_POST['cus_ref'];
$cus_ref_cno = $_POST['cus_ref_cno'];
$branch_id = $_POST['branch_id'];
$is_active = $_POST['cus_status'] ?? 1;

// Upload files
$upload_dir = "uploads/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$photo_name = basename($_FILES["cus_photo"]["name"]);
$photo_path = $upload_dir . time() . "_photo_" . $photo_name;
move_uploaded_file($_FILES["cus_photo"]["tmp_name"], $photo_path);

$id_name = basename($_FILES["cus_id_copy"]["name"]);
$id_copy_path = $upload_dir . time() . "_id_" . $id_name;
move_uploaded_file($_FILES["cus_id_copy"]["tmp_name"], $id_copy_path);

// Insert query
$sql = "INSERT INTO Customer_Master (
    Cus_Name, Cus_CNo, Cus_Address, Cus_Email, Cus_Ref_Name, Cus_Ref_CNo,
    Branch_Id, Cus_Photo_Path, Cus_Id_Copy_Path, Is_Active, Created_At
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sisssisssi", 
    $cus_name, $cus_cno, $cus_address, $cus_email, $cus_ref, $cus_ref_cno,
    $branch_id, $photo_path, $id_copy_path, $is_active
);

if (mysqli_stmt_execute($stmt)) {
    echo "✅ Customer added successfully!";
} else {
    echo "❌ Error: " . mysqli_error($conn);
}
?>

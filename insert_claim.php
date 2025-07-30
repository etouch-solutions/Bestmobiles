<?php
include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$insurance_id = $_POST['insurance_entry_id'];
$defect_id = $_POST['defect_id'];
$claim_date = $_POST['claim_date'];
$remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
$created_at = date('Y-m-d H:i:s');

$image_path = "";
if (isset($_FILES['claim_image']) && $_FILES['claim_image']['error'] === UPLOAD_ERR_OK) {
  $uploadDir = "uploads/";
  $fileName = time() . "_" . basename($_FILES['claim_image']['name']);
  $targetPath = $uploadDir . $fileName;

  if (move_uploaded_file($_FILES['claim_image']['tmp_name'], $targetPath)) {
    $image_path = $targetPath;
  }
}

// Insert into database
$sql = "INSERT INTO Claim_Entry (Insurance_Entry_Id, Defect_Id, Claim_Date, Remarks, Uploaded_Image_Path, Created_At)
        VALUES ('$insurance_id', '$defect_id', '$claim_date', '$remarks', '$image_path', '$created_at')";

if (mysqli_query($conn, $sql)) {
  echo "<script>alert('Claim Submitted Successfully'); window.location.href='claim_entry.php';</script>";
} else {
  echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>

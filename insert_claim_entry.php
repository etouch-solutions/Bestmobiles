<?php
include 'db.php';

$insurance_id = $_POST['insurance_entry_id'];
$defect_id = $_POST['defect_id'];
$remarks = $_POST['remarks'];
$date = date('Y-m-d');

$image_path = '';
if ($_FILES['claim_image']['error'] === UPLOAD_ERR_OK) {
  $tmp = $_FILES['claim_image']['tmp_name'];
  $name = basename($_FILES['claim_image']['name']);
  $path = "uploads/claims/" . time() . "_" . $name;
  move_uploaded_file($tmp, $path);
  $image_path = $path;
}

$sql = "
  INSERT INTO Claim_Entry (Insurance_Entry_Id, Defect_Id, Claim_Date, Remarks, Uploaded_Image_Path)
  VALUES ('$insurance_id', '$defect_id', '$date', '$remarks', '$image_path')
";

if (mysqli_query($conn, $sql)) {
  echo "Claim submitted successfully.";
} else {
  echo "Error: " . mysqli_error($conn);
}
?>

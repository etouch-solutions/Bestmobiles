<?php
include 'db.php';

$conn = mysqli_connect($host, $user, $pass, $db);

$insurance_entry_id = $_POST['insurance_entry_id'];
$defect_id = $_POST['defect_id'];
$claim_date = $_POST['claim_date'];
$remarks = $_POST['remarks'];
$created_at = date('Y-m-d H:i:s');

$image_path = '';
if (isset($_FILES['claim_image']) && $_FILES['claim_image']['error'] === UPLOAD_ERR_OK) {
  $upload_dir = 'uploads/claims/';
  if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
  }

  $file_tmp = $_FILES['claim_image']['tmp_name'];
  $file_name = uniqid() . '_' . basename($_FILES['claim_image']['name']);
  $file_path = $upload_dir . $file_name;

  if (move_uploaded_file($file_tmp, $file_path)) {
    $image_path = $file_path;
  }
}

$query = "INSERT INTO Claim_Entry 
          (Insurance_Entry_Id, Defect_Id, Claim_Date, Remarks, Claim_Image_Path, Created_At)
          VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($query);
$stmt->bind_param("iissss", $insurance_entry_id, $defect_id, $claim_date, $remarks, $image_path, $created_at);
$success = $stmt->execute();

if ($success) {
  echo "Claim submitted successfully.";
} else {
  echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

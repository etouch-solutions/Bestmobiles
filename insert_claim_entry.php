<?php
include 'db.php';

$conn = mysqli_connect($host, $user, $pass, $db);

$insurance_entry_id = $_POST['insurance_entry_id'];
$defect_id = $_POST['defect_id'];
$remarks = mysqli_real_escape_string($conn, $_POST['remarks'] ?? '');
$claim_date = date('Y-m-d');
$upload_path = '';

if (isset($_FILES['claim_image']) && $_FILES['claim_image']['error'] === 0) {
  $targetDir = "uploads/";
  $fileName = basename($_FILES["claim_image"]["name"]);
  $targetFilePath = $targetDir . time() . '_' . $fileName;
  if (move_uploaded_file($_FILES["claim_image"]["tmp_name"], $targetFilePath)) {
    $upload_path = $targetFilePath;
  }
}

$sql = "INSERT INTO Claim_Entry (Insurance_Entry_Id, Defect_Id, Claim_Date, Remarks, Uploaded_Image_Path)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisss", $insurance_entry_id, $defect_id, $claim_date, $remarks, $upload_path);
$stmt->execute();

echo "<script>alert('Claim submitted successfully!'); window.location.href='claim_entry.php';</script>";
?>

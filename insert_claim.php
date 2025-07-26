<?php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

$sql = "INSERT INTO Claim_Entry 
(Insurance_Entry_Id, Defect_Id, Defect_Value, Defect_Description, Claim_Date, Created_At)
VALUES (?, ?, ?, ?, ?, NOW())";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iiiss",
    $_POST['insurance_entry_id'], $_POST['defect_id'],
    $_POST['defect_value'], $_POST['defect_description'], $_POST['claim_date']
);

mysqli_stmt_execute($stmt);
echo "✅ Claim entry added!";
?>

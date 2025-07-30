<?php
include 'db.php';

$data = [];
$res = mysqli_query($conn, "SELECT Defect_Id, Defect_Name FROM Claim_Defects");
while ($row = mysqli_fetch_assoc($res)) {
  $data[] = $row;
}
header('Content-Type: application/json');
echo json_encode($data);

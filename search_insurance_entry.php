<?php
include 'db.php';

$q = $_GET['q'] ?? '';
if (!$q) {
  echo json_encode([]);
  exit;
}

$conn = mysqli_connect($host, $user, $pass, $db);
$q = mysqli_real_escape_string($conn, $q);

$sql = "SELECT ie.Insurance_Entry_Id, cm.Cus_Name, ie.Product_Model_Name, ie.IMEI_1
        FROM Insurance_Entry ie
        JOIN Customer_Master cm ON ie.Cus_Id = cm.Cus_Id
        WHERE cm.Cus_Name LIKE '%$q%' OR ie.IMEI_1 LIKE '%$q%'";

$res = mysqli_query($conn, $sql);
$rows = [];
while ($row = mysqli_fetch_assoc($res)) {
  $rows[] = $row;
}

header('Content-Type: application/json');
echo json_encode($rows);
?>

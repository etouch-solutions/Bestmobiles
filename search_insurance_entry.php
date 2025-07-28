<?php
include 'db.php';

$q = $_GET['q'] ?? '';
$data = [];

if ($q !== '') {
  $q = mysqli_real_escape_string($conn, $q);
  $sql = "
    SELECT ie.Insurance_Entry_Id, cm.Cus_Name, ie.Product_Model_Name, ie.IMEI_1 
    FROM Insurance_Entry ie
    JOIN Customer_Master cm ON cm.Cus_Id = ie.Cus_Id
    WHERE cm.Cus_Name LIKE '%$q%' OR ie.IMEI_1 LIKE '%$q%'
  ";
  $res = mysqli_query($conn, $sql);
  while ($row = mysqli_fetch_assoc($res)) {
    $data[] = [
      'insurance_entry_id' => $row['Insurance_Entry_Id'],
      'name' => $row['Cus_Name'],
      'model' => $row['Product_Model_Name'],
      'imei1' => $row['IMEI_1']
    ];
  }
}

header('Content-Type: application/json');
echo json_encode($data);
?>

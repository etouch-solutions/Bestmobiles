<?php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

$q = mysqli_real_escape_string($conn, $_GET['q']);
$data = [];

$sql = "
  SELECT i.Insurance_Entry_Id, c.Cus_Name, i.Product_Model_Name, i.IMEI_1
  FROM Insurance_Entry i
  JOIN Customer_Master c ON i.Cus_Id = c.Cus_Id
  WHERE c.Cus_Name LIKE '%$q%' OR i.IMEI_1 LIKE '%$q%' OR i.IMEI_2 LIKE '%$q%'
  ORDER BY i.Insurance_Entry_Id DESC
  LIMIT 10
";

$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
  $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>

<?php
include 'db.php';
$q = $_GET['q'];
$sql = "SELECT i.Insurance_Entry_Id, c.Cus_Name, i.Product_Model_Name, i.IMEI_1 
        FROM Insurance_Entry i
        JOIN Customer_Master c ON c.Cus_Id = i.Cus_Id
        WHERE c.Cus_Name LIKE '%$q%' OR c.Cus_CNo LIKE '%$q%' OR i.IMEI_1 LIKE '%$q%'";
$result = mysqli_query($conn, $sql);
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
  $data[] = [
    'insurance_entry_id' => $row['Insurance_Entry_Id'],
    'name' => $row['Cus_Name'],
    'model' => $row['Product_Model_Name'],
    'imei1' => $row['IMEI_1']
  ];
}
echo json_encode($data);


<?php
include 'db.php';
$q = $_GET['q'];
$data = [];

$res = mysqli_query($conn, "
  SELECT 
    i.Insurance_Entry_Id,
    c.Cus_Name AS name,
    i.Product_Model_Name AS model,
    i.IMEI_1 AS imei1,
    i.Product_Value AS product_value,
    i.Premium_Amount AS premium_amount,
    (SELECT COUNT(*) FROM Claim_Entry ce WHERE ce.Insurance_Entry_Id = i.Insurance_Entry_Id) AS total_claims,
    (SELECT IFNULL(SUM(cd.Defect_Value), 0)
     FROM Claim_Entry ce
     JOIN Claim_Defects cd ON ce.Defect_Id = cd.Defect_Id
     WHERE ce.Insurance_Entry_Id = i.Insurance_Entry_Id) AS total_claimed
  FROM Insurance_Entry i
  JOIN Customer_Master c ON i.Cus_Id = c.Cus_Id
  WHERE c.Cus_Name LIKE '%$q%' OR c.Cus_CNo LIKE '%$q%' OR i.IMEI_1 LIKE '%$q%'
");

while ($row = mysqli_fetch_assoc($res)) {
  $data[] = $row;
}

echo json_encode($data);
?>

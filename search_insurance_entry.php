<?php
include 'db.php';
header('Content-Type: application/json');

$q = mysqli_real_escape_string($conn, $_GET['q'] ?? '');
$data = [];

if ($q !== '') {
    $sql = "SELECT i.Insurance_Entry_Id, c.Cus_Name, i.Product_Model_Name, i.IMEI_1 
            FROM Insurance_Entry i
            JOIN Customer_Master c ON c.Cus_Id = i.Cus_Id
            WHERE c.Cus_Name LIKE '%$q%' 
               OR c.Cus_CNo LIKE '%$q%' 
               OR i.IMEI_1 LIKE '%$q%'
            LIMIT 10";
    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'insurance_entry_id' => $row['Insurance_Entry_Id'],
            'name' => $row['Cus_Name'],
            'model' => $row['Product_Model_Name'],
            'imei1' => $row['IMEI_1']
        ];
    }
}

echo json_encode($data);
?>

<?php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

$sql = "INSERT INTO Insurance_Entry 
(Cus_Id, Brand_Id, Insurance_Id, Staff_Id, Product_Model_Name, IMEI_1, IMEI_2, Product_Value,
 Bill_Date, Insurance_Start_Date, Insurance_End_Date, Premium_Amount,
 Product_Ins_Status, Insurance_Status, Created_At)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iiiisssiissiii",
    $_POST['cus_id'], $_POST['brand_id'], $_POST['insurance_id'], $_POST['staff_id'],
    $_POST['product_model_name'], $_POST['imei_1'], $_POST['imei_2'], $_POST['product_value'],
    $_POST['bill_date'], $_POST['insurance_start'], $_POST['insurance_end'],
    $_POST['premium_amount'], $_POST['product_ins_status'], $_POST['insurance_status']
);

mysqli_stmt_execute($stmt);
echo "✅ Insurance entry added!";
?>

<?php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

// File Uploads
$productPhotoName = $_FILES['product_photo']['name'];
$billPhotoName = $_FILES['bill_photo']['name'];
$productPhotoPath = "uploads/" . basename($productPhotoName);
$billPhotoPath = "uploads/" . basename($billPhotoName);

move_uploaded_file($_FILES['product_photo']['tmp_name'], $productPhotoPath);
move_uploaded_file($_FILES['bill_photo']['tmp_name'], $billPhotoPath);

// Inputs
$cus_id = $_POST['cus_id'];
$brand_id = $_POST['brand_id'];
$insurance_id = $_POST['insurance_id'];
$staff_id = $_POST['staff_id'];
$model = $_POST['product_model_name'];
$imei1 = $_POST['imei_1'];
$imei2 = $_POST['imei_2'];
$product_value = $_POST['product_value'];
$premium = $_POST['premium_amount'];
$bill_date = $_POST['bill_date'];
$start_date = $_POST['insurance_start'];
$end_date = $_POST['insurance_end'];
$is_active = $_POST['insurance_status'];
$is_product = $_POST['product_ins_status'];
$created_at = date('Y-m-d H:i:s');

// Insert
$sql = "INSERT INTO Insurance_Entry (
    Cus_Id, Brand_Id, Insurance_Id, Staff_Id, Product_Model_Name, IMEI_1, IMEI_2, Product_Value,
    Bill_Copy_Path, Product_Photo_Path, Bill_Date, Insurance_Start_Date, Insurance_End_Date, 
    Premium_Amount, Is_Product_Covered, Is_Insurance_Active, Created_At
) VALUES (
    '$cus_id', '$brand_id', '$insurance_id', '$staff_id', '$model', '$imei1', '$imei2', '$product_value',
    '$billPhotoPath', '$productPhotoPath', '$bill_date', '$start_date', '$end_date',
    '$premium', '$is_product', '$is_active', '$created_at'
)";

if (mysqli_query($conn, $sql)) {
    echo "Entry successfully added!";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>

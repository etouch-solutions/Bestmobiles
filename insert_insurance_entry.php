<?php
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$cus_id = $_POST['cus_id'];
$brand_id = $_POST['brand_id'];
$insurance_id = $_POST['insurance_id'];
$staff_id = $_POST['staff_id'];
$product_model_name = $_POST['product_model_name'];
$imei_1 = $_POST['imei_1'];
$imei_2 = $_POST['imei_2'];
$product_value = $_POST['product_value'];
$premium_amount = $_POST['premium_amount'];
$bill_date = $_POST['bill_date'];
$insurance_start = $_POST['insurance_start'];
$insurance_end = $_POST['insurance_end'];
$insurance_status = $_POST['insurance_status'];
$product_ins_status = $_POST['product_ins_status'];

$product_photo_path = '';
$bill_photo_path = '';
$upload_dir = "uploads/";

if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

// Upload product photo
if (!empty($_FILES['product_photo']['name'])) {
    $product_photo_path = $upload_dir . basename($_FILES['product_photo']['name']);
    move_uploaded_file($_FILES['product_photo']['tmp_name'], $product_photo_path);
}

// Upload bill photo
if (!empty($_FILES['bill_photo']['name'])) {
    $bill_photo_path = $upload_dir . basename($_FILES['bill_photo']['name']);
    move_uploaded_file($_FILES['bill_photo']['tmp_name'], $bill_photo_path);
}

$query = "INSERT INTO Insurance_Entry (
    Cus_Id, Brand_Id, Insurance_Id, Staff_Id, Product_Model_Name, IMEI_1, IMEI_2,
    Product_Value, Premium_Amount, Bill_Date, Insurance_Start, Insurance_End,
    Insurance_Status, Product_Insurance_Status, Product_Photo, Bill_Photo
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iiiisssddsssisss",
    $cus_id, $brand_id, $insurance_id, $staff_id, $product_model_name, $imei_1, $imei_2,
    $product_value, $premium_amount, $bill_date, $insurance_start, $insurance_end,
    $insurance_status, $product_ins_status, $product_photo_path, $bill_photo_path
);

if (mysqli_stmt_execute($stmt)) {
    echo "✅ Insurance Entry inserted successfully.";
} else {
    echo "❌ Error: " . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

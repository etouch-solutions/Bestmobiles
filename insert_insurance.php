<?php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Collect form data
$cus_id           = $_POST['cus_id'];
$brand_id         = $_POST['brand_id'];
$insurance_id     = $_POST['insurance_id'];
$staff_id         = $_POST['staff_id'];
$product_model    = $_POST['product_model_name'];
$imei_1           = $_POST['imei_1'];
$imei_2           = $_POST['imei_2'];
$product_value    = $_POST['product_value'];
$premium_amount   = $_POST['premium_amount']; // ✅ Will take either auto-calculated or manual edit
$bill_date        = $_POST['bill_date'];
$insurance_start  = $_POST['insurance_start'];
$insurance_end    = $_POST['insurance_end'];
$insurance_status = $_POST['insurance_status'];
$product_status   = $_POST['product_ins_status'];

// File upload handling
$product_photo = "";
$bill_photo    = "";

if (!empty($_FILES['product_photo']['name'])) {
    $product_photo = "uploads/" . basename($_FILES['product_photo']['name']);
    move_uploaded_file($_FILES['product_photo']['tmp_name'], $product_photo);
}

if (!empty($_FILES['bill_photo']['name'])) {
    $bill_photo = "uploads/" . basename($_FILES['bill_photo']['name']);
    move_uploaded_file($_FILES['bill_photo']['tmp_name'], $bill_photo);
}

// Insert query
$sql = "INSERT INTO Insurance_Entry 
        (Cus_Id, Brand_Id, Insurance_Id, Staff_Id, Product_Model, IMEI_1, IMEI_2, Product_Value, Premium_Amount, Bill_Date, Insurance_Start, Insurance_End, Insurance_Status, Product_Status, Product_Photo, Bill_Photo) 
        VALUES 
        ('$cus_id','$brand_id','$insurance_id','$staff_id','$product_model','$imei_1','$imei_2','$product_value','$premium_amount','$bill_date','$insurance_start','$insurance_end','$insurance_status','$product_status','$product_photo','$bill_photo')";

if (mysqli_query($conn, $sql)) {
    echo "<h2>✅ Insurance Entry Added Successfully!</h2>";
    echo "<a href='insuranceentry.php'>Add Another</a>";
} else {
    echo "❌ Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

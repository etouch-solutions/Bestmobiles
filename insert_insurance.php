<?php
// Turn on error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include DB connection
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch all inputs
$cus_id = $_POST['cus_id'] ?? '';
$brand_id = $_POST['brand_id'] ?? '';
$insurance_id = $_POST['insurance_id'] ?? '';
$staff_id = $_POST['staff_id'] ?? '';
$model_name = $_POST['product_model_name'] ?? '';
$imei_1 = $_POST['imei_1'] ?? '';
$imei_2 = $_POST['imei_2'] ?? '';
$product_value = $_POST['product_value'] ?? 0;
$premium_amount = $_POST['premium_amount'] ?? 0;
$bill_date = $_POST['bill_date'] ?? '';
$start_date = $_POST['insurance_start'] ?? '';
$end_date = $_POST['insurance_end'] ?? '';
$insurance_status = $_POST['insurance_status'] ?? 1;
$product_ins_status = $_POST['product_ins_status'] ?? 1;

// Handle file uploads
$product_photo = $_FILES['product_photo']['name'];
$product_tmp = $_FILES['product_photo']['tmp_name'];
$bill_photo = $_FILES['bill_photo']['name'];
$bill_tmp = $_FILES['bill_photo']['tmp_name'];

// Upload folder
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true); // create folder if not exist
}

move_uploaded_file($product_tmp, $upload_dir . $product_photo);
move_uploaded_file($bill_tmp, $upload_dir . $bill_photo);

// Prepare insert query
$sql = "INSERT INTO Insurance_Entry (
    Cus_Id, Brand_Id, Insurance_Id, Staff_Id,
    Product_Model_Name, IMEI_1, IMEI_2,
    Product_Value, Premium_Amount,
    Bill_Date, Insurance_Start, Insurance_End,
    Product_Ins_Status, Insurance_Status,
    Product_Photo, Bill_Photo
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind data
$stmt->bind_param("iiiisssddsssisss",
    $cus_id, $brand_id, $insurance_id, $staff_id,
    $model_name, $imei_1, $imei_2,
    $product_value, $premium_amount,
    $bill_date, $start_date, $end_date,
    $product_ins_status, $insurance_status,
    $product_photo, $bill_photo
);

// Execute and check result
if ($stmt->execute()) {
    echo "<h2>✅ Insurance Entry Saved Successfully!</h2>";
    echo "<a href='your_form_page.php'>Add Another Entry</a>";
} else {
    echo "<h2>❌ Failed to Save Entry</h2>";
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

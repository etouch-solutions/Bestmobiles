<?php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $cus_id = $_POST['cus_id'] ?? null;
    $brand_id = $_POST['brand_id'] ?? null;
    $insurance_id = $_POST['insurance_id'] ?? null;
    $staff_id = $_POST['staff_id'] ?? null;
    $product_model_name = $_POST['product_model_name'] ?? null;
    $imei_1 = $_POST['imei_1'] ?? null;
    $imei_2 = $_POST['imei_2'] ?? null;
    $product_value = $_POST['product_value'] ?? null;
    $premium_amount = $_POST['premium_amount'] ?? null;
    $bill_date = $_POST['bill_date'] ?? null;
    $insurance_start = $_POST['insurance_start'] ?? null;
    $insurance_status = $_POST['insurance_status'] ?? 0;
    $product_ins_status = $_POST['product_ins_status'] ?? 0;

    // 📸 Handle product photo upload
    $product_photo_path = "";
    if (!empty($_FILES["product_photo"]["name"])) {
        $product_photo_path = "uploads/" . basename($_FILES["product_photo"]["name"]);
        move_uploaded_file($_FILES["product_photo"]["tmp_name"], $product_photo_path);
    }

    // 🧾 Handle bill photo upload
    $bill_photo_path = "";
    if (!empty($_FILES["bill_photo"]["name"])) {
        $bill_photo_path = "uploads/" . basename($_FILES["bill_photo"]["name"]);
        move_uploaded_file($_FILES["bill_photo"]["tmp_name"], $bill_photo_path);
    }

    // 🕒 Fetch duration from Insurance_Master
    $insurance_duration_months = 0;
    $res = mysqli_query($conn, "SELECT Duration_Months FROM Insurance_Master WHERE Insurance_Id = $insurance_id");
    if ($row = mysqli_fetch_assoc($res)) {
        $insurance_duration_months = $row['Duration_Months'];
    }

    // 🧮 Calculate insurance end date
    $insurance_end = date('Y-m-d', strtotime("+$insurance_duration_months months", strtotime($insurance_start)));

    // 💾 Insert data
    $stmt = $conn->prepare("INSERT INTO Insurance_Entry (
        Cus_Id, Brand_Id, Insurance_Id, Staff_Id, Product_Model_Name, IMEI_1, IMEI_2,
        Product_Value, Bill_Copy_Path, Product_Photo_Path, Bill_Date, Insurance_Start_Date, 
        Insurance_End_Date, Premium_Amount, Is_Product_Covered, Is_Insurance_Active, Created_At
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    $stmt->bind_param("iiiisssddsssddii",
        $cus_id, $brand_id, $insurance_id, $staff_id, $product_model_name, $imei_1, $imei_2,
        $product_value, $bill_photo_path, $product_photo_path, $bill_date, $insurance_start,
        $insurance_end, $premium_amount, $product_ins_status, $insurance_status
    );

    try {
        $stmt->execute();
        echo "✅ Insurance Entry Added Successfully.";
    } catch (mysqli_sql_exception $e) {
        echo "❌ Error: " . $e->getMessage();
    }

    $stmt->close();
    $conn->close();
}
?>

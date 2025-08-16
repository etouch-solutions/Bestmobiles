<?php
include 'db.php';

// Get Insurance Entry Id from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Insurance Entry ID.");
}
$insurance_entry_id = intval($_GET['id']);

// Fetch Insurance Entry Details
$sql = "
SELECT 
    ie.Insurance_Entry_Id,
    ie.Product_Model_Name,
    ie.IMEI_1,
    ie.IMEI_2,
    ie.Product_Value,
    ie.Bill_Copy_Path,
    ie.Product_Photo_Path,
    ie.Bill_Date,
    ie.Insurance_Start_Date,
    ie.Insurance_End_Date,
    ie.Premium_Amount,
    ie.Is_Product_Covered,
    ie.Is_Insurance_Active,
    ie.Created_At,
    
    c.Cus_Name,
    c.Cus_CNo,
    c.Cus_Address,
    c.Cus_Email,
    c.Cus_Ref_Name,
    c.Cus_Ref_CNo,
    c.Cus_Photo_Path,
    c.Cus_Id_Copy_Path,
    
    b.Brand_Name,
    br.Branch_Name,
    s.Staff_Name,
    i.Insurance_Name,
    i.Duration_Months,
    i.Premium_Percentage

FROM insurance_entry ie
LEFT JOIN customer_master c ON ie.Cus_Id = c.Cus_Id
LEFT JOIN brand_master b ON ie.Brand_Id = b.Brand_Id
LEFT JOIN branch_master br ON c.Branch_Id = br.Branch_Id
LEFT JOIN staff_master s ON ie.Staff_Id = s.Staff_Id
LEFT JOIN insurance_master i ON ie.Insurance_Id = i.Insurance_Id
WHERE ie.Insurance_Entry_Id = '$insurance_entry_id'
";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("No insurance entry found.");
}

$data = mysqli_fetch_assoc($result);

// Fetch Claim History for this insurance
$claims_sql = "
SELECT 
    ce.Claim_Id,
    ce.Claim_Date,
    ce.Remarks,
    ce.Claim_Image_Path,
    d.Defect_Name
FROM claim_entry ce
LEFT JOIN claim_defects d ON ce.Defect_Id = d.Defect_Id
WHERE ce.Insurance_Entry_Id = '$insurance_entry_id'
ORDER BY ce.Claim_Date DESC
";
$claims_result = mysqli_query($conn, $claims_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insurance Details</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .details-container {
            display: flex;
            gap: 20px;
            margin: 20px;
        }
        .panel {
            flex: 1;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .panel h2 { margin-bottom: 10px; }
        .details-table { width: 100%; border-collapse: collapse; }
        .details-table td { padding: 6px; border-bottom: 1px solid #eee; }
        .claims-list { margin-top: 15px; }
        .claims-list li { padding: 5px; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>

<h1 style="text-align:center;">Insurance & Claim Details</h1>

<div class="details-container">
    <!-- Customer & Product Info -->
    <div class="panel">
        <h2>Customer Details</h2>
        <table class="details-table">
            <tr><td>Name</td><td><?= $data['Cus_Name'] ?></td></tr>
            <tr><td>Contact</td><td><?= $data['Cus_CNo'] ?></td></tr>
            <tr><td>Email</td><td><?= $data['Cus_Email'] ?></td></tr>
            <tr><td>Address</td><td><?= $data['Cus_Address'] ?></td></tr>
            <tr><td>Reference</td><td><?= $data['Cus_Ref_Name'] ?> (<?= $data['Cus_Ref_CNo'] ?>)</td></tr>
        </table>
        <h2>Product Details</h2>
        <table class="details-table">
            <tr><td>Brand</td><td><?= $data['Brand_Name'] ?></td></tr>
            <tr><td>Model</td><td><?= $data['Product_Model_Name'] ?></td></tr>
            <tr><td>IMEI 1</td><td><?= $data['IMEI_1'] ?></td></tr>
            <tr><td>IMEI 2</td><td><?= $data['IMEI_2'] ?></td></tr>
            <tr><td>Value</td><td>₹<?= $data['Product_Value'] ?></td></tr>
        </table>
    </div>

    <!-- Insurance Info -->
    <div class="panel">
        <h2>Insurance Details</h2>
        <table class="details-table">
            <tr><td>Plan</td><td><?= $data['Insurance_Name'] ?></td></tr>
            <tr><td>Duration</td><td><?= $data['Duration_Months'] ?> months</td></tr>
            <tr><td>Premium %</td><td><?= $data['Premium_Percentage'] ?>%</td></tr>
            <tr><td>Premium Amount</td><td>₹<?= $data['Premium_Amount'] ?></td></tr>
            <tr><td>Start Date</td><td><?= $data['Insurance_Start_Date'] ?></td></tr>
            <tr><td>End Date</td><td><?= $data['Insurance_End_Date'] ?></td></tr>
            <tr><td>Status</td><td><?= $data['Is_Insurance_Active'] ? 'Active' : 'Expired' ?></td></tr>
        </table>
        <h2>Files</h2>
        <p>Bill Copy: <a href="<?= $data['Bill_Copy_Path'] ?>" target="_blank">View</a></p>
        <p>Product Photo: <a href="<?= $data['Product_Photo_Path'] ?>" target="_blank">View</a></p>
    </div>

    <!-- Claim History -->
    <div class="panel">
        <h2>Claim History</h2>
        <ul class="claims-list">
            <?php if ($claims_result && mysqli_num_rows($claims_result) > 0) {
                while ($claim = mysqli_fetch_assoc($claims_result)) { ?>
                    <li>
                        <strong><?= $claim['Claim_Date'] ?></strong> - <?= $claim['Defect_Name'] ?><br>
                        <?= $claim['Remarks'] ?><br>
                        <?php if ($claim['Claim_Image_Path']) { ?>
                            <a href="<?= $claim['Claim_Image_Path'] ?>" target="_blank">View Image</a>
                        <?php } ?>
                    </li>
            <?php } } else { echo "<li>No claims filed yet.</li>"; } ?>
        </ul>
    </div>
</div>

</body>
</html>

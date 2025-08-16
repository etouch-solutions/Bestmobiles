<?php
include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id'])) {
    die("Insurance ID not provided.");
}

$insuranceId = intval($_GET['id']);

// Fetch Insurance + Customer + Brand + Insurance Plan
$query = "
    SELECT ie.*, 
           cm.Cus_Name, cm.Cus_CNo, cm.Cus_Email, cm.Cus_Address, 
           bm.Brand_Name, 
           im.Insurance_Name, im.Duration_Months, im.Premium_Percentage
    FROM Insurance_Entry ie
    LEFT JOIN Customer_Master cm ON ie.Cus_Id = cm.Cus_Id
    LEFT JOIN Brand_Master bm ON ie.Brand_Id = bm.Brand_Id
    LEFT JOIN Insurance_Master im ON ie.Insurance_Id = im.Insurance_Id
    WHERE ie.Ins_Entry_Id = $insuranceId
";

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Insurance record not found.");
}

$insurance = mysqli_fetch_assoc($result);

// Fetch Claim History
$claimQuery = "
    SELECT ce.*, cd.Defect_Name 
    FROM Claim_Entry ce
    LEFT JOIN Claim_Defects cd ON ce.Defect_Id = cd.Defect_Id
    WHERE ce.Ins_Entry_Id = $insuranceId
    ORDER BY ce.Created_At DESC
";
$claimResult = mysqli_query($conn, $claimQuery);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Insurance</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; }
        h2 { margin-top: 0; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        img { max-width: 200px; display: block; margin-top: 10px; }
    </style>
</head>
<body>

    <h1>Insurance & Claim Details</h1>

    <!-- Customer Details -->
    <div class="section">
        <h2>Customer Details</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($insurance['Cus_Name'] ?? '') ?></p>
        <p><strong>Contact:</strong> <?= htmlspecialchars($insurance['Cus_CNo'] ?? '') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($insurance['Cus_Email'] ?? '') ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($insurance['Cus_Address'] ?? '') ?></p>
    </div>

    <!-- Insurance Details -->
    <div class="section">
        <h2>Insurance Details</h2>
        <p><strong>Insurance Plan:</strong> <?= htmlspecialchars($insurance['Insurance_Name'] ?? '') ?></p>
        <p><strong>Duration:</strong> <?= htmlspecialchars($insurance['Duration_Months'] ?? '') ?> months</p>
        <p><strong>Premium %:</strong> <?= htmlspecialchars($insurance['Premium_Percentage'] ?? '') ?>%</p>
        <p><strong>Premium Amount:</strong> ₹<?= number_format($insurance['Premium_Amount'] ?? 0, 2) ?></p>
        <p><strong>Start Date:</strong> <?= htmlspecialchars($insurance['Insurance_Start_Date'] ?? '') ?></p>
        <p><strong>End Date:</strong> <?= htmlspecialchars($insurance['Insurance_End_Date'] ?? '') ?></p>
    </div>

    <!-- Product Details -->
    <div class="section">
        <h2>Product Details</h2>
        <p><strong>Brand:</strong> <?= htmlspecialchars($insurance['Brand_Name'] ?? '') ?></p>
        <p><strong>Model:</strong> <?= htmlspecialchars($insurance['Product_Model_Name'] ?? '') ?></p>
        <p><strong>IMEI 1:</strong> <?= htmlspecialchars($insurance['IMEI_1'] ?? '') ?></p>
        <p><strong>IMEI 2:</strong> <?= htmlspecialchars($insurance['IMEI_2'] ?? '') ?></p>
        <p><strong>Product Value:</strong> ₹<?= number_format($insurance['Product_Value'] ?? 0, 2) ?></p>
        <p><strong>Bill Date:</strong> <?= htmlspecialchars($insurance['Bill_Date'] ?? '') ?></p>

        <p><strong>Bill Copy:</strong></p>
        <?php if (!empty($insurance['Bill_Copy_Path'])): ?>
            <img src="<?= htmlspecialchars($insurance['Bill_Copy_Path']) ?>" alt="Bill Copy">
        <?php else: ?>
            <em>No Bill Copy Uploaded</em>
        <?php endif; ?>

        <p><strong>Product Photo:</strong></p>
        <?php if (!empty($insurance['Product_Photo_Path'])): ?>
            <img src="<?= htmlspecialchars($insurance['Product_Photo_Path']) ?>" alt="Product Photo">
        <?php else: ?>
            <em>No Product Photo Uploaded</em>
        <?php endif; ?>
    </div>

    <!-- Claim History -->
    <div class="section">
        <h2>Claim History</h2>
        <?php if ($claimResult && mysqli_num_rows($claimResult) > 0): ?>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Defect</th>
                    <th>Defect Value</th>
                    <th>Remarks</th>
                    <th>Claim Remarks</th>
                    <th>Image</th>
                </tr>
                <?php while ($claim = mysqli_fetch_assoc($claimResult)): ?>
                    <tr>
                        <td><?= htmlspecialchars($claim['Created_At'] ?? '') ?></td>
                        <td><?= htmlspecialchars($claim['Claim_Status'] ?? '') ?></td>
                        <td><?= htmlspecialchars($claim['Defect_Name'] ?? '') ?></td>
                        <td>₹<?= number_format($claim['Defect_Value'] ?? 0, 2) ?></td>
                        <td><?= htmlspecialchars($claim['Remarks'] ?? '') ?></td>
                        <td><?= htmlspecialchars($claim['Claim_Remarks'] ?? '') ?></td>
                        <td>
                            <?php if (!empty($claim['Claim_Image_Path'])): ?>
                                <img src="<?= htmlspecialchars($claim['Claim_Image_Path']) ?>" alt="Claim Image">
                            <?php else: ?>
                                <em>No Image</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No claims found for this insurance.</p>
        <?php endif; ?>
    </div>

</body>
</html>

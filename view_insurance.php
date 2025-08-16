<?php
include 'db.php';

if (!isset($_GET['id'])) {
    die("No insurance entry selected.");
}

$insurance_entry_id = intval($_GET['id']);

// Fetch Insurance Entry with Customer & Plan Details
$sql = "
SELECT 
    ie.*, 
    c.Cus_Name, c.Cus_CNo, c.Cus_Address, c.Cus_Email, c.Cus_Photo_Path, 
    b.Branch_Name,
    br.Brand_Name,
    ip.Insurance_Name, ip.Duration_Months, ip.Premium_Percentage,
    s.Staff_Name
FROM Insurance_Entry ie
LEFT JOIN Customer_Master c ON ie.Cus_Id = c.Cus_Id
LEFT JOIN Branch_Master b ON c.Branch_Id = b.Branch_Id
LEFT JOIN Brand_Master br ON ie.Brand_Id = br.Brand_Id
LEFT JOIN Insurance_Master ip ON ie.Insurance_Id = ip.Insurance_Id
LEFT JOIN Staff_Master s ON ie.Staff_Id = s.Staff_Id
WHERE ie.Insurance_Entry_Id = '$insurance_entry_id'
";

$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Insurance entry not found.");
}
$insurance = mysqli_fetch_assoc($result);

// Fetch Claim History
$sql_claims = "
SELECT 
    ce.Claim_Id, 
    ce.Claim_Date, 
    ce.Claim_Status, 
    ce.Remarks, 
    ce.Defect_Value,
    ce.Uploaded_Image_Path,
    ce.Claim_Image_Path,
    ce.Claim_Remarks,
    d.Defect_Name
FROM Claim_Entry ce
LEFT JOIN Claim_Defects d ON ce.Defect_Id = d.Defect_Id
WHERE ce.Insurance_Entry_Id = '$insurance_entry_id'
ORDER BY ce.Claim_Date DESC
";

$claims_result = mysqli_query($conn, $sql_claims);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insurance & Claim Details</title>
    <style>
        body { font-family: Arial, sans-serif; margin:20px; background:#f9f9f9; }
        .card { background:#fff; padding:20px; margin-bottom:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
        h2 { margin-top:0; }
        img { border-radius:6px; margin:5px 0; }
        .claims { margin-top:20px; }
        table { width:100%; border-collapse:collapse; background:#fff; }
        th, td { padding:10px; border:1px solid #ddd; text-align:left; }
        th { background:#f0f0f0; }
    </style>
</head>
<body>

<div class="card">
    <h2>Customer Details</h2>
    <p><strong>Name:</strong> <?= htmlspecialchars($insurance['Cus_Name']) ?></p>
    <p><strong>Contact:</strong> <?= htmlspecialchars($insurance['Cus_CNo']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($insurance['Cus_Email']) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($insurance['Cus_Address']) ?></p>
    <?php if (!empty($insurance['Cus_Photo_Path'])): ?>
        <img src="<?= $insurance['Cus_Photo_Path'] ?>" width="120">
    <?php endif; ?>
</div>

<div class="card">
    <h2>Insurance Details</h2>
    <p><strong>Insurance Plan:</strong> <?= htmlspecialchars($insurance['Insurance_Name']) ?></p>
    <p><strong>Duration:</strong> <?= htmlspecialchars($insurance['Duration_Months']) ?> months</p>
    <p><strong>Premium %:</strong> <?= htmlspecialchars($insurance['Premium_Percentage']) ?>%</p>
    <p><strong>Premium Amount:</strong> ₹<?= htmlspecialchars($insurance['Premium_Amount']) ?></p>
    <p><strong>Start Date:</strong> <?= htmlspecialchars($insurance['Insurance_Start_Date']) ?></p>
    <p><strong>End Date:</strong> <?= htmlspecialchars($insurance['Insurance_End_Date']) ?></p>
</div>

<div class="card">
    <h2>Product Details</h2>
    <p><strong>Brand:</strong> <?= htmlspecialchars($insurance['Brand_Name']) ?></p>
    <p><strong>Model:</strong> <?= htmlspecialchars($insurance['Product_Model_Name']) ?></p>
    <p><strong>IMEI 1:</strong> <?= htmlspecialchars($insurance['IMEI_1']) ?></p>
    <p><strong>IMEI 2:</strong> <?= htmlspecialchars($insurance['IMEI_2']) ?></p>
    <p><strong>Product Value:</strong> ₹<?= htmlspecialchars($insurance['Product_Value']) ?></p>
    <p><strong>Bill Date:</strong> <?= htmlspecialchars($insurance['Bill_Date']) ?></p>
    <?php if (!empty($insurance['Bill_Copy_Path'])): ?>
        <p><strong>Bill Copy:</strong><br><img src="<?= $insurance['Bill_Copy_Path'] ?>" width="200"></p>
    <?php endif; ?>
    <?php if (!empty($insurance['Product_Photo_Path'])): ?>
        <p><strong>Product Photo:</strong><br><img src="<?= $insurance['Product_Photo_Path'] ?>" width="200"></p>
    <?php endif; ?>
</div>

<div class="card claims">
    <h2>Claim History</h2>
    <?php if (mysqli_num_rows($claims_result) > 0): ?>
        <table>
            <tr>
                <th>Date</th>
                <th>Status</th>
                <th>Defect</th>
                <th>Defect Value</th>
                <th>Remarks</th>
                <th>Claim Remarks</th>
                <th>Images</th>
            </tr>
            <?php while ($claim = mysqli_fetch_assoc($claims_result)): ?>
                <tr>
                    <td><?= htmlspecialchars($claim['Claim_Date']) ?></td>
                    <td><?= htmlspecialchars($claim['Claim_Status']) ?></td>
                    <td><?= htmlspecialchars($claim['Defect_Name']) ?></td>
                    <td>₹<?= htmlspecialchars($claim['Defect_Value']) ?></td>
                    <td><?= htmlspecialchars($claim['Remarks']) ?></td>
                    <td><?= htmlspecialchars($claim['Claim_Remarks']) ?></td>
                    <td>
                        <?php if (!empty($claim['Uploaded_Image_Path'])): ?>
                            <img src="<?= $claim['Uploaded_Image_Path'] ?>" width="80">
                        <?php endif; ?>
                        <?php if (!empty($claim['Claim_Image_Path'])): ?>
                            <img src="<?= $claim['Claim_Image_Path'] ?>" width="80">
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No claims filed for this insurance.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php
include 'db.php';

if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$insuranceId = intval($_GET['id']);

// Fetch Insurance Details with Customer, Brand, Insurance Plan
$query = "
    SELECT 
        ie.*,
        c.Cus_Name, c.Cus_CNo, c.Cus_Address, c.Cus_Email, c.Cus_Ref_Name, c.Cus_Ref_CNo,
        b.Brand_Name,
        ins.Insurance_Name, ins.Duration_Months, ins.Premium_Percentage
    FROM Insurance_Entry ie
    LEFT JOIN Customer_Master c ON ie.Cus_Id = c.Cus_Id
    LEFT JOIN Brand_Master b ON ie.Brand_Id = b.Brand_Id
    LEFT JOIN Insurance_Master ins ON ie.Insurance_Id = ins.Insurance_Id
    WHERE ie.Insurance_Entry_Id = '$insuranceId'
";

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Insurance record not found.");
}

$insurance = mysqli_fetch_assoc($result);

// Fetch Claim History for this insurance entry
$claimsQuery = "
    SELECT 
        ce.*,
        d.Defect_Name,
        s.Staff_Name
    FROM Claim_Entry ce
    LEFT JOIN Defect_Master d ON ce.Defect_Id = d.Defect_Id
    LEFT JOIN Staff_Master s ON ce.Staff_Id = s.Staff_Id
    WHERE ce.Insurance_Entry_Id = '$insuranceId'
    ORDER BY ce.Created_At DESC
";

$claimsResult = mysqli_query($conn, $claimsQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insurance Details</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { color: #2c3e50; }
        .section { margin-bottom: 30px; }
        .card { border: 1px solid #ccc; padding: 15px; border-radius: 5px; background: #f9f9f9; margin-bottom: 10px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
        img { max-width: 200px; margin: 10px 0; border: 1px solid #ccc; }
    </style>
</head>
<body>

    <h2>Insurance Details</h2>
    <div class="section card">
        <h3>Customer Details</h3>
        <p><b>Name:</b> <?= $insurance['Cus_Name'] ?></p>
        <p><b>Contact:</b> <?= $insurance['Cus_CNo'] ?></p>
        <p><b>Email:</b> <?= $insurance['Cus_Email'] ?></p>
        <p><b>Address:</b> <?= $insurance['Cus_Address'] ?></p>
        <p><b>Reference:</b> <?= $insurance['Cus_Ref_Name'] ?> (<?= $insurance['Cus_Ref_CNo'] ?>)</p>
    </div>

    <div class="section card">
        <h3>Product / Insurance Details</h3>
        <p><b>Brand:</b> <?= $insurance['Brand_Name'] ?></p>
        <p><b>Model:</b> <?= $insurance['Product_Model_Name'] ?></p>
        <p><b>IMEI 1:</b> <?= $insurance['IMEI_1'] ?></p>
        <p><b>IMEI 2:</b> <?= $insurance['IMEI_2'] ?></p>
        <p><b>Product Value:</b> <?= $insurance['Product_Value'] ?></p>
        <p><b>Insurance Plan:</b> <?= $insurance['Insurance_Name'] ?> (<?= $insurance['Duration_Months'] ?> months)</p>
        <p><b>Premium Amount:</b> <?= $insurance['Premium_Amount'] ?></p>
        <p><b>Bill Date:</b> <?= $insurance['Bill_Date'] ?></p>
        <p><b>Start Date:</b> <?= $insurance['Insurance_Start_Date'] ?></p>
        <p><b>End Date:</b> <?= $insurance['Insurance_End_Date'] ?></p>
        <p><b>Status:</b> <?= $insurance['Is_Insurance_Active'] ? 'Active' : 'Inactive' ?></p>

        <?php if ($insurance['Bill_Copy_Path']): ?>
            <p><b>Bill Copy:</b><br><img src="<?= $insurance['Bill_Copy_Path'] ?>" alt="Bill Copy"></p>
        <?php endif; ?>

        <?php if ($insurance['Product_Photo_Path']): ?>
            <p><b>Product Photo:</b><br><img src="<?= $insurance['Product_Photo_Path'] ?>" alt="Product Photo"></p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h3>Claim History</h3>
        <?php if (mysqli_num_rows($claimsResult) > 0): ?>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Defect</th>
                    <th>Remarks</th>
                    <th>Claim Status</th>
                    <th>Claimed Amount</th>
                    <th>Staff</th>
                    <th>Image</th>
                </tr>
                <?php while ($claim = mysqli_fetch_assoc($claimsResult)): ?>
                <tr>
                    <td><?= $claim['Claim_Date'] ?></td>
                    <td><?= $claim['Defect_Name'] ?></td>
                    <td><?= $claim['Remarks'] ?></td>
                    <td><?= $claim['Claim_Status'] ?></td>
                    <td><?= $claim['Defect_Value'] ?></td>
                    <td><?= $claim['Staff_Name'] ?></td>
                    <td>
                        <?php if ($claim['Claim_Image_Path']): ?>
                            <img src="<?= $claim['Claim_Image_Path'] ?>" alt="Claim Image" style="max-width:100px;">
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No claims filed for this insurance yet.</p>
        <?php endif; ?>
    </div>

</body>
</html>

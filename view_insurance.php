<?php
include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("Invalid insurance ID.");
}

$id = (int) $_GET['id'];

$res = mysqli_query($conn, "
  SELECT 
    i.*,
    c.Cus_Name, c.Cus_CNo, c.Cus_Email, c.Cus_Address,
    c.Cus_Ref_Name, c.Cus_Ref_CNo,
    b.Branch_Name,
    ins.Insurance_Name
  FROM Insurance_Entry i
  JOIN Customer_Master c ON i.Cus_Id = c.Cus_Id
  JOIN Branch_Master b ON c.Branch_Id = b.Branch_Id
  JOIN Insurance_Master ins ON i.Insurance_Id = ins.Insurance_Id
  WHERE i.Insurance_Entry_Id = $id
");

if (!$row = mysqli_fetch_assoc($res)) {
  die("Insurance record not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Insurance Details</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 30px; }
    .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    h2 { margin-bottom: 20px; }
    .section { margin-bottom: 20px; }
    .label { font-weight: bold; }
    .image-preview { max-width: 200px; border: 1px solid #ccc; margin-top: 10px; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Insurance & Customer Details</h2>

    <div class="section">
      <h3>Customer Info</h3>
      <p><span class="label">Name:</span> <?= $row['Cus_Name'] ?></p>
      <p><span class="label">Contact:</span> <?= $row['Cus_CNo'] ?></p>
      <p><span class="label">Email:</span> <?= $row['Cus_Email'] ?></p>
      <p><span class="label">Address:</span> <?= $row['Cus_Address'] ?></p>
      <p><span class="label">Reference Name:</span> <?= $row['Cus_Ref_Name'] ?> | <b>Ref Contact:</b> <?= $row['Cus_Ref_CNo'] ?></p>
      <p><span class="label">Branch:</span> <?= $row['Branch_Name'] ?></p>
    </div>

    <div class="section">
      <h3>Insurance Info</h3>
      <p><span class="label">Insurance Plan:</span> <?= $row['Insurance_Name'] ?></p>
      <p><span class="label">IMEI 1:</span> <?= $row['IMEI_1'] ?> | <b>IMEI 2:</b> <?= $row['IMEI_2'] ?></p>
      <p><span class="label">Model:</span> <?= $row['Product_Model_Name'] ?></p>
      <p><span class="label">Product Value:</span> ₹<?= $row['Product_Value'] ?></p>
      <p><span class="label">Premium:</span> ₹<?= $row['Premium_Amount'] ?></p>
      <p><span class="label">Start Date:</span> <?= $row['Insurance_Start_Date'] ?> | <b>End Date:</b> <?= $row['Insurance_End_Date'] ?></p>
      <p><span class="label">Covered:</span> <?= $row['Is_Product_Covered'] ? 'Yes' : 'No' ?> | <b>Status:</b> <?= $row['Is_Insurance_Active'] ? 'Active' : 'Inactive' ?></p>
    </div>

    <div class="section">
      <h3>Uploaded Documents</h3>
      <p><span class="label">Bill Copy:</span><br>
        <?php if (!empty($row['Bill_Copy_Path'])): ?>
          <img src="<?= $row['Bill_Copy_Path'] ?>" class="image-preview">
        <?php else: ?>
          No bill uploaded.
        <?php endif; ?>
      </p>
      <p><span class="label">Product Photo:</span><br>
        <?php if (!empty($row['Product_Photo_Path'])): ?>
          <img src="<?= $row['Product_Photo_Path'] ?>" class="image-preview">
        <?php else: ?>
          No product photo uploaded.
        <?php endif; ?>
      </p>
    </div>

    <a href="serch.php">← Back to Insurance List</a>
  </div>
</body>
</html>

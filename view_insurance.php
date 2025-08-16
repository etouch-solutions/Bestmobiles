<?php
include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("Invalid insurance ID.");
}

$id = (int) $_GET['id'];

// Fetch Insurance + Customer Details
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

// Fetch Claim History
$claims = mysqli_query($conn, "
  SELECT 
    ce.Claim_Id,
    ce.Claim_Date,
    ce.Defect_Value,
    d.Defect_Name
  FROM Claim_Entry ce
  JOIN Defect_Master d ON ce.Defect_Id = d.Defect_Id
  WHERE ce.Insurance_Entry_Id = $id
  ORDER BY ce.Claim_Date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Insurance Details</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 30px;
    }
    .container {
      max-width: 1000px;
      margin: auto;
    }
    .card {
      background: white;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    h2, h3 {
      margin-top: 0;
      color: #2c3e50;
    }
    .info p {
      margin: 6px 0;
    }
    .label {
      font-weight: bold;
      color: #555;
    }
    .image-preview {
      max-width: 200px;
      border: 1px solid #ddd;
      border-radius: 8px;
      margin-top: 8px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    table th, table td {
      padding: 10px;
      border: 1px solid #eee;
      text-align: left;
    }
    table th {
      background: #f8f9fb;
    }
    .btn-back {
      display: inline-block;
      margin-top: 15px;
      padding: 10px 16px;
      background: #3498db;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      transition: 0.2s;
    }
    .btn-back:hover {
      background: #2980b9;
    }
    @media (max-width: 768px) {
      body { padding: 15px; }
      .image-preview { max-width: 100%; }
      table { font-size: 14px; }
    }
  </style>
</head>
<body>
  <div class="container">

    <div class="card">
      <h2>Insurance & Customer Details</h2>

      <div class="info">
        <h3>👤 Customer Info</h3>
        <p><span class="label">Name:</span> <?= $row['Cus_Name'] ?></p>
        <p><span class="label">Contact:</span> <?= $row['Cus_CNo'] ?></p>
        <p><span class="label">Email:</span> <?= $row['Cus_Email'] ?></p>
        <p><span class="label">Address:</span> <?= $row['Cus_Address'] ?></p>
        <p><span class="label">Reference:</span> <?= $row['Cus_Ref_Name'] ?> (<?= $row['Cus_Ref_CNo'] ?>)</p>
        <p><span class="label">Branch:</span> <?= $row['Branch_Name'] ?></p>
      </div>
    </div>

    <div class="card">
      <h3>📄 Insurance Info</h3>
      <p><span class="label">Plan:</span> <?= $row['Insurance_Name'] ?></p>
      <p><span class="label">IMEI 1:</span> <?= $row['IMEI_1'] ?> | <b>IMEI 2:</b> <?= $row['IMEI_2'] ?></p>
      <p><span class="label">Model:</span> <?= $row['Product_Model_Name'] ?></p>
      <p><span class="label">Product Value:</span> ₹<?= $row['Product_Value'] ?></p>
      <p><span class="label">Premium:</span> ₹<?= $row['Premium_Amount'] ?></p>
      <p><span class="label">Coverage:</span> <?= $row['Is_Product_Covered'] ? 'Yes' : 'No' ?></p>
      <p><span class="label">Status:</span> <?= $row['Is_Insurance_Active'] ? 'Active ✅' : 'Inactive ❌' ?></p>
      <p><span class="label">Start Date:</span> <?= $row['Insurance_Start_Date'] ?> | 
         <b>End Date:</b> <?= $row['Insurance_End_Date'] ?></p>
    </div>

    <div class="card">
      <h3>📷 Uploaded Documents</h3>
      <p><span class="label">Bill Copy:</span><br>
        <?php if (!empty($row['Bill_Copy_Path'])): ?>
          <img src="<?= $row['Bill_Copy_Path'] ?>" class="image-preview">
        <?php else: ?>
          ❌ No bill uploaded.
        <?php endif; ?>
      </p>
      <p><span class="label">Product Photo:</span><br>
        <?php if (!empty($row['Product_Photo_Path'])): ?>
          <img src="<?= $row['Product_Photo_Path'] ?>" class="image-preview">
        <?php else: ?>
          ❌ No product photo uploaded.
        <?php endif; ?>
      </p>
    </div>

    <div class="card">
      <h3>📜 Claim History</h3>
      <?php if (mysqli_num_rows($claims) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Claim Date</th>
              <th>Defect</th>
              <th>Defect Value (₹)</th>
            </tr>
          </thead>
          <tbody>
            <?php $i = 1; while ($c = mysqli_fetch_assoc($claims)): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td>
                  <?php 
                    if (!empty($c['Claim_Date']) && $c['Claim_Date'] != '0000-00-00') {
                      echo date("d-M-Y", strtotime($c['Claim_Date']));
                    } else {
                      echo "N/A";
                    }
                  ?>
                </td>
                <td><?= $c['Defect_Name'] ?></td>
                <td>₹<?= number_format($c['Defect_Value'], 2) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No claims made for this insurance yet.</p>
      <?php endif; ?>
    </div>

    <a href="serch.php" class="btn-back">← Back to Insurance List</a>
  </div>
</body>
</html>

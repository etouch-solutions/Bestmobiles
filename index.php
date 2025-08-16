<?php
// dashboard.php
// Database connection
 
include 'db.php'; 
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch counts
$totalCustomers = $conn->query("SELECT COUNT(*) as cnt FROM Customer_Master")->fetch_assoc()['cnt'];
$totalInsurance = $conn->query("SELECT COUNT(*) as cnt FROM Insurance_Entry")->fetch_assoc()['cnt'];
$totalClaims    = $conn->query("SELECT COUNT(*) as cnt FROM Claim_Entry")->fetch_assoc()['cnt'];
$totalPlans     = $conn->query("SELECT COUNT(*) as cnt FROM Insurance_Master")->fetch_assoc()['cnt'];

// Latest 5 customers
$latestCustomers = $conn->query("SELECT Cus_Name, Cus_CNo, Created_At FROM Customer_Master ORDER BY Created_At DESC LIMIT 5");

// Latest 5 insurance entries
$latestInsurance = $conn->query("SELECT ie.Ins_Entry_Id, cm.Cus_Name, im.Insurance_Name, ie.Created_At 
    FROM Insurance_Entry ie
    JOIN Customer_Master cm ON ie.Cus_Id = cm.Cus_Id
    JOIN Insurance_Master im ON ie.Insurance_Id = im.Insurance_Id
    ORDER BY ie.Created_At DESC LIMIT 5");

// Latest 5 claims
$latestClaims = $conn->query("SELECT ce.Claim_Id, cm.Cus_Name, ce.Created_At 
    FROM Claim_Entry ce
    JOIN Customer_Master cm ON ce.Cus_Id = cm.Cus_Id
    ORDER BY ce.Created_At DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>IMS Dashboard</title>
  <style>
    body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f5f6fa; }
    header { background:#2c3e50; color:white; padding:15px; text-align:center; font-size:24px; }
    .container { padding:20px; }
    .cards { display:grid; grid-template-columns: repeat(4, 1fr); gap:20px; margin-bottom:30px; }
    .card { background:white; padding:20px; border-radius:10px; text-align:center; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    .card h2 { margin:0; font-size:28px; color:#2c3e50; }
    .card p { margin:5px 0 0; color:#7f8c8d; }
    h3 { margin-top:40px; margin-bottom:10px; color:#2c3e50; }
    table { width:100%; border-collapse:collapse; background:white; border-radius:10px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    table th, table td { padding:10px; border-bottom:1px solid #ddd; text-align:left; }
    table th { background:#ecf0f1; }
    table tr:hover { background:#f9f9f9; }
  </style>
</head>
<body>
<header>Insurance Management System - Dashboard</header>
<div class="container">

  <div class="cards">
    <div class="card">
      <h2><?= $totalCustomers ?></h2>
      <p>Total Customers</p>
    </div>
    <div class="card">
      <h2><?= $totalInsurance ?></h2>
      <p>Total Insurance Entries</p>
    </div>
    <div class="card">
      <h2><?= $totalClaims ?></h2>
      <p>Total Claims</p>
    </div>
    <div class="card">
      <h2><?= $totalPlans ?></h2>
      <p>Total Insurance Plans</p>
    </div>
  </div>

  <h3>Latest Customers</h3>
  <table>
    <tr><th>Name</th><th>Phone</th><th>Created At</th></tr>
    <?php while($row = $latestCustomers->fetch_assoc()): ?>
    <tr>
      <td><?= $row['Cus_Name'] ?></td>
      <td><?= $row['Cus_CNo'] ?></td>
      <td><?= $row['Created_At'] ?></td>
    </tr>
    <?php endwhile; ?>
  </table>

  <h3>Latest Insurance Entries</h3>
  <table>
    <tr><th>ID</th><th>Customer</th><th>Plan</th><th>Created At</th></tr>
    <?php while($row = $latestInsurance->fetch_assoc()): ?>
    <tr>
      <td><?= $row['Ins_Entry_Id'] ?></td>
      <td><?= $row['Cus_Name'] ?></td>
      <td><?= $row['Insurance_Name'] ?></td>
      <td><?= $row['Created_At'] ?></td>
    </tr>
    <?php endwhile; ?>
  </table>

  <h3>Latest Claims</h3>
  <table>
    <tr><th>ID</th><th>Customer</th><th>Created At</th></tr>
    <?php while($row = $latestClaims->fetch_assoc()): ?>
    <tr>
      <td><?= $row['Claim_Id'] ?></td>
      <td><?= $row['Cus_Name'] ?></td>
      <td><?= $row['Created_At'] ?></td>
    </tr>
    <?php endwhile; ?>
  </table>

</div>
</body>
</html>

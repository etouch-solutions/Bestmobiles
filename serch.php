<?php
include 'db.php';

function fetch_insurance_entries($conn, $q = '') {
  $q = mysqli_real_escape_string($conn, $q);
  $where = $q ? "WHERE c.Cus_Name LIKE '%$q%' OR c.Cus_CNo LIKE '%$q%' OR i.IMEI_1 LIKE '%$q%'" : '';

  $res = mysqli_query($conn, "
    SELECT 
      i.Insurance_Entry_Id,
      c.Cus_Name AS name,
      c.Cus_CNo AS phone,
      i.Product_Model_Name AS model,
      i.IMEI_1 AS imei,
      i.Product_Value,
      i.Insurance_Start_Date,
      i.Insurance_End_Date,
      (
        SELECT IFNULL(SUM(dv.Defect_Value), 0)
        FROM Claim_Defect_Value dv
        JOIN Claim_Entry ce ON ce.Claim_Id = dv.Claim_Id
        WHERE ce.Insurance_Entry_Id = i.Insurance_Entry_Id
      ) AS Defect_Value,
      (
        SELECT COUNT(*) FROM Claim_Entry ce 
        WHERE ce.Insurance_Entry_Id = i.Insurance_Entry_Id
      ) AS claim_count
    FROM Insurance_Entry i
    JOIN Customer_Master c ON c.Cus_Id = i.Cus_Id
    $where
    ORDER BY i.Insurance_Entry_Id DESC
  ");

  $data = [];
  $today = date('Y-m-d');
  while ($row = mysqli_fetch_assoc($res)) {
    $row['status'] = ($today > $row['Insurance_End_Date']) ? 'expired' : ($row['claim_count'] > 0 ? 'claimed' : 'not_claimed');
    $data[] = $row;
  }
  return $data;
}

if (isset($_GET['q'])) {
  header('Content-Type: application/json');
  echo json_encode(fetch_insurance_entries($conn, $_GET['q']));
  exit;
}
$allData = fetch_insurance_entries($conn);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Insurance & Claim History</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f7fa;
    }
    .navtop {
      background: #fff;
      padding: 15px 20px;
      border-bottom: 1px solid #ddd;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
    }
    .navtop h1 { margin: 0; font-size: 20px; font-weight: 600; }
    .sidebar {
      width: 200px;
      position: fixed;
      top: 60px;
      left: 0;
      background-color: #fff;
      height: 100%;
      padding: 20px 10px;
      border-right: 1px solid #ddd;
    }
    .sidebar a {
      display: block;
      padding: 10px;
      text-decoration: none;
      color: #333;
      border-radius: 6px;
      margin-bottom: 5px;
    }
    .sidebar a:hover, .sidebar a.active {
      background: #3498db;
      color: white;
    }
    .main-content {
      margin-left: 220px;
      padding: 100px 30px 30px 30px;
    }
    h2 { margin-top: 0; }
    .search-bar {
      padding: 12px 15px;
      font-size: 16px;
      width: 100%;
      max-width: 500px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      border-radius: 8px;
      overflow: hidden;
    }
    th, td {
      padding: 12px 15px;
      border-bottom: 1px solid #f0f0f0;
      text-align: left;
    }
    th { background-color: #f9f9f9; font-weight: bold; }
    tr.expired { background-color: #ffe6e6; }
    tr.claimed { background-color: #fff8e1; }
    tr.not_claimed { background-color: #e8f5e9; }
    .action-btn {
      padding: 6px 12px;
      background: #3498db;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin-right: 5px;
    }
    .action-btn:hover { background: #2980b9; }
    @media (max-width: 768px) {
      .sidebar { display: none; }
      .main-content { margin-left: 0; padding: 80px 20px 20px; }
      .search-bar { width: 100%; }
    }
  </style>
</head>
<body>

<!-- Header -->
<div class="navtop">
  <div class="logo"><strong>LOGO</strong></div>
  <h1>Best Mobile Insurance Software</h1>
  <div></div>
</div>

<!-- Sidebar -->
<aside class="sidebar mobile-hidden" id="sidebarMenu">
  <ul>
    <a href="index.php"><li>Dashboard</li></a>
    <a href="branch.php"><li>Branch Master</li></a>
    <a href="brand.php"><li>Brand Master</li></a>
    <a href="add_staff.php"><li>Staff Master</li></a>
    <a href="Customer_Master.php"><li>Customer Master</li></a>
    <a href="add_insurance.php"><li>Insurance Master</li></a>
    <a href="add_defect.php"><li>Defect Master</li></a>
    <a href="insuranceentry.php"><li>Insurance Entry</li></a>
    <a href="serch.php" class="active"><li>Claim</li></a>
  </ul>
</aside>

<!-- Main Content -->
<div class="main-content">
  <h2>Insurance & Claim History</h2>

  <input type="text" id="search" class="search-bar" placeholder="Search by Name, Phone, or IMEI...">

  <div style="overflow-x:auto;">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Phone</th>
          <th>Model</th>
          <th>IMEI</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Claims</th>
          <th>Total Defect Value</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="results">
        <?php foreach ($allData as $item): ?>
        <tr class="<?= $item['status'] ?>">
          <td><?= $item['name'] ?></td>
          <td><?= $item['phone'] ?></td>
          <td><?= $item['model'] ?></td>
          <td><?= $item['imei'] ?></td>
          <td><?= $item['Insurance_Start_Date'] ?></td>
          <td><?= $item['Insurance_End_Date'] ?></td>
          <td><?= $item['claim_count'] ?></td>
          <td>₹<?= number_format($item['Defect_Value'], 2) ?></td>
          <td>
            <button class="action-btn" onclick="location.href='view_insurance.php?id=<?= $item['Insurance_Entry_Id'] ?>'">View</button>
            <button class="action-btn" onclick="location.href='clamentry-form.php?insurance_id=<?= $item['Insurance_Entry_Id'] ?>'">Claim</button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.getElementById('search').addEventListener('input', function () {
  const query = this.value.trim();

  fetch('?q=' + encodeURIComponent(query))
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('results');
      container.innerHTML = '';

      data.forEach(item => {
        let row = document.createElement('tr');
        row.className = item.status;

        row.innerHTML = `
          <td>${item.name}</td>
          <td>${item.phone}</td>
          <td>${item.model}</td>
          <td>${item.imei}</td>
          <td>${item.Insurance_Start_Date}</td>
          <td>${item.Insurance_End_Date}</td>
          <td>${item.claim_count}</td>
          <td>₹${parseFloat(item.Defect_Value).toFixed(2)}</td>
          <td>
            <button class="action-btn" onclick="location.href='view_insurance.php?id=${item.Insurance_Entry_Id}'">View</button>
            <button class="action-btn" onclick="location.href='clamentry-form.php?insurance_id=${item.Insurance_Entry_Id}'">Claim</button>
          </td>
        `;
        container.appendChild(row);
      });

      if (data.length === 0) {
        container.innerHTML = '<tr><td colspan="9">No insurance found.</td></tr>';
      }
    });
});
</script>
</body>
</html>

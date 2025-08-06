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
    .main-content {
      margin-left: 220px;
      padding: 30px;
      background: #f5f7fa;
      min-height: 90vh;
    }

    .page-title {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .search-bar {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      margin-bottom: 25px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    table {
      width: 100%;
      background: white;
      border-collapse: collapse;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      background: #f1f1f1;
    }

    tr.expired { background-color: #f8d7da; }
    tr.claimed { background-color: #fff3cd; }
    tr.not_claimed { background-color: #d4edda; }

    .action-btn {
      padding: 6px 12px;
      background: #3498db;
      color: #fff;
      border: none;
      border-radius: 5px;
      margin-right: 5px;
      cursor: pointer;
    }

    .action-btn:hover {
      background: #2980b9;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 15px;
      }

      table, th, td {
        font-size: 14px;
      }
    }
  </style>
</head>
<body>

<!-- Header -->
<div class="navtop">
  <div class="logo">LOGO</div>
  <h1>Best Mobile Insurance Software</h1>
  <div class="hamburger" onclick="toggleSidebar()">☰</div>
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
  <div class="page-title">Insurance & Claim History</div>

  <input type="text" id="search" class="search-bar" placeholder="Search by Name / Phone / IMEI">

  <table>
    <thead>
      <tr>
        <th>Customer Name</th>
        <th>Phone</th>
        <th>Model</th>
        <th>IMEI</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Claims</th>
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
          <td>
            <button class="action-btn" onclick="location.href='view_insurance.php?id=<?= $item['Insurance_Entry_Id'] ?>'">View</button>
            <button class="action-btn" onclick="location.href='clamentry-form.php?insurance_id=<?= $item['Insurance_Entry_Id'] ?>'">Claim</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Search Script -->
<script>
document.getElementById('search').addEventListener('input', function () {
  const query = this.value.trim();
  fetch('?q=' + encodeURIComponent(query))
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('results');
      container.innerHTML = '';
      data.forEach(item => {
        const row = document.createElement('tr');
        row.className = item.status;
        row.innerHTML = `
          <td>${item.name}</td>
          <td>${item.phone}</td>
          <td>${item.model}</td>
          <td>${item.imei}</td>
          <td>${item.Insurance_Start_Date}</td>
          <td>${item.Insurance_End_Date}</td>
          <td>${item.claim_count}</td>
          <td>
            <button class="action-btn" onclick="location.href='view_insurance.php?id=${item.Insurance_Entry_Id}'">View</button>
            <button class="action-btn" onclick="location.href='clamentry-form.php?insurance_id=${item.Insurance_Entry_Id}'">Claim</button>
          </td>
        `;
        container.appendChild(row);
      });
      if (data.length === 0) {
        container.innerHTML = `<tr><td colspan="8">No matching records found.</td></tr>`;
      }
    });
});
</script>

</body>
</html>

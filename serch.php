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
    * { box-sizing: border-box; }
    body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }

    .header {
      
      color: white;
      
      font-size: 20px;
    }

    .sidebar {
       ;
      position: fixed;
      
    }

    .sidebar a {
      display: block;
      
    }

     

    .main-content {
      margin-left: 220px;
      padding: 20px 30px;
      margin-top: 50px;
    }

    h2 {
      margin-bottom: 20px;
    }

    .search-bar {
      width: 100%;
      padding: 12px 15px;
      font-size: 16px;
      margin-bottom: 25px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .card {
      background: white;
      padding: 20px;
      border-left: 6px solid #2ecc71;
      margin-bottom: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
    }

    .card.claimed { border-left-color: #f1c40f; }
    .card.expired { border-left-color: #e74c3c; }

    .card:hover {
      transform: translateY(-2px);
    }

    .card-buttons {
      margin-top: 10px;
    }

    .card-buttons button {
      padding: 8px 15px;
      margin-right: 10px;
      border: none;
      border-radius: 5px;
      background: #3498db;
      color: white;
      cursor: pointer;
      transition: background 0.3s;
    }

    .card-buttons button:hover {
      background: #2980b9;
    }

    @media (max-width: 768px) {
      .sidebar {
        display: none;
      }

      .main-content {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

<div class="navtop">
    <div class="logo">LOGO</div>
    <h1>Best Mobile Insurance Software</h1>
    <div class="hamburger" onclick="toggleSidebar()">☰</div>
  </div>

    <aside class="sidebar mobile-hidden" id="sidebarMenu">
      <ul>
        <a href="index.php"><li>Dashboard</li></a>
        <a href="branch.php" class="active"><li>Branch Master</li></a>
        <a href="brand.php"><li>Brand Master</li></a>
        <a href="add_staff.php"><li>Staff Master</li></a>
        <a href="Customer_Master.php"><li>Customer Master</li></a>
        <a href="add_insurance.php"><li>Insurance Master</li></a>
        <a href="add_defect.php"><li>Defect Master</li></a>
        <a href="insuranceentry.php"><li>Insurance Entry</li></a>
        <a href="serch.php"><li>Claim</li></a>
      </ul>
    </aside>


<div class="main-content">
  <h2>Insurance & Claim History</h2>
  <input type="text" id="search" class="search-bar" placeholder="Search by Name, Phone, or IMEI...">
  <div id="results">
    <?php foreach ($allData as $item): ?>
      <div class="card <?= $item['status'] ?>">
        <b>Name:</b> <?= $item['name'] ?> |
        <b>Phone:</b> <?= $item['phone'] ?><br>
        <b>Model:</b> <?= $item['model'] ?> |
        <b>IMEI:</b> <?= $item['imei'] ?><br>
        <b>Start:</b> <?= $item['Insurance_Start_Date'] ?> |
        <b>End:</b> <?= $item['Insurance_End_Date'] ?><br>
        <b>Total Claims:</b> <?= $item['claim_count'] ?><br>
        <div class="card-buttons">
          <button onclick="location.href='view_insurance.php?id=<?= $item['Insurance_Entry_Id'] ?>'">View</button>
          <button onclick="location.href='clamentry-form.php?insurance_id=<?= $item['Insurance_Entry_Id'] ?>'">Claim</button>
        </div>
      </div>
    <?php endforeach; ?>
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
        let statusClass = item.status;
        let div = document.createElement('div');
        div.className = 'card ' + statusClass;

        div.innerHTML = `
          <b>Name:</b> ${item.name}
          <b>Phone:</b> ${item.phone}
          <b>Model:</b> ${item.model} 
          <b>IMEI:</b> ${item.imei}
          <b>Start:</b> ${item.Insurance_Start_Date} 
          <b>End:</b> ${item.Insurance_End_Date}
          <b>Total Claims:</b> ${item.claim_count}
          <div class="card-buttons">
            <button onclick="location.href='view_insurance.php?id=${item.Insurance_Entry_Id}'">View</button>
            <button onclick="location.href='clamentry-form.php?insurance_id=${item.Insurance_Entry_Id}'">Claim</button>
          </div>
        `;
        container.appendChild(div);
      });

      if (data.length === 0) {
        container.innerHTML = '<p>No results found.</p>';
      }
    });
});
</script>

</body>
</html>

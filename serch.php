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
    .card { border-radius: 10px; padding: 15px; margin-bottom: 15px; color: white; }
    .green { background-color: #28a745; }
    .yellow { background-color: #ffc107; color: black; }
    .red { background-color: #dc3545; }
    .card-buttons button { margin-right: 10px; padding: 5px 15px; }
  </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
  <div class="header">
    <h2>Insurance & Claim History</h2>
  </div>

  <div class="content-panel">
    <input type="text" id="search" class="form-input" placeholder="Search by Name / Phone / IMEI">
    <div id="results">
      <?php foreach ($allData as $item): ?>
        <div class="card <?= $item['status'] === 'expired' ? 'red' : ($item['status'] === 'claimed' ? 'yellow' : 'green') ?>">
          <b>Name:</b> <?= $item['name'] ?> | <b>Phone:</b> <?= $item['phone'] ?><br>
          <b>Model:</b> <?= $item['model'] ?> | <b>IMEI:</b> <?= $item['imei'] ?><br>
          <b>Start:</b> <?= $item['Insurance_Start_Date'] ?> | <b>End:</b> <?= $item['Insurance_End_Date'] ?><br>
          <b>Claims:</b> <?= $item['claim_count'] ?>
          <div class="card-buttons">
            <button onclick="location.href='view_insurance.php?id=<?= $item['Insurance_Entry_Id'] ?>'">View</button>
            <button onclick="location.href='clamentry-form.php?insurance_id=<?= $item['Insurance_Entry_Id'] ?>'">Claim</button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
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
        let color = item.status === 'expired' ? 'red' : item.status === 'claimed' ? 'yellow' : 'green';

        const div = document.createElement('div');
        div.className = 'card ' + color;
        div.innerHTML = `
          <b>Name:</b> ${item.name} | <b>Phone:</b> ${item.phone}<br>
          <b>Model:</b> ${item.model} | <b>IMEI:</b> ${item.imei}<br>
          <b>Start:</b> ${item.Insurance_Start_Date} | <b>End:</b> ${item.Insurance_End_Date}<br>
          <b>Claims:</b> ${item.claim_count}
          <div class="card-buttons">
            <button onclick="location.href='view_insurance.php?id=${item.Insurance_Entry_Id}'">View</button>
            <button onclick="location.href='clamentry-form.php?insurance_id=${item.Insurance_Entry_Id}'">Claim</button>
          </div>
        `;
        container.appendChild(div);
      });

      if (data.length === 0) {
        container.innerHTML = '<p>No insurance found.</p>';
      }
    });
});
</script>
</body>
</html>
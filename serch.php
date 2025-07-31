<?php
include 'db.php';

// Handle AJAX search request for insurance + claim history
if (isset($_GET['q'])) {
  $q = $_GET['q'];
  $data = [];

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
    WHERE c.Cus_Name LIKE '%$q%' OR c.Cus_CNo LIKE '%$q%' OR i.IMEI_1 LIKE '%$q%'
  ");

  while ($row = mysqli_fetch_assoc($res)) {
    $today = date('Y-m-d');
    $row['status'] = ($today > $row['Insurance_End_Date']) ? 'expired' : ($row['claim_count'] > 0 ? 'claimed' : 'not_claimed');
    $data[] = $row;
  }

  header('Content-Type: application/json');
  echo json_encode($data);
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Insurance History</title>
  <style>
    body { font-family: Arial; background: #f5f5f5; padding: 30px; }
    .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    input { width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px; }
    .card { border-radius: 10px; padding: 15px; margin-bottom: 15px; color: white; }
    .green { background-color: #28a745; }
    .yellow { background-color: #ffc107; }
    .red { background-color: #dc3545; }
    .card-buttons button { margin-right: 10px; padding: 5px 15px; }
  </style>
</head>
<body>
<div class="container">
  <h2>Insurance & Claim History</h2>
  <input type="text" id="search" placeholder="Search by Name / Phone / IMEI">
  <div id="results"></div>
</div>

<script>
document.getElementById('search').addEventListener('input', function () {
  const query = this.value;
  if (query.length < 2) return;

  fetch('?q=' + encodeURIComponent(query))
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('results');
      container.innerHTML = '';

      data.forEach(item => {
        const card = document.createElement('div');
        let color = item.status === 'expired' ? 'red' : item.status === 'claimed' ? 'yellow' : 'green';

        card.className = 'card ' + color;
        card.innerHTML = `
          <b>Name:</b> ${item.name} | <b>Phone:</b> ${item.phone}<br>
          <b>Model:</b> ${item.model} | <b>IMEI:</b> ${item.imei}<br>
          <b>Start:</b> ${item.Insurance_Start_Date} | <b>End:</b> ${item.Insurance_End_Date}<br>
          <b>Claims:</b> ${item.claim_count}
          <div class="card-buttons">
            <button onclick="location.href='view_insurance.php?id=${item.Insurance_Entry_Id}'">View</button>
            <button onclick="location.href='claim_entry.php?insurance_id=${item.Insurance_Entry_Id}'">Claim</button>
          </div>
        `;
        container.appendChild(card);
      });

      if (data.length === 0) {
        container.innerHTML = '<p>No insurance found.</p>';
      }
    });
});
</script>
</body>
</html>

<?php
include 'db.php';
?>
<!DOCTYPE html>
<html>
<head>
  <title>Insurance & Claim History</title>
  <style>
    body { font-family: Arial; background: #f8f8f8; padding: 30px; }
    .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    input { width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
    th { background-color: #f0f0f0; }
    .green { background-color: #d4edda; }
    .yellow { background-color: #fff3cd; }
    .red { background-color: #f8d7da; }
    button { padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; margin: 2px; }
    .view-btn { background: #007bff; color: white; }
    .claim-btn { background: #28a745; color: white; }
  </style>
</head>
<body>
<div class="container">
  <h2 style="text-align:center;">Insurance & Claim History</h2>

  <input type="text" id="searchInput" placeholder="Search by name, phone or IMEI...">

  <table id="resultTable">
    <thead>
      <tr>
        <th>Customer</th>
        <th>IMEI</th>
        <th>Model</th>
        <th>Insurance Dates</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</div>

<script>
document.getElementById('searchInput').addEventListener('input', function () {
  const query = this.value.trim();
  if (query.length < 2) return;

  fetch(`search_insurance.php?q=${encodeURIComponent(query)}`)
    .then(res => res.json())
    .then(data => {
      const tbody = document.querySelector('#resultTable tbody');
      tbody.innerHTML = '';

      data.forEach(entry => {
        const tr = document.createElement('tr');

        const today = new Date();
        const endDate = new Date(entry.Insurance_End_Date);
        const isExpired = endDate < today;
        const claimed = parseInt(entry.total_claims) > 0;

        if (isExpired) {
          tr.className = 'red';
        } else if (claimed) {
          tr.className = 'yellow';
        } else {
          tr.className = 'green';
        }

        tr.innerHTML = `
          <td>${entry.Cus_Name}</td>
          <td>${entry.IMEI_1}</td>
          <td>${entry.Product_Model_Name}</td>
          <td>${entry.Insurance_Start_Date} → ${entry.Insurance_End_Date}</td>
          <td>${isExpired ? 'Expired' : claimed ? 'Claimed' : 'Not Claimed'}</td>
          <td>
            <button class="view-btn" onclick="viewDetails(${entry.Insurance_Entry_Id})">View</button>
            <button class="claim-btn" onclick="claimNow(${entry.Insurance_Entry_Id})">Claim</button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    });
});

function viewDetails(id) {
  window.location.href = `view_insurance.php?id=${id}`;
}

function claimNow(id) {
  window.location.href = `claim_entry.php?insurance_id=${id}`;
}
</script>
</body>
</html>

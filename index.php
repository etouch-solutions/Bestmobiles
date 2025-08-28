<?php
// dashboard.php

include 'db.php';  // <-- make sure this has $conn = new mysqli(...);

error_reporting(E_ALL);
ini_set('display_errors', 1);
// ===== Fetch Insurance Entries by Month =====
$insuranceData = [];
$insuranceLabels = [];
$sql = "SELECT DATE_FORMAT(Insurance_Start_Date, '%Y-%m') as month, COUNT(*) as total 
        FROM Insurance_Entry 
        GROUP BY DATE_FORMAT(Insurance_Start_Date, '%Y-%m') 
        ORDER BY month ASC";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    $insuranceLabels[] = $row['month'];
    $insuranceData[] = $row['total'];
}

// ===== Fetch Claim Status (Approved, Pending, Rejected etc.) =====
$claimLabels = [];
$claimData = [];
$sql2 = "SELECT Status, COUNT(*) as total FROM Claim_Entry GROUP BY Status";
$result2 = $conn->query($sql2);
while($row = $result2->fetch_assoc()){
    $claimLabels[] = $row['Status'];
    $claimData[] = $row['total'];
}

// ===== Total Stats for Cards =====
$totalCustomers = $conn->query("SELECT COUNT(*) as c FROM Customer_Master")->fetch_assoc()['c'];
$totalInsurance = $conn->query("SELECT COUNT(*) as c FROM Insurance_Entry")->fetch_assoc()['c'];
$totalClaims = $conn->query("SELECT COUNT(*) as c FROM Claim_Entry")->fetch_assoc()['c'];
$totalStaff = $conn->query("SELECT COUNT(*) as c FROM Staff_Master")->fetch_assoc()['c'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Insurance Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; background: #f4f6f9; }
    .header { background: #2c3e50; color: #fff; padding: 20px; text-align: center; }
    .cards { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; margin: 20px; }
    .card { flex: 1 1 200px; background: white; border-radius: 10px; padding: 20px; box-shadow: 0 3px 6px rgba(0,0,0,0.1); text-align: center; }
    .card h2 { margin: 0; font-size: 2em; color: #2c3e50; }
    .card p { margin: 5px 0 0; color: #7f8c8d; }
    .charts { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; margin: 20px; }
    .chart-box { background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 3px 6px rgba(0,0,0,0.1); flex: 1 1 400px; }
    canvas { max-width: 100%; }
  </style>
</head>
<body>
  <div class="header"><h1>📊 Insurance Management Dashboard</h1></div>

  <!-- Cards -->
  <div class="cards">
    <div class="card"><h2><?php echo $totalCustomers; ?></h2><p>Customers</p></div>
    <div class="card"><h2><?php echo $totalInsurance; ?></h2><p>Insurance Entries</p></div>
    <div class="card"><h2><?php echo $totalClaims; ?></h2><p>Total Claims</p></div>
    <div class="card"><h2><?php echo $totalStaff; ?></h2><p>Staff</p></div>
  </div>

  <!-- Charts -->
  <div class="charts">
    <div class="chart-box">
      <h3>Insurance Entries by Month</h3>
      <canvas id="insuranceChart"></canvas>
    </div>
    <div class="chart-box">
      <h3>Claims Status</h3>
      <canvas id="claimChart"></canvas>
    </div>
  </div>

  <script>
    // Insurance Entries by Month
    new Chart(document.getElementById("insuranceChart"), {
      type: "line",
      data: {
        labels: <?php echo json_encode($insuranceLabels); ?>,
        datasets: [{
          label: "Insurance Entries",
          data: <?php echo json_encode($insuranceData); ?>,
          borderColor: "#2980b9",
          backgroundColor: "rgba(41,128,185,0.2)",
          fill: true,
          tension: 0.3
        }]
      }
    });

    // Claim Status Pie
    new Chart(document.getElementById("claimChart"), {
      type: "pie",
      data: {
        labels: <?php echo json_encode($claimLabels); ?>,
        datasets: [{
          data: <?php echo json_encode($claimData); ?>,
          backgroundColor: ["#27ae60", "#f39c12", "#e74c3c", "#8e44ad", "#3498db"]
        }]
      }
    });
  </script>
</body>
</html>

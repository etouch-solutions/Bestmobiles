<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Best Mobile Insurance Software</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Dashboard Layout */
    .dashboard {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin: 20px;
    }
    .card {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.2s;
    }
    .card:hover { transform: translateY(-5px); }
    .card h2 { font-size: 22px; margin: 10px 0; color:#144d30; }
    .card p { font-size: 14px; color: #666; }

    .charts {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
      gap: 20px;
      margin: 20px;
    }
    canvas {
      background: #fff;
      padding: 15px;
      border-radius: 12px;
      box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <!-- Top Nav -->
  <div class="navtop">
    <div class="logo">LOGO</div>
    <h1> Best Mobile Insurance Software</h1>
    <div class="hamburger" onclick="toggleSidebar()">☰</div>
  </div>

  <!-- Layout -->
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar mobile-hidden" id="sidebarMenu">
      <ul>
        <a href="index.php"><li class="active">Dashboard</li></a>
        <a href="branch.php"><li>Branch Master</li></a>
        <a href="brand.php"><li>Brand Master</li></a>
        <a href="add_staff.php"><li>Staff Master</li></a>
        <a href="Customer_Master.php"><li>Customer Master</li></a>
        <a href="add_insurance.php"><li>Insurance Master</li></a>
        <a href="add_defect.php"><li>Defect Master</li></a>
        <a href="insuranceentry.php"><li>Insurance Entry</li></a>
        <a href="serch.php"><li>Claim</li></a>
      </ul>
    </aside>

    <!-- Main Content -->
    <main style="flex:1; padding:20px;">
      <h2 style="color:#144d30;">📊 Dashboard Overview</h2>

      <!-- Cards -->
      <div class="dashboard">
        <div class="card">
          <i class="fa fa-users fa-2x" style="color:#144d30;"></i>
          <h2>
            <?php
              $result = $conn->query("SELECT COUNT(*) AS total FROM Customer_Master");
              $row = $result->fetch_assoc();
              echo $row['total'];
            ?>
          </h2>
          <p>Total Customers</p>
        </div>
        <div class="card">
          <i class="fa fa-shield-alt fa-2x" style="color:#144d30;"></i>
          <h2>
            <?php
              $result = $conn->query("SELECT COUNT(*) AS total FROM Insurance_Entry");
              $row = $result->fetch_assoc();
              echo $row['total'];
            ?>
          </h2>
          <p>Total Insurances</p>
        </div>
        <div class="card">
          <i class="fa fa-file-contract fa-2x" style="color:#144d30;"></i>
          <h2>
            <?php
              $result = $conn->query("SELECT COUNT(*) AS total FROM Claim_Entry");
              $row = $result->fetch_assoc();
              echo $row['total'];
            ?>
          </h2>
          <p>Total Claims</p>
        </div>
        <div class="card">
          <i class="fa fa-user-tie fa-2x" style="color:#144d30;"></i>
          <h2>
            <?php
              $result = $conn->query("SELECT COUNT(*) AS total FROM Staff_Master");
              $row = $result->fetch_assoc();
              echo $row['total'];
            ?>
          </h2>
          <p>Total Staff</p>
        </div>
      </div>

      <!-- Charts -->
      <div class="charts">
        <canvas id="insuranceChart"></canvas>
        <canvas id="claimsChart"></canvas>
      </div>
    </main>
  </div>

  <script>
    // Insurance by Month
    const ctx1 = document.getElementById('insuranceChart').getContext('2d');
    new Chart(ctx1, {
      type: 'bar',
      data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [{
          label: 'Insurance Entries',
          data: [12, 19, 3, 5, 2, 3, 10, 15, 8, 6, 12, 9], // Replace with PHP later
          backgroundColor: '#144d30'
        }]
      }
    });

    // Claims Pie
    const ctx2 = document.getElementById('claimsChart').getContext('2d');
    new Chart(ctx2, {
      type: 'pie',
      data: {
        labels: ['Approved','Pending','Rejected'],
        datasets: [{
          label: 'Claims Status',
          data: [10, 5, 2], // Replace with PHP later
          backgroundColor: ['#2ecc71','#f1c40f','#e74c3c']
        }]
      }
    });
  </script>

  <script src="script.js"></script>
</body>
</html>

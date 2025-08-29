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
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f5f6fa;
    }

    /* Dashboard Cards */
    .dashboard {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      margin: 20px;
    }
    .card {
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      text-align: center;
      transition: all 0.3s ease;
    }
    .card:hover { transform: translateY(-5px); }
    .card i {
      font-size: 28px;
      color: #144d30;
      margin-bottom: 10px;
    }
    .card h2 {
      font-size: 28px;
      margin: 5px 0;
      color: #144d30;
    }
    .card p {
      font-size: 14px;
      color: #555;
    }

    /* Charts Section */
    .charts {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 20px;
      margin: 20px;
    }
    .chart-card {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }
    canvas {
      width: 100% !important;
      height: 300px !important;
    }

    /* Responsive */
    @media(max-width: 900px){
      .dashboard { grid-template-columns: repeat(2, 1fr); }
      .charts { grid-template-columns: 1fr; }
    }
    @media(max-width: 600px){
      .dashboard { grid-template-columns: 1fr; }
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

    <!-- Main Dashboard -->
    <main style="flex:1; padding:20px;">
      <h2 style="color:#144d30; margin-bottom:20px;">📊 Dashboard Overview</h2>

      <!-- Stats Cards -->
      <div class="dashboard">
        <div class="card">
          <i class="fa fa-users"></i>
          <h2>
            <?php $r = $conn->query("SELECT COUNT(*) AS c FROM Customer_Master"); echo $r->fetch_assoc()['c']; ?>
          </h2>
          <p>Total Customers</p>
        </div>
        <div class="card">
          <i class="fa fa-shield-alt"></i>
          <h2>
            <?php $r = $conn->query("SELECT COUNT(*) AS c FROM Insurance_Entry"); echo $r->fetch_assoc()['c']; ?>
          </h2>
          <p>Total Insurances</p>
        </div>
        <div class="card">
          <i class="fa fa-file-contract"></i>
          <h2>
            <?php $r = $conn->query("SELECT COUNT(*) AS c FROM Claim_Entry"); echo $r->fetch_assoc()['c']; ?>
          </h2>
          <p>Total Claims</p>
        </div>
        <div class="card">
          <i class="fa fa-user-tie"></i>
          <h2>
            <?php $r = $conn->query("SELECT COUNT(*) AS c FROM Staff_Master"); echo $r->fetch_assoc()['c']; ?>
          </h2>
          <p>Total Staff</p>
        </div>
      </div>

      
    </main>
  </div>

  <!-- Charts -->
  <script>
    const ctx1 = document.getElementById('insuranceChart').getContext('2d');
    new Chart(ctx1, {
      type: 'bar',
      data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [{
          label: 'Insurance Entries',
          data: [12, 19, 3, 5, 2, 3, 10, 15, 8, 6, 12, 9], // replace later with PHP
          backgroundColor: '#144d30'
        }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });

    const ctx2 = document.getElementById('claimsChart').getContext('2d');
    new Chart(ctx2, {
      type: 'pie',
      data: {
        labels: ['Approved','Pending','Rejected'],
        datasets: [{
          label: 'Claims Status',
          data: [10, 5, 2], // replace later with PHP
          backgroundColor: ['#2ecc71','#f1c40f','#e74c3c']
        }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });
  </script>

  <script src="script.js"></script>
</body>
</html>

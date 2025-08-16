<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Best Mobile Insurance</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container { padding:20px; }
        .cards { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; margin-bottom:30px; }
        .card {
            background:#fff; padding:20px; border-radius:12px; 
            box-shadow:0 4px 10px rgba(0,0,0,0.1);
            text-align:center; transition:0.3s;
        }
        .card:hover { transform:translateY(-5px); }
        .card h2 { font-size:28px; margin:10px 0; color:#144d30ff; }
        .card p { font-size:14px; color:#555; }
        .chart-section { display:flex; gap:30px; margin-bottom:40px; }
        .chart-box { background:#fff; padding:20px; border-radius:12px; flex:1; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
        .recent-table { background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
        table { width:100%; border-collapse:collapse; }
        table th, table td { border-bottom:1px solid #eee; padding:10px; text-align:left; font-size:14px; }
        table th { background:#f5f5f5; }
    </style>
</head>
<body>
<div class="navtop">
    <div class="logo">LOGO</div>
    <h1>Best Mobile Insurance Dashboard</h1>
    <div class="hamburger" onclick="toggleSidebar()">☰</div>
</div>

<div class="container">
    <aside class="sidebar mobile-hidden" id="sidebarMenu">
      <ul>
        <a href="index.php" class="active"><li>Dashboard</li></a>
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

    <main class="dashboard-container">
        <?php
        // Fetch counts
        $branchCount = $conn->query("SELECT COUNT(*) as c FROM Branch_Master")->fetch_assoc()['c'];
        $brandCount = $conn->query("SELECT COUNT(*) as c FROM Brand_Master")->fetch_assoc()['c'];
        $staffCount = $conn->query("SELECT COUNT(*) as c FROM Staff_Master")->fetch_assoc()['c'];
        $customerCount = $conn->query("SELECT COUNT(*) as c FROM Customer_Master")->fetch_assoc()['c'];
        $insuranceCount = $conn->query("SELECT COUNT(*) as c FROM Insurance_Master")->fetch_assoc()['c'];
        $defectCount = $conn->query("SELECT COUNT(*) as c FROM Defect_Master")->fetch_assoc()['c'];
        $insuranceEntryCount = $conn->query("SELECT COUNT(*) as c FROM Insurance_Entry")->fetch_assoc()['c'];
        $claimCount = $conn->query("SELECT COUNT(*) as c FROM Claim_Entry")->fetch_assoc()['c'];
        ?>

        <!-- Dashboard Cards -->
        <div class="cards">
            <div class="card"><i class="fa-solid fa-building fa-2x"></i><h2><?php echo $branchCount; ?></h2><p>Branches</p></div>
            <div class="card"><i class="fa-solid fa-mobile fa-2x"></i><h2><?php echo $brandCount; ?></h2><p>Brands</p></div>
            <div class="card"><i class="fa-solid fa-users fa-2x"></i><h2><?php echo $staffCount; ?></h2><p>Staff</p></div>
            <div class="card"><i class="fa-solid fa-user fa-2x"></i><h2><?php echo $customerCount; ?></h2><p>Customers</p></div>
            <div class="card"><i class="fa-solid fa-file-shield fa-2x"></i><h2><?php echo $insuranceCount; ?></h2><p>Insurance Plans</p></div>
            <div class="card"><i class="fa-solid fa-bug fa-2x"></i><h2><?php echo $defectCount; ?></h2><p>Defects</p></div>
            <div class="card"><i class="fa-solid fa-shield-halved fa-2x"></i><h2><?php echo $insuranceEntryCount; ?></h2><p>Insurance Entries</p></div>
            <div class="card"><i class="fa-solid fa-clipboard-list fa-2x"></i><h2><?php echo $claimCount; ?></h2><p>Claims</p></div>
        </div>

        <!-- Charts -->
        <div class="chart-section">
            <div class="chart-box">
                <h3>Insurance vs Claims</h3>
                <canvas id="insuranceClaimChart"></canvas>
            </div>
            <div class="chart-box">
                <h3>Customers Growth</h3>
                <canvas id="customerChart"></canvas>
            </div>
        </div>

        <!-- Recent Claims -->
        <div class="recent-table">
            <h3>Recent Claims</h3>
            <table>
                <tr>
                    <th>Claim ID</th>
                    <th>Customer</th>
                    <th>IMEI</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
                <?php
                $recentClaims = $conn->query("SELECT c.Claim_Id, cm.Cus_Name, ie.IMEI_1, c.Created_At, c.Status 
                                              FROM Claim_Entry c 
                                              JOIN Insurance_Entry ie ON c.Ins_Entry_Id=ie.Ins_Entry_Id
                                              JOIN Customer_Master cm ON ie.Cus_Id=cm.Cus_Id
                                              ORDER BY c.Created_At DESC LIMIT 5");
                while ($row = $recentClaims->fetch_assoc()) {
                    echo "<tr>
                            <td>".$row['Claim_Id']."</td>
                            <td>".$row['Cus_Name']."</td>
                            <td>".$row['IMEI_1']."</td>
                            <td>".$row['Created_At']."</td>
                            <td>".$row['Status']."</td>
                          </tr>";
                }
                ?>
            </table>
        </div>
    </main>
</div>

<script>
    // Insurance vs Claims Chart
    new Chart(document.getElementById("insuranceClaimChart"), {
        type: "doughnut",
        data: {
            labels: ["Insurance Entries", "Claims"],
            datasets: [{
                data: [<?php echo $insuranceEntryCount; ?>, <?php echo $claimCount; ?>],
                backgroundColor: ["#144d30", "#ff6b6b"]
            }]
        }
    });

    // Customer Growth (dummy month-wise for now)
    new Chart(document.getElementById("customerChart"), {
        type: "line",
        data: {
            labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug"],
            datasets: [{
                label:"Customers",
                data:[5,10,15,20,30,40,<?php echo $customerCount; ?>],
                borderColor:"#144d30",
                fill:false
            }]
        }
    });
</script>
</body>
</html>

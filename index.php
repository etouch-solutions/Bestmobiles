<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Mobile Insurance</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="navtop">
    <div class="logo">LOGO</div>
    <h1>Best Mobile Insurance Dashboard</h1>
    <div class="hamburger" onclick="toggleSidebar()">☰</div>
</div>

<div class="container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebarMenu">
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

    <!-- Main -->
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

        <!-- Cards -->
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

    // Customer Growth (sample with last count)
    new Chart(document.getElementById("customerChart"), {
        type: "line",
        data: {
            labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug"],
            datasets: [{
                label:"Customers",
                data:[5,10,15,20,30,40,<?php echo $customerCount; ?>],
                borderColor:"#144d30",
                borderWidth:2,
                fill:false,
                tension:0.3
            }]
        }
    });
</script>
</body>
</html>

<?php
$host = 'localhost';  
$user = 'u520351775_etouch';
$pass = '!@#Admin@4321';  
$db   = 'u520351775_bestmobiles';  
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

// Fetch analytics
$totalCustomers = $conn->query("SELECT COUNT(*) as cnt FROM Customer_Master")->fetch_assoc()['cnt'];
$totalStaff = $conn->query("SELECT COUNT(*) as cnt FROM Staff_Master")->fetch_assoc()['cnt'];
$totalBranches = $conn->query("SELECT COUNT(*) as cnt FROM Branch_Master")->fetch_assoc()['cnt'];
$totalInsurance = $conn->query("SELECT COUNT(*) as cnt FROM Insurance_Master")->fetch_assoc()['cnt'];
$totalClaims = $conn->query("SELECT COUNT(*) as cnt FROM Claim_Entry")->fetch_assoc()['cnt'];

// Claims per month (for chart)
$claimsData = [];
$res = $conn->query("SELECT MONTH(Created_At) as m, COUNT(*) as c FROM Claim_Entry GROUP BY MONTH(Created_At)");
while ($row = $res->fetch_assoc()) {
    $claimsData[$row['m']] = $row['c'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insurance Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-box canvas {
    width: 100% !important;
    height: 300px !important;
}

        body { margin:0; font-family: Arial, sans-serif; background:#f4f6f9; }
        header { background:#2d3436; color:white; padding:15px; text-align:center; font-size:20px; }
        .container { display:flex; }
        .sidebar { width:220px; background:#2d3436; color:white; min-height:100vh; padding:20px; }
        .sidebar a { display:block; color:white; padding:10px; text-decoration:none; margin:5px 0; border-radius:5px; }
        .sidebar a:hover { background:#636e72; }
        .content { flex:1; padding:20px; }
        .cards { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:20px; }
        .card { background:white; padding:20px; border-radius:12px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
        .card h2 { margin:0; font-size:28px; }
        .card p { color:#666; margin:5px 0 0; }
        .charts { display:flex; gap:20px; flex-wrap:wrap; margin-top:20px; }
        .chart-box { flex:1; min-width:350px; background:white; border-radius:12px; padding:20px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
        table { width:100%; border-collapse:collapse; margin-top:20px; background:white; border-radius:12px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
        th, td { padding:12px; border-bottom:1px solid #ddd; text-align:left; }
        th { background:#2d3436; color:white; }
    </style>
</head>
<?php
// Debug claims data
echo "<pre>";
print_r($claimsData);
echo "</pre>";
?>

<body>
<header>📊 Insurance Management Dashboard</header>
<div class="container">
    <div class="sidebar">
        <h3>Menu</h3>
        <a href="dashboard.php">Dashboard</a>
        <a href="customer_master.php">Customers</a>
        <a href="staff_master.php">Staff</a>
        <a href="branch_master.php">Branches</a>
        <a href="insurance_master.php">Insurance</a>
        <a href="insurance_entry.php">Insurance Entry</a>
        <a href="claim_entry.php">Claims</a>
    </div>
    <div class="content">
        <!-- Summary Cards -->
        <div class="cards">
            <div class="card"><h2><?= $totalCustomers ?></h2><p>Total Customers</p></div>
            <div class="card"><h2><?= $totalStaff ?></h2><p>Total Staff</p></div>
            <div class="card"><h2><?= $totalBranches ?></h2><p>Total Branches</p></div>
            <div class="card"><h2><?= $totalInsurance ?></h2><p>Insurance Plans</p></div>
            <div class="card"><h2><?= $totalClaims ?></h2><p>Total Claims</p></div>
        </div>

        <!-- Charts -->
        <div class="charts">
            <div class="chart-box">
                <h3>Claims per Month</h3>
                <canvas id="claimsChart"></canvas>
            </div>
            <div class="chart-box">
                <h3>Insurance Distribution</h3>
                <canvas id="insuranceChart"></canvas>
            </div>
        </div>

        <!-- Recent Records -->
        <h3 style="margin-top:30px;">📌 Recent Claims</h3>
        <table>
            <tr><th>ID</th><th>Customer</th><th>Insurance</th><th>Date</th></tr>
            <?php
            $res = $conn->query("SELECT ce.Claim_Id, cm.Cus_Name, im.Insurance_Name, ce.Created_At 
                FROM Claim_Entry ce 
                JOIN Insurance_Entry ie ON ce.Ins_Entry_Id = ie.Ins_Entry_Id 
                JOIN Customer_Master cm ON ie.Cus_Id = cm.Cus_Id 
                JOIN Insurance_Master im ON ie.Insurance_Id = im.Insurance_Id 
                ORDER BY ce.Created_At DESC LIMIT 5");
            while ($r = $res->fetch_assoc()) {
                echo "<tr>
                        <td>{$r['Claim_Id']}</td>
                        <td>{$r['Cus_Name']}</td>
                        <td>{$r['Insurance_Name']}</td>
                        <td>{$r['Created_At']}</td>
                      </tr>";
            }
            ?>
        </table>
    </div>
</div>

<script>
    // Claims per Month Chart
    const claimsData = <?= json_encode(array_values($claimsData)) ?>;
    const claimsLabels = <?= json_encode(array_keys($claimsData)) ?>;
    new Chart(document.getElementById('claimsChart'), {
        type: 'bar',
        data: { labels: claimsLabels, datasets: [{ label: 'Claims', data: claimsData, backgroundColor: '#0984e3' }] }
    });

    // Insurance Distribution Pie
    new Chart(document.getElementById('insuranceChart'), {
        type: 'pie',
        data: {
            labels: ['Active', 'Expired'],
            datasets: [{
                data: [<?= $conn->query("SELECT COUNT(*) as c FROM Insurance_Entry WHERE Is_Insurance_Active=1")->fetch_assoc()['c'] ?>,
                       <?= $conn->query("SELECT COUNT(*) as c FROM Insurance_Entry WHERE Is_Insurance_Active=0")->fetch_assoc()['c'] ?>],
                backgroundColor: ['#00b894', '#d63031']
            }]
        }
    });


        // ✅ Claims per Month Chart
    const claimsCtx = document.getElementById('claimsChart').getContext('2d');
    const claimsLabels = <?= json_encode(array_map(function($m){ 
        return date("F", mktime(0, 0, 0, $m, 1)); 
    }, array_keys($claimsData))) ?>; // Month names
    const claimsData = <?= json_encode(array_values($claimsData), JSON_NUMERIC_CHECK) ?>;

    new Chart(claimsCtx, {
        type: 'bar',
        data: {
            labels: claimsLabels,
            datasets: [{
                label: 'Claims',
                data: claimsData,
                backgroundColor: '#0984e3'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // ✅ Insurance Distribution Pie Chart
    const insuranceCtx = document.getElementById('insuranceChart').getContext('2d');
    new Chart(insuranceCtx, {
        type: 'pie',
        data: {
            labels: ['Active', 'Expired'],
            datasets: [{
                data: [
                    <?= (int)$conn->query("SELECT COUNT(*) as c FROM Insurance_Entry WHERE Is_Insurance_Active=1")->fetch_assoc()['c'] ?>,
                    <?= (int)$conn->query("SELECT COUNT(*) as c FROM Insurance_Entry WHERE Is_Insurance_Active=0")->fetch_assoc()['c'] ?>
                ],
                backgroundColor: ['#00b894', '#d63031']
            }]
        },
        options: { responsive: true }
    });
</script>
</body>
</html>

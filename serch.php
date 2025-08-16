<?php
// serch.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch insurance/claim entries with defects
function fetch_insurance_entries($conn, $search = "")
{
    $search_sql = "";
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $search_sql = " AND (c.Cus_Name LIKE '%$search%' 
                        OR c.Cus_CNo LIKE '%$search%' 
                        OR ie.IMEI_1 LIKE '%$search%')";
    }

    $sql = "
        SELECT 
            ce.Claim_Id,
            c.Cus_Name, c.Cus_CNo, c.Cus_Address,
            ie.IMEI_1,
            d.Defect_Name,
            cdv.Defect_Value,
            ce.Claim_Remarks AS Remarks,
            ce.Created_At
        FROM Claim_Entry ce
        JOIN Insurance_Entry ie ON ce.Insurance_Entry_Id = ie.Ins_Entry_Id
        JOIN Customer_Master c ON ie.Cus_Id = c.Cus_Id
        LEFT JOIN Claim_Defect_Value cdv ON ce.Claim_Id = cdv.Claim_Id
        LEFT JOIN Defect_Master d ON cdv.Defect_Id = d.Defect_Id
        WHERE 1=1 $search_sql
        ORDER BY ce.Created_At DESC
    ";

    return $conn->query($sql);
}

// Handle search input
$search = $_GET['search'] ?? "";
$result = fetch_insurance_entries($conn, $search);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Claim Search</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .container { width: 90%; margin: 20px auto; }
        h2 { text-align: center; margin-bottom: 20px; }
        .search-bar { text-align: center; margin-bottom: 20px; }
        input[type="text"] {
            width: 300px; padding: 10px; font-size: 14px;
            border: 1px solid #ccc; border-radius: 4px;
        }
        button {
            padding: 10px 15px; border: none; border-radius: 4px;
            background: #007BFF; color: #fff; cursor: pointer;
        }
        table {
            width: 100%; border-collapse: collapse; background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px; border: 1px solid #ddd; text-align: left;
        }
        th { background: #007BFF; color: #fff; }
        tr:nth-child(even) { background: #f9f9f9; }
    </style>
</head>
<body>
<div class="container">
    <h2>Claim & Defect Search</h2>

    <div class="search-bar">
        <form method="get" action="serch.php">
            <input type="text" name="search" placeholder="Search by Name, Contact, IMEI" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <table>
        <tr>
            <th>Claim ID</th>
            <th>Customer Name</th>
            <th>Contact</th>
            <th>IMEI</th>
            <th>Defect</th>
            <th>Defect Value</th>
            <th>Remarks</th>
            <th>Date</th>
        </tr>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['Claim_Id'] ?></td>
                    <td><?= htmlspecialchars($row['Cus_Name']) ?></td>
                    <td><?= htmlspecialchars($row['Cus_CNo']) ?></td>
                    <td><?= htmlspecialchars($row['IMEI_1']) ?></td>
                    <td><?= htmlspecialchars($row['Defect_Name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['Defect_Value'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['Remarks'] ?? '-') ?></td>
                    <td><?= $row['Created_At'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8" style="text-align:center;">No results found</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>

<?php $conn->close(); ?>

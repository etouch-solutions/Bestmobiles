<?php
include 'db.php';

// Fetch insurance entries with customer, branch, brand, insurance details
$sql = "
    SELECT ie.*, 
           cm.Cus_Name, cm.Cus_CNo, 
           bm.Branch_Name, 
           br.Brand_Name, 
           im.Insurance_Name,
           (
               SELECT COUNT(*) 
               FROM Claim_Entry ce 
               WHERE ce.Ins_Entry_Id = ie.Ins_Entry_Id
           ) AS Claim_Count
    FROM Insurance_Entry ie
    JOIN Customer_Master cm ON ie.Cus_Id = cm.Cus_Id
    JOIN Branch_Master bm ON cm.Branch_Id = bm.Branch_Id
    JOIN Brand_Master br ON ie.Brand_Id = br.Brand_Id
    JOIN Insurance_Master im ON ie.Insurance_Id = im.Insurance_Id
    ORDER BY ie.Created_At DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Insurance Entries</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
    }
    th, td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: left;
    }
    th {
        background: #f4f4f4;
    }

    /* ✅ Fixed row colors */
    tr.not_claimed td {
        background-color: #b2ffb8;   /* Green */
    }
    tr.claimed td {
        background-color: #ffecaf;   /* Yellow */
    }
    tr.expired td {
        background-color: #ffc1c1;   /* Red */
    }
</style>
</head>
<body>

<h2>Insurance Entries</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Contact</th>
        <th>Branch</th>
        <th>Brand</th>
        <th>Insurance</th>
        <th>Product</th>
        <th>IMEI</th>
        <th>Value</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Premium</th>
        <th>Claim Count</th>
        <th>Status</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): 
        $today = date("Y-m-d");
        $status_class = "";

        if ($row['Insurance_End_Date'] < $today) {
            $status = "Expired";
            $status_class = "expired";
        } elseif ($row['Claim_Count'] > 0) {
            $status = "Claimed";
            $status_class = "claimed";
        } else {
            $status = "Not Claimed";
            $status_class = "not_claimed";
        }
    ?>
    <tr class="<?php echo $status_class; ?>">
        <td><?php echo $row['Ins_Entry_Id']; ?></td>
        <td><?php echo $row['Cus_Name']; ?></td>
        <td><?php echo $row['Cus_CNo']; ?></td>
        <td><?php echo $row['Branch_Name']; ?></td>
        <td><?php echo $row['Brand_Name']; ?></td>
        <td><?php echo $row['Insurance_Name']; ?></td>
        <td><?php echo $row['Product_Model_Name']; ?></td>
        <td><?php echo $row['IMEI_1']; ?></td>
        <td><?php echo $row['Product_Value']; ?></td>
        <td><?php echo $row['Insurance_Start_Date']; ?></td>
        <td><?php echo $row['Insurance_End_Date']; ?></td>
        <td><?php echo $row['Premium_Amount']; ?></td>
        <td><?php echo $row['Claim_Count']; ?></td>
        <td><?php echo $status; ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

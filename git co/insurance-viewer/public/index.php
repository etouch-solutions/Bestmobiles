<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insurance Viewer</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div id="insurance-data">
        <?php
        include '../src/fetch_insurance_entries.php';
        $insurance_entries = fetchInsuranceEntries();

        if ($insurance_entries) {
            echo "<table>";
            echo "<tr><th>Customer ID</th><th>Brand ID</th><th>Insurance ID</th><th>Staff ID</th><th>Product Model Name</th><th>IMEI 1</th><th>IMEI 2</th><th>Product Value</th><th>Bill Copy Path</th><th>Product Photo Path</th><th>Bill Date</th><th>Insurance Start Date</th><th>Insurance End Date</th><th>Premium Amount</th><th>Is Product Covered</th><th>Is Insurance Active</th></tr>";
            foreach ($insurance_entries as $entry) {
                echo "<tr>";
                echo "<td>{$entry['Cus_Id']}</td>";
                echo "<td>{$entry['Brand_Id']}</td>";
                echo "<td>{$entry['Insurance_Id']}</td>";
                echo "<td>{$entry['Staff_Id']}</td>";
                echo "<td>{$entry['Product_Model_Name']}</td>";
                echo "<td>{$entry['IMEI_1']}</td>";
                echo "<td>{$entry['IMEI_2']}</td>";
                echo "<td>{$entry['Product_Value']}</td>";
                echo "<td>{$entry['Bill_Copy_Path']}</td>";
                echo "<td>{$entry['Product_Photo_Path']}</td>";
                echo "<td>{$entry['Bill_Date']}</td>";
                echo "<td>{$entry['Insurance_Start_Date']}</td>";
                echo "<td>{$entry['Insurance_End_Date']}</td>";
                echo "<td>{$entry['Premium_Amount']}</td>";
                echo "<td>{$entry['Is_Product_Covered']}</td>";
                echo "<td>{$entry['Is_Insurance_Active']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No insurance entries found.</p>";
        }
        ?>
    </div>
</body>
</html>
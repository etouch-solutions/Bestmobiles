<?php
// Database connection
 include 'db.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If "Download Excel" is clicked
if (isset($_POST['download_excel'])) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=branch_list.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    $result = $conn->query("SELECT Branch_Id, Branch_Name, Branch_Code, Is_Active, Created_At FROM Branch_Master");

    echo "Branch ID\tBranch Name\tBranch Code\tStatus\tCreated At\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Branch_Id'] . "\t" . 
             $row['Branch_Name'] . "\t" . 
             $row['Branch_Code'] . "\t" . 
             ($row['Is_Active'] ? "Active" : "Inactive") . "\t" . 
             $row['Created_At'] . "\n";
    }
    exit;
}

// Fetch data to display in table
$result = $conn->query("SELECT * FROM Branch_Master");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Branch Master</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #007BFF; color: white; }
        .btn-download {
            background: green; 
            color: white; 
            padding: 10px 15px; 
            border: none; 
            cursor: pointer; 
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <h2>Branch Master</h2>

    <!-- Download Excel Button -->
    <form method="post">
        <button type="submit" name="download_excel" class="btn-download">⬇ Download Excel</button>
    </form>

    <!-- Display Branch Table -->
    <table>
        <tr>
            <th>Branch ID</th>
            <th>Branch Name</th>
            <th>Branch Code</th>
            <th>Status</th>
            <th>Created At</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['Branch_Id'] ?></td>
                <td><?= $row['Branch_Name'] ?></td>
                <td><?= $row['Branch_Code'] ?></td>
                <td><?= $row['Is_Active'] ? "Active" : "Inactive" ?></td>
                <td><?= $row['Created_At'] ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>

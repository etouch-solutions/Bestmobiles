<?php
// Database connection
$servername = "localhost"; // or your host
$username   = "u520351775_etouch"; // your DB username
$password   = "!@#Admin@4321";  // your DB password
$dbname     = "u520351775_bestmobiles"; // your DB name
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ===== Download Excel when button clicked =====
if (isset($_POST['download'])) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=branch_list.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    $result = $conn->query("SELECT * FROM Branch_Master");
    if ($result->num_rows > 0) {
        echo "Branch_Id\tBranch_Name\tBranch_Address\tBranch_CNo\tIs_Active\tCreated_At\n";
        while ($row = $result->fetch_assoc()) {
            echo $row['Branch_Id'] . "\t" .
                 $row['Branch_Name'] . "\t" .
                 $row['Branch_Address'] . "\t" .
                 $row['Branch_CNo'] . "\t" .
                 $row['Is_Active'] . "\t" .
                 $row['Created_At'] . "\n";
        }
    }
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Download Branch Master Excel</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        button { padding: 10px 20px; background: green; color: white; border: none; cursor: pointer; border-radius: 5px; }
        button:hover { background: darkgreen; }
    </style>
</head>
<body>
    <h2>Branch Master Export</h2>
    <form method="post">
        <button type="submit" name="download">Download Excel</button>
    </form>
</body>
</html>
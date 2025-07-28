<?php
include 'db.php';

$conn = mysqli_connect($host, $user, $pass, $db);
$keyword = mysqli_real_escape_string($conn, $_GET['keyword']);

$query = "
  SELECT ie.Insurance_Entry_Id, cm.Cus_Name, ie.IMEI_1 
  FROM Insurance_Entry ie
  JOIN Customer_Master cm ON cm.Cus_Id = ie.Cus_Id
  WHERE cm.Cus_Name LIKE '%$keyword%' OR ie.IMEI_1 LIKE '%$keyword%'
  LIMIT 10
";

$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    echo "<div onclick=\"selectInsuranceEntry({$row['Insurance_Entry_Id']}, '{$row['Cus_Name']} ({$row['IMEI_1']})')\" 
            style='cursor:pointer;padding:5px;border-bottom:1px solid #ccc;'>
            {$row['Cus_Name']} ({$row['IMEI_1']})
          </div>";
  }
} else {
  echo "No results found.";
}
?>

<?php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

if (isset($_GET['brand_id'])) {
    $id = intval($_GET['brand_id']);
    $res = mysqli_query($conn, "SELECT * FROM Brands_Master WHERE Brand_Id = $id");
    if ($row = mysqli_fetch_assoc($res)) {
        echo "<strong>Brand:</strong> " . htmlspecialchars($row['Brand_Name']) . "<br>";
        echo "<strong>Description:</strong> " . htmlspecialchars($row['Brand_Description'] ?? 'N/A') . "<br>";
    } else {
        echo "Brand not found.";
    }
}
?>

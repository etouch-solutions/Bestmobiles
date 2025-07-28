<?php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

if (isset($_GET['insurance_id'])) {
    $id = intval($_GET['insurance_id']);
    $res = mysqli_query($conn, "SELECT * FROM Insurance_Master WHERE Insurance_Id = $id");
    if ($row = mysqli_fetch_assoc($res)) {
        echo "<strong>Plan:</strong> " . htmlspecialchars($row['Insurance_Name']) . "<br>";
        echo "<strong>Description:</strong> " . htmlspecialchars($row['Insurance_Description']) . "<br>";
        echo "<strong>Premium %:</strong> " . htmlspecialchars($row['Premium_Percentage']) . "%<br>";
        echo "<strong>Duration:</strong> " . htmlspecialchars($row['Duration_Months']) . " months<br>";
    } else {
        echo "Plan not found.";
    }
}
?>

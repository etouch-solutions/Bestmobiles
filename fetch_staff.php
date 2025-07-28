<?php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

if (isset($_GET['staff_id'])) {
    $id = intval($_GET['staff_id']);
    $res = mysqli_query($conn, "SELECT * FROM Staff_Master WHERE Staff_Id = $id");
    if ($row = mysqli_fetch_assoc($res)) {
        echo "<strong>Name:</strong> " . htmlspecialchars($row['Staff_Name']) . "<br>";
        echo "<strong>Email:</strong> " . htmlspecialchars($row['Staff_Email'] ?? 'N/A') . "<br>";
        echo "<strong>Phone:</strong> " . htmlspecialchars($row['Staff_Contact'] ?? 'N/A') . "<br>";
    } else {
        echo "Staff not found.";
    }
}
?>

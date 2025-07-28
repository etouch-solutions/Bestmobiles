<?php
include 'db.php';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed");
}

if (isset($_GET['cus_id'])) {
    $cus_id = intval($_GET['cus_id']);
    $sql = "SELECT Cus_Name, Cus_CNo, Cus_Email, Cus_Address FROM Customer_Master WHERE Cus_Id = $cus_id";
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        echo "<strong>Name:</strong> " . htmlspecialchars($row['Cus_Name']) . "<br>";
        echo "<strong>Contact:</strong> " . htmlspecialchars($row['Cus_CNo']) . "<br>";
        echo "<strong>Email:</strong> " . htmlspecialchars($row['Cus_Email']) . "<br>";
        echo "<strong>Address:</strong> " . nl2br(htmlspecialchars($row['Cus_Address'])) . "<br>";
    } else {
        echo "Customer not found.";
    }
} else {
    echo "Invalid request.";
}


 
?>

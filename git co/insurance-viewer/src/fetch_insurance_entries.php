<?php
include 'db.php';

header('Content-Type: application/json');

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die(json_encode(["error" => "Connection failed: " . mysqli_connect_error()]));
}

$query = "SELECT * FROM Insurance_Entry";
$result = mysqli_query($conn, $query);

$insurance_entries = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $insurance_entries[] = $row;
    }
}

mysqli_close($conn);

echo json_encode($insurance_entries);
?>
<?php
// Make sure this includes correct values or your db.php
include 'db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT Brand_Id, Brand_Name, Brand_Status FROM Brands_Master";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

echo "<h3>Brand Debug Output:</h3>";
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row['Brand_Id'] . " | Name: " . $row['Brand_Name'] . " | Status: " . $row['Brand_Status'] . "<br>";
    }
} else {
    echo "No brands found.";
}
?>

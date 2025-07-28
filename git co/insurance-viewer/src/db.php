<?php
$host = 'localhost'; // Database host
$user = 'your_username'; // Database username
$pass = 'your_password'; // Database password
$db = 'your_database_name'; // Database name

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<?php
$host = 'localhost'; // or try your Hostinger host if localhost doesn't work
$user = 'u520351775_etouch';
$pass = '!@#Admin@4321';  // replace with your actual password
$db   = 'u520351775_bestmobiles'; // or use the correct one you created

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

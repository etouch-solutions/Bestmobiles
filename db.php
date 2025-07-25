<?php
$host = 'localhost';  
$user = 'u520351775_etouch';
$pass = '!@#Admin@4321';  
$db   = 'u520351775_bestmobiles';  
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
 
 

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
$conn = new mysqli("localhost", "root", "", "your_db_name");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $photo = addslashes(file_get_contents($_FILES['photo']['tmp_name']));
    $id_copy = addslashes(file_get_contents($_FILES['id_copy']['tmp_name']));

    $sql = "INSERT INTO sample (name, photo, id_copy) VALUES ('$name', '$photo', '$id_copy')";
    
    if ($conn->query($sql)) {
        echo "Data inserted successfully. <a href='sample_view.php'>View Data</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

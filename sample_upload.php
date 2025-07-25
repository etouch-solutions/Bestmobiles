<?php
include 'db.php';

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $photo = addslashes(file_get_contents($_FILES['photo']['tmp_name']));
    $id_copy = addslashes(file_get_contents($_FILES['id_copy']['tmp_name']));

    $sql = "INSERT INTO sample (name, photo, id_copy) VALUES ('$name', '$photo', '$id_copy')";

    if ($conn->query($sql)) {
        echo "✅ Upload successful! <a href='index.php'>Back to list</a>";
    } else {
        echo "❌ Upload failed: " . $conn->error;
    }
}
?>

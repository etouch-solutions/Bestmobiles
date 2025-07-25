<?php
$conn = new mysqli("localhost", "root", "", "your_db_name");
$sql = "SELECT * FROM sample";
$result = $conn->query($sql);
?>

<h2>Uploaded Data</h2>
<?php while($row = $result->fetch_assoc()) { ?>
    <b>Name:</b> <?= $row['name']; ?><br>
    <b>Photo:</b><br>
    <img src="data:image/jpeg;base64,<?= base64_encode($row['photo']); ?>" height="100"><br>
    <b>ID Copy:</b><br>
    <img src="data:image/jpeg;base64,<?= base64_encode($row['id_copy']); ?>" height="100"><br><br><hr>
<?php } ?>

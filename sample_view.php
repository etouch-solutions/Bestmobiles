<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

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
<?php }



$result = $conn->query("SELECT * FROM sample");
while($row = $result->fetch_assoc()){
    echo "<b>Name:</b> {$row['name']}<br>";
    echo "<b>Photo:</b><br><img src='data:image/jpeg;base64," . base64_encode($row['photo']) . "' height='100'><br>";
    echo "<b>ID Copy:</b><br><img src='data:image/jpeg;base64," . base64_encode($row['id_copy']) . "' height='100'><br><hr>";
}
?>
 
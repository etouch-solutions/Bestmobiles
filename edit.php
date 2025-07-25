<?php include 'db.php'; ?>
<?php
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM users WHERE id=$id");
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['NAME'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $conn->query("UPDATE users SET NAME='$name', email='$email', phone='$phone' WHERE id=$id");
    header("Location: index.php");
    exit;
}
?>
<form method="POST">
    <input type="text" name="NAME" value="<?= $row['NAME'] ?>" required><br>
    <input type="email" name="email" value="<?= $row['email'] ?>" required><br>
    <input type="text" name="phone" value="<?= $row['phone'] ?>"><br>
    <input type="submit" value="Update">
</form>

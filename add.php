<?php include 'db.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['NAME'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $conn->query("INSERT INTO users (NAME, email, phone) VALUES ('$name', '$email', '$phone')");
    header("Location: index.php");
    exit;
}
?>
<form method="POST">
    <input type="text" name="NAME" placeholder="Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="text" name="phone" placeholder="Phone"><br>
    <input type="submit" value="Add User">
</form>

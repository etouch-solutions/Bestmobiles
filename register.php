<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';
  if ($name && $email && $pass) {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO users(name,email,password_hash,role) VALUES (?,?,?,'admin')");
    $stmt->bind_param('sss', $name, $email, $hash);
    if ($stmt->execute()) { echo "User created. <a href='login.php'>Login</a>"; exit; }
    echo "Error: " . $mysqli->error;
  } else {
    echo "All fields required.";
  }
}
?>
<!DOCTYPE html>
<html><body>
<form method="post">
  <input name="name" placeholder="Name"><br>
  <input name="email" type="email" placeholder="Email"><br>
  <input name="password" type="password" placeholder="Password"><br>
  <button type="submit">Create Admin</button>
</form>
</body></html>

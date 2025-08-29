<?php
session_start();
include 'db.php';

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = $_POST['password'];

    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $row = mysqli_fetch_assoc($res);

    if ($row && password_verify($pass, $row['password'])) {
        $_SESSION['user'] = $row['id'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <style>
    body {font-family: Arial, sans-serif; background:#f4f4f4; display:flex; height:100vh; align-items:center; justify-content:center;}
    .box {background:#fff; padding:30px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.2);}
    input {width:100%; padding:10px; margin:10px 0; border:1px solid #ccc; border-radius:5px;}
    button {width:100%; padding:10px; background:#28a745; color:#fff; border:none; border-radius:5px; cursor:pointer;}
    button:hover {background:#218838;}
    p {text-align:center; color:red;}
  </style>
</head>
<body>
  <div class="box">
    <h2>Login</h2>
    <form method="POST">
      <input type="email" name="email" placeholder="Enter Email" required>
      <input type="password" name="password" placeholder="Enter Password" required>
      <button type="submit" name="login">Login</button>
    </form>
    <?php if(isset($error)) echo "<p>$error</p>"; ?>
  </div>
</body>
</html>

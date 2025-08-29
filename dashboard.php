<?php
session_start();

// If user not logged in, redirect back to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      padding: 50px;
      text-align: center;
    }
    .box {
      background: #fff;
      padding: 40px;
      border-radius: 10px;
      display: inline-block;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    .logout {
      margin-top: 20px;
      display: inline-block;
      padding: 10px 20px;
      background: red;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
    }
    .logout:hover {
      background: darkred;
    }
  </style>
</head>
<body>
  <div class="box">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 🎉</h1>
    <p>You are successfully logged in!</p>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</body>
</html>

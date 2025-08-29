<?php
require_once 'config.php';
require_once 'auth_check.php'; // blocks if not logged in
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard</title>
<style>
  body{font-family:system-ui,Segoe UI,Roboto,Arial;margin:0}
  header{display:flex;justify-content:space-between;align-items:center;padding:14px 18px;background:#0f172a;color:#fff}
  .brand{font-weight:700}
  .main{padding:18px}
  .btn{background:#ef4444;color:#fff;padding:8px 12px;border:none;border-radius:8px;cursor:pointer}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin-top:16px;box-shadow:0 4px 14px rgba(0,0,0,.06)}
</style>
</head>
<body>
<header>
  <div class="brand">IMS Dashboard</div>
  <div>
    Hi, <?= htmlspecialchars($_SESSION['user']['name']); ?> |
    <a href="logout.php" class="btn">Logout</a>
  </div>
</header>
<div class="main">
  <div class="card">
    <h2>Welcome</h2>
    <p>You are logged in. Only authenticated users can view this page.</p>
  </div>
</div>
</body>
</html>

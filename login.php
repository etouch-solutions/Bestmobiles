<?php require_once 'config.php'; 


 
?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login | IMS</title>
<style>
  :root { --bg:#f6f7fb; --card:#fff; --text:#222; --muted:#666; --primary:#2563eb; }
  body{margin:0;font-family:system-ui,Segoe UI,Roboto,Arial;background:var(--bg);color:var(--text);}
  .wrap{min-height:100dvh;display:grid;place-items:center;padding:24px;}
  .card{background:var(--card);width:100%;max-width:420px;border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,.08);padding:28px;}
  h1{margin:0 0 12px;font-size:22px}
  p{margin:0 0 20px;color:var(--muted)}
  label{display:block;font-size:14px;margin:12px 0 6px}
  input{width:100%;padding:12px 14px;border:1px solid #e5e7eb;border-radius:10px;font-size:16px;outline:none}
  input:focus{border-color:var(--primary);box-shadow:0 0 0 4px rgba(37,99,235,.12)}
  .row{display:flex;align-items:center;justify-content:space-between;margin:14px 0}
  .btn{width:100%;padding:12px 16px;background:var(--primary);color:#fff;border:none;border-radius:12px;font-weight:600;cursor:pointer}
  .btn:hover{filter:brightness(.95)}
  .error{background:#fee2e2;color:#991b1b;border:1px solid #fecaca;padding:10px 12px;border-radius:10px;margin-bottom:14px}
  .note{font-size:12px;color:var(--muted);margin-top:12px;text-align:center}
</style>
</head>
<body>
<div class="wrap">
  <form class="card" action="login_process.php" method="post" autocomplete="on">
    <h1>Sign in</h1>
    <p>Login to access your dashboard.</p>

    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="error"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>

    <label for="email">Email</label>
    <input id="email" name="email" type="email" required placeholder="you@example.com">

    <label for="password">Password</label>
    <input id="password" name="password" type="password" required placeholder="••••••••">

    <div class="row">
      <label style="display:flex;gap:8px;align-items:center;font-size:14px">
        <input type="checkbox" name="remember" value="1" style="width:16px;height:16px"> Remember me
      </label>
      <a href="#" style="font-size:14px;color:var(--primary);text-decoration:none" onclick="alert('Ask admin to reset password.')">Forgot?</a>
    </div>

    <button class="btn" type="submit">Login</button>
    <div class="note">Don’t have an account? Ask your admin to create one.</div>
  </form>
</div>
</body>
</html>

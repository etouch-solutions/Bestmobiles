<?php
require_once 'config.php';

// Basic rate-limit (optional)
if (!isset($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = 0;

$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password'] ?? '';
$remember = !empty($_POST['remember']);

if ($email === '' || $pass === '') {
  $_SESSION['flash_error'] = 'Email and password are required.';
  header('Location: login.php'); exit;
}

$stmt = $mysqli->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if ($user && password_verify($pass, $user['password_hash'])) {
  // Regenerate session ID to prevent fixation
  session_regenerate_id(true);
  $_SESSION['user'] = [
    'id'    => $user['id'],
    'name'  => $user['name'],
    'email' => $user['email'],
    'role'  => $user['role']
  ];

  // Simple "remember me" via cookie session extension (non-persistent token)
  if ($remember) {
    // Extend session cookie for 7 days (adjust as needed)
    $params = session_get_cookie_params();
    setcookie(session_name(), session_id(), [
      'expires'  => time() + 60*60*24*7,
      'path'     => $params['path'],
      'domain'   => $params['domain'],
      'secure'   => isset($_SERVER['HTTPS']),
      'httponly' => true,
      'samesite' => 'Lax'
    ]);
  }

  header('Location: dashboard.php'); // your app home
  exit;
}

// Fail
$_SESSION['login_attempts']++;
$_SESSION['flash_error'] = 'Invalid email or password.';
header('Location: login.php');
exit;

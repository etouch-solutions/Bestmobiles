<?php
require_once 'config.php';
$_SESSION = [];
if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time()-42000, $params['path'], $params['domain'], isset($_SERVER['HTTPS']), true);
}
session_destroy();
header('Location: login.php');
exit;

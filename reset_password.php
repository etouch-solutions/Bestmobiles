<?php
include 'db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $result = mysqli_query($conn, "SELECT * FROM Users WHERE reset_token='$token' AND reset_token_expire > NOW()");

    if (mysqli_num_rows($result) > 0) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE Users SET password='$password', reset_token=NULL, reset_token_expire=NULL WHERE reset_token='$token'");
            echo "Password has been reset. <a href='login.php'>Login</a>";
        }
        ?>
        <form method="POST">
            <h2>Reset Password</h2>
            <input type="password" name="password" placeholder="New Password" required>
            <button type="submit">Reset</button>
        </form>
        <?php
    } else {
        echo "Invalid or expired token.";
    }
}
?>

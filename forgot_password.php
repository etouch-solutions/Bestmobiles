<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php'; // database connection

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $result = mysqli_query($conn, "SELECT * FROM Users WHERE email='$email'");

    if (mysqli_num_rows($result) > 0) {
        $token = bin2hex(random_bytes(50));
        $expire = date("Y-m-d H:i:s", strtotime("+1 hour"));

        mysqli_query($conn, "UPDATE Users SET reset_token='$token', reset_token_expire='$expire' WHERE email='$email'");

        $resetLink = "http://yourdomain.com/reset_password.php?token=" . $token;

        // send mail
        $subject = "Password Reset Request";
        $message = "Click the link below to reset your password:\n\n" . $resetLink;
        $headers = "From: noreply@yourdomain.com";

        mail($email, $subject, $message, $headers);

        echo "Password reset link has been sent to your email.";
    } else {
        echo "No account found with that email.";
    }
}
?>
<form method="POST">
    <h2>Forgot Password?</h2>
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Send Reset Link</button>
</form>

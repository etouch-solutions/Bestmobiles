<?php
// popup_handler.php
if (isset($_GET['success']) || isset($_GET['error'])) {
    $type = isset($_GET['success']) ? 'success' : 'error';
    $message = htmlspecialchars($_GET['msg'] ?? '', ENT_QUOTES, 'UTF-8');
    echo "
    <style>
        .popup-msg {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            color: #fff;
            border-radius: 5px;
            font-weight: bold;
            z-index: 9999;
            opacity: 0;
            animation: slideIn 0.5s forwards, fadeOut 0.5s 3s forwards;
        }
        .popup-msg.success { background-color: #28a745; }
        .popup-msg.error { background-color: #dc3545; }
        @keyframes slideIn { from {opacity: 0; transform: translateY(-20px);} to {opacity: 1; transform: translateY(0);} }
        @keyframes fadeOut { to {opacity: 0; transform: translateY(-20px);} }
    </style>
    <div class='popup-msg $type'>$message</div>
    ";
}
?>

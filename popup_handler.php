<?php
// popup_handler.php — Include this at top of your page
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// STEP 1: Catch popup data from query string
if (isset($_GET['success']) || isset($_GET['error'])) {
    $_SESSION['popup_type'] = isset($_GET['success']) ? 'success' : 'error';
    $_SESSION['popup_message'] = $_GET['msg'] ?? '';

    // Redirect to same page without query string (Prevents loop)
    $url = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: $url");
    exit();
}

// STEP 2: Show popup only if message exists in session
if (!empty($_SESSION['popup_message'])) {
    $type = $_SESSION['popup_type'] ?? 'success';
    $msg  = htmlspecialchars($_SESSION['popup_message'], ENT_QUOTES, 'UTF-8');

    // Clear so refresh won't show again
    unset($_SESSION['popup_type'], $_SESSION['popup_message']);
    ?>
    <style>
    .site-popup {
        position: fixed;
        top: 20px;
        right: -400px; /* hidden initially */
        min-width: 260px;
        max-width: 420px;
        padding: 14px 16px;
        border-radius: 6px;
        color: #fff;
        font-weight: 600;
        z-index: 99999;
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        display: flex;
        align-items: center;
        gap: 10px;
        opacity: 0;
        transition: right 0.4s ease, opacity 0.4s ease;
    }
    .site-popup.success { background: #28a745; }
    .site-popup.error   { background: #dc3545; }
    .site-popup.show {
        right: 20px;
        opacity: 1;
    }
    </style>
    <div class="site-popup <?= $type ?>" id="sitePopup">
        <?= $type === 'success' ? '✅' : '❌' ?> <?= $msg ?>
    </div>
    <script>
        window.onload = function() {
            var el = document.getElementById('sitePopup');
            if (el) {
                setTimeout(() => el.classList.add('show'), 100);
                setTimeout(() => {
                    el.classList.remove('show');
                    setTimeout(() => el.remove(), 400);
                }, 3500);
            }
        };
    </script>
    <?php
}
?>

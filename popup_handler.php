<?php
// popup_handler.php
session_start();

// Step 1: If URL has popup parameters, save them in session and redirect
if (isset($_GET['success']) || isset($_GET['error'])) {
    $_SESSION['popup_type'] = isset($_GET['success']) ? 'success' : 'error';
    $_SESSION['popup_message'] = $_GET['msg'] ?? '';
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

// Step 2: Show popup if stored in session
if (!empty($_SESSION['popup_message'])) {
    $type = $_SESSION['popup_type'];
    $msg = htmlspecialchars($_SESSION['popup_message'], ENT_QUOTES, 'UTF-8');

    // Clear from session so it doesn't reappear
    unset($_SESSION['popup_message'], $_SESSION['popup_type']);
    ?>
    <style>
    .site-popup {
        color: #000000;
      position: fixed;
      top: 20px;
      right: 20px;
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
      animation: popupIn 0.35s ease;
    }
    .site-popup.success { background: #86ffa2ff; }
    .site-popup.error   { background: #ff929dff; }
    .site-popup .close { cursor: pointer; font-size: 18px; opacity: 0.9; }
    @keyframes popupIn { from { transform: translateY(-10px); opacity: 0 } to { transform: translateY(0); opacity: 1 } }
    </style>

    <div id="sitePopup" class="site-popup <?= $type ?>">
      <div style="flex:1;line-height:1.2;">
        <?= $type === 'success' ? '✅' : '❌' ?>
        <span style="margin-left:8px;"><?= $msg ?></span>
      </div>
      <div class="close" onclick="closeSitePopup()">×</div>
    </div>

    <script>
    function closeSitePopup(){
      const el = document.getElementById('sitePopup');
      if(!el) return;
      el.style.transition = 'opacity .25s, transform .25s';
      el.style.opacity = 0;
      el.style.transform = 'translateY(-8px)';
      setTimeout(()=> el.remove(), 300);
    }
    setTimeout(()=> { if (document.getElementById('sitePopup')) closeSitePopup(); }, 3500);
    </script>
    <?php
}
?>

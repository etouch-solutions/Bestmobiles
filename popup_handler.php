<?php
// popup_handler.php
// This file prints a one-time popup if the URL has ?success=1&msg=... or ?error=1&msg=...
if (!isset($_GET['success']) && !isset($_GET['error'])) {
    return;
}
$type = isset($_GET['success']) ? 'success' : 'error';
$msg = isset($_GET['msg']) ? htmlspecialchars(urldecode($_GET['msg']), ENT_QUOTES, 'UTF-8') : '';
?>
<style>
/* simple popup - adjust CSS to match your styles.css if needed */
.site-popup {
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
.site-popup.success { background: #28a745; }
.site-popup.error   { background: #dc3545; }
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
// auto close after 3.5s
setTimeout(()=> {
  if (document.getElementById('sitePopup')) closeSitePopup();
}, 3500);
</script>

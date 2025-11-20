<?php
// messages.php
require_once __DIR__ . '/includes/functions.php';
require_login();
include __DIR__ . '/includes/header.php';
$other_id = intval($_GET['u'] ?? 0);
?>
<div style="max-width:900px;margin:20px auto;">
  <h2>Mesajlar</h2>
  <div id="chat-box" style="height:400px;overflow:auto;border:1px solid #ddd;padding:8px;background:#fff;"></div>

  <form id="chat-form" style="margin-top:8px;">
    <input type="hidden" name="recipient_id" value="<?=htmlspecialchars($other_id)?>">
    <textarea name="body" rows="3" required style="width:100%"></textarea><br>
    <button type="submit">Gönder</button>
  </form>
</div>

<script src="/assets/chat.js"></script>

<?php include __DIR__ . '/includes/footer.php'; ?>

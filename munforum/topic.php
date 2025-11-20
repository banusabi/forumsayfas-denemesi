<?php
// topic.php
require_once __DIR__ . '/includes/functions.php';

$topic_id = intval($_GET['id'] ?? 0);
if ($topic_id <= 0) {
    header('Location: /index.php');
    exit;
}

// Get topic data
$stmt = $mysqli->prepare("SELECT t.*, u.username FROM topics t LEFT JOIN users u ON u.id = t.user_id WHERE t.id = ? LIMIT 1");
$stmt->bind_param('i', $topic_id);
$stmt->execute();
$res = $stmt->get_result();
$topic = $res->fetch_assoc();
$stmt->close();
if (!$topic) {
    die('Konu bulunamadı.');
}

// log view with unique (topic_id, ip)
$ip = get_ip();
$stmt = $mysqli->prepare("INSERT IGNORE INTO view_logs (topic_id, ip, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param('is', $topic_id, $ip);
$stmt->execute();
if ($stmt->affected_rows > 0) {
    // yeni benzersiz görüntüleme eklendi -> increment topics.views
    $up = $mysqli->prepare("UPDATE topics SET views = views + 1 WHERE id = ?");
    $up->bind_param('i', $topic_id);
    $up->execute();
    $up->close();
}
$stmt->close();

// show topic and replies...
include __DIR__ . '/includes/header.php';
?>
<div style="max-width:900px;margin:20px auto;padding:16px;">
  <h2><?=htmlspecialchars($topic['title'])?></h2>
  <div>Yazan: <?=htmlspecialchars($topic['username'] ?? 'Anonim')?> - Görüntülenme: <?=intval($topic['views'])?></div>
  <div style="margin-top:12px;"><?=nl2br(htmlspecialchars($topic['body']))?></div>
  <hr>
  <h3>Cevaplar</h3>
  <?php
  $r = $mysqli->query("SELECT r.*, u.username FROM replies r LEFT JOIN users u ON u.id = r.user_id WHERE r.topic_id = ".intval($topic_id)." ORDER BY r.created_at ASC");
  while($rep = $r->fetch_assoc()){
      echo '<div style="padding:8px;border-bottom:1px solid #eee;">';
      echo '<strong>'.htmlspecialchars($rep['username']).'</strong> <small>'.$rep['created_at'].'</small>';
      echo '<div>'.nl2br(htmlspecialchars($rep['body'])).'</div>';
      echo '</div>';
  }
  ?>
  <?php if(is_logged_in()): ?>
    <form action="/reply_handler.php" method="post" style="margin-top:16px;">
      <input type="hidden" name="topic_id" value="<?=intval($topic_id)?>">
      <textarea name="body" rows="4" required></textarea><br>
      <button type="submit">Cevapla</button>
    </form>
  <?php else: ?>
    <div>Yanıt yazmak için <a href="/login.php">giriş</a> yapın.</div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>

<?php
require 'header.php';
$rows = $pdo->query("SELECT c.*, city.name as city_name, u.username as organizer FROM conferences c LEFT JOIN cities city ON city.id=c.city_id LEFT JOIN users u ON u.id=c.created_by ORDER BY c.start_date DESC")->fetchAll();
?>
<div class="card">
  <h2>Tüm Konferanslar</h2>
  <div style="margin-bottom:12px">
    <?php if(isset($_SESSION['user'])): ?>
      <a class="btn" href="/munforum/conference_create.php">Yeni Konferans Ekle</a>
    <?php else: ?>
      <div class="small">Konferans eklemek için giriş yapın.</div>
    <?php endif; ?>
  </div>

  <?php foreach($rows as $r): ?>
    <div style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.03)">
      <a href="/munforum/conference.php?id=<?=$r['id']?>"><?=htmlspecialchars($r['title'])?></a>
      <div class="small"><?=htmlspecialchars($r['city_name'])?> • <?=htmlspecialchars($r['start_date'])?> - <?=htmlspecialchars($r['end_date'])?> • Organizatör: <?=htmlspecialchars($r['organizer'])?></div>
    </div>
  <?php endforeach; ?>
</div>
<?php require 'footer.php'; ?>

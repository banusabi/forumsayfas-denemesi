<?php
require 'header.php';

// Trending and Latest
$trending = $pdo->query("SELECT t.*, u.username FROM topics t JOIN users u ON u.id=t.user_id ORDER BY score DESC LIMIT 10")->fetchAll();
$latest = $pdo->query("SELECT t.*, u.username FROM topics t JOIN users u ON u.id=t.user_id ORDER BY created_at DESC LIMIT 10")->fetchAll();

// upcoming conferences (next 5)
$upcoming = $pdo->query("SELECT c.*, city.name as city_name FROM conferences c LEFT JOIN cities city ON city.id=c.city_id WHERE c.start_date >= CURDATE() ORDER BY c.start_date ASC LIMIT 5")->fetchAll();

// top5 users by points
$top5 = $pdo->query("SELECT id, username, points FROM users ORDER BY points DESC LIMIT 5")->fetchAll();

// random featured topic
$rand = $pdo->query("SELECT t.id,t.title FROM topics t ORDER BY RAND() LIMIT 1")->fetch();

// extra stats box data
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_topics = $pdo->query("SELECT COUNT(*) FROM topics")->fetchColumn();
$total_replies = $pdo->query("SELECT COUNT(*) FROM replies")->fetchColumn();
$last_user_row = $pdo->query("SELECT id,username,created_at FROM users ORDER BY created_at DESC LIMIT 1")->fetch();

?>
<section class="card">
 
  <div>
    <h3>Popüler Konular</h3>
    <?php foreach($trending as $t): ?>
      <div class="topic">
        <h3><a href="/munforum/topic.php?id=<?=$t['id']?>"><?=htmlspecialchars($t['title'])?></a></h3>
        <div class="small"><a class="pseudo-link" href="/munforum/profile.php?id=<?=$t['user_id']?>"><?=htmlspecialchars($t['username'])?></a> • <a href="/munforum/topic.php?id=<?=$t['id']?>">Yanıt: <?=htmlspecialchars($t['replies_count'])?></a> • <a href="/munforum/topic.php?id=<?=$t['id']?>">Görüntülenme: <?=round($t['views'],2)?></a></div>
      </div>
    <?php endforeach; ?>
    <h3>Yeni Konular</h3>
    <?php foreach($latest as $t): ?>
      <div class="topic">
        <h3><a href="/munforum/topic.php?id=<?=$t['id']?>"><?=htmlspecialchars($t['title'])?></a></h3>
        <div class="small"><a class="pseudo-link" href="/munforum/profile.php?id=<?=$t['user_id']?>"><?=htmlspecialchars($t['username'])?></a> • <?=htmlspecialchars($t['replies_count'])?> Yanıtlar</div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<aside class="sidebar">
  <div class="card widget">
    <h4>Yaklaşan MUN'lar</h4>
    <?php foreach($upcoming as $c): ?>
      <div><a href="/munforum/conference.php?id=<?=$c['id']?>"><?=htmlspecialchars($c['title'])?></a><div class="small"><?=htmlspecialchars($c['city_name'])?> • <?=htmlspecialchars($c['start_date'])?></div></div>
    <?php endforeach; ?>
  </div>

  <div class="card widget">
    <h4>Top 5 Kullanıcı</h4>
    <?php foreach($top5 as $u): ?>
      <div><a href="/munforum/profile.php?id=<?=$u['id']?>"><?=htmlspecialchars($u['username'])?></a> <span class="small">(<?=htmlspecialchars($u['points'])?>)</span></div>
    <?php endforeach; ?>
  </div>

  <div class="card widget">
    <h4>Rastgele Tartışma</h4>
    <?php if($rand): ?>
      <div><a href="/munforum/topic.php?id=<?=$rand['id']?>"><?=htmlspecialchars($rand['title'])?></a></div>
    <?php else: ?>
      <div class="small">Henüz tartışma yok.</div>
    <?php endif; ?>
  </div>

  <div class="card widget">
    <h4>İstatistikler</h4>
    <div class="stat-box">
      <div class="stat-row"><div class="small">Toplam Üye</div><div><?=htmlspecialchars($total_users)?></div></div>
      <div class="stat-row"><div class="small">Toplam Tartışma</div><div><?=htmlspecialchars($total_topics)?></div></div>
      <div class="stat-row"><div class="small">Toplam Yanıt</div><div><?=htmlspecialchars($total_replies)?></div></div>
      <div class="stat-row"><div class="small">Son Üye</div><div><?php if($last_user_row): ?><a href="/munforum/profile.php?id=<?=$last_user_row['id']?>"><?=htmlspecialchars($last_user_row['username'])?></a><?php endif;?></div></div>
    </div>
  </div>

  <div class="card widget center">
    <a class="btn" href="/munforum/new_topic.php">Yeni Tartışma Aç</a>
  </div>
</aside>

<?php require 'footer.php'; ?>

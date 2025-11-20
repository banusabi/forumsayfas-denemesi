<?php
require 'header.php';
$id = intval($_GET['id'] ?? 0);
if(!$id) { echo "<div class='card'>Kullanıcı bulunamadı</div>"; require 'footer.php'; exit; }

$stmt = $pdo->prepare("SELECT u.id,u.username,u.created_at,u.points,u.city_id,p.full_name,p.university,p.bio,p.avatar,p.delegation_history FROM users u LEFT JOIN user_profiles p ON p.user_id = u.id WHERE u.id = :id");
$stmt->execute(['id'=>$id]);
$user = $stmt->fetch();
if(!$user){ echo "<div class='card'>Kullanıcı bulunamadı</div>"; require 'footer.php'; exit; }

// city name
$cityName = null;
if($user['city_id']){
    $c = $pdo->prepare("SELECT name FROM cities WHERE id=:id");
    $c->execute(['id'=>$user['city_id']]);
    $cityName = $c->fetchColumn();
}

// friend status between current user and viewed profile
$me = $_SESSION['user']['id'] ?? null;
$friend_status = null;
if($me && $me != $user['id']){
    $f = $pdo->prepare("SELECT * FROM friends WHERE (requester_id=:me AND recipient_id=:u) OR (requester_id=:u AND recipient_id=:me) LIMIT 1");
    $f->execute(['me'=>$me,'u'=>$user['id']]);
    $fr = $f->fetch();
    if($fr) $friend_status = $fr['status'];
}

// recent posts
$posts = $pdo->prepare("SELECT id,title,created_at FROM topics WHERE user_id = :id ORDER BY created_at DESC LIMIT 10");
$posts->execute(['id'=>$id]);
$posts = $posts->fetchAll();
?>
<div class="card">
  <div style="display:flex;gap:12px;align-items:center">
    <div class="avatar-sm"></div>
    <div>
      <h2><?=htmlspecialchars($user['username'])?></h2>
      <div class="small"><?=htmlspecialchars($user['full_name'] ?? '')?> <?= $cityName ? "• " . htmlspecialchars($cityName) : '' ?></div>
      <div class="small">Üyelik: <?=htmlspecialchars($user['created_at'])?> • Puan: <?=htmlspecialchars($user['points'])?></div>
    </div>
    <div style="margin-left:auto">
      <?php if(isset($_SESSION['user']) && $_SESSION['user']['id'] == $user['id']): ?>
        <a class="btn" href="/munforum/profile_edit.php">Profili Düzenle</a>
      <?php elseif(isset($_SESSION['user'])): ?>
        <?php if($friend_status === 'accepted'): ?>
          <span class="small">Arkadaşsınız</span>
        <?php elseif($friend_status === 'pending'): ?>
          <span class="small">Arkadaşlık isteği bekliyor</span>
        <?php else: ?>
          <form method="post" action="/munforum/friend_request.php" style="display:inline">
            <input type="hidden" name="to" value="<?=$user['id']?>">
            <button class="btn">Arkadaş Ekle</button>
          </form>
        <?php endif; ?>
        <a class="btn" href="/munforum/messages.php?with=<?=$user['id']?>">Mesaj Gönder</a>
      <?php endif; ?>
    </div>
  </div>

  <hr style="border:none;border-top:1px solid rgba(255,255,255,0.03);margin:12px 0">

  <h3>Hakkında</h3>
  <div><?=nl2br(htmlspecialchars($user['bio'] ?? 'Henüz biyografi eklenmemiş.'))?></div>
  <h3 style="margin-top:12px">Eğitim / Delegasyon</h3>
  <div class="small"><?=htmlspecialchars($user['university'] ?? 'Belirtilmemiş')?></div>
  <div style="margin-top:8px"><?=nl2br(htmlspecialchars($user['delegation_history'] ?? 'Delegasyon bilgisi yok.'))?></div>

  <h3 style="margin-top:12px">Son Paylaşımlar</h3>
  <?php foreach($posts as $p): ?>
    <div><a href="/munforum/topic.php?id=<?=$p['id']?>"><?=htmlspecialchars($p['title'])?></a> <span class="small"><?=htmlspecialchars($p['created_at'])?></span></div>
  <?php endforeach; ?>
</div>

<?php require 'footer.php'; ?>

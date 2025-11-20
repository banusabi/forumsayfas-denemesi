<?php
// friends.php
require_once __DIR__ . '/includes/functions.php';
require_login();
include __DIR__ . '/includes/header.php';
$me = current_user_id();
?>
<div style="max-width:900px;margin:20px auto;">
  <h2>Arkadaşlar ve İstekler</h2>
  <div>
    <h3>Gelen İstekler</h3>
    <?php
    $res = $mysqli->query("SELECT f.*, u.username FROM friends f JOIN users u ON u.id = f.requester_id WHERE f.recipient_id = ".intval($me)." AND f.status='pending'");
    while($r = $res->fetch_assoc()){
      echo '<div>'.htmlspecialchars($r['username']).' <a href="/ajax/friend_action.php?action=accept&id='.$r['id'].'">Kabul et</a> <a href="/ajax/friend_action.php?action=reject&id='.$r['id'].'">Reddet</a></div>';
    }
    ?>
  </div>

  <div style="margin-top:12px;">
    <h3>Arkadaşlarım</h3>
    <?php
    $res = $mysqli->query("SELECT u.id,u.username FROM friends f JOIN users u ON (u.id = f.requester_id OR u.id = f.recipient_id) WHERE (f.requester_id = ".intval($me)." OR f.recipient_id = ".intval($me).") AND f.status='accepted' AND u.id != ".intval($me));
    while($r = $res->fetch_assoc()){
      echo '<div><a href="/profile.php?id='.intval($r['id']).'">'.htmlspecialchars($r['username']).'</a></div>';
    }
    ?>
  </div>

  <div style="margin-top:12px;">
    <h3>Arkadaş Ara</h3>
    <form method="get" action="/friends.php">
      <input type="text" name="q" placeholder="Kullanıcı adı">
      <button type="submit">Ara</button>
    </form>
    <?php
    if (!empty($_GET['q'])) {
      $q = $mysqli->real_escape_string($_GET['q']);
      $r = $mysqli->query("SELECT id,username FROM users WHERE username LIKE '%".$q."%' AND id != ".intval($me)." LIMIT 10");
      while($u = $r->fetch_assoc()){
        echo '<div>'.htmlspecialchars($u['username']).' <a href="/ajax/friend_request.php?to='.intval($u['id']).'">İstek Gönder</a></div>';
      }
    }
    ?>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>

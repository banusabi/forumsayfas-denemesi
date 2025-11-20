<?php
// create_topic.php
require_once __DIR__ . '/includes/functions.php';
require_login();

include __DIR__ . '/includes/header.php';
?>
<div style="max-width:900px;margin:20px auto;padding:16px;border:1px solid #eee;border-radius:6px;">
  <h2>Yeni Konu Oluştur</h2>
  <?php if(!empty($_GET['error'])): ?>
    <div style="color:red;"><?=htmlspecialchars($_GET['error'])?></div>
  <?php endif; ?>
  <form action="/create_topic_handler.php" method="post">
    <label>Başlık<br><input type="text" name="title" required maxlength="255"></label><br><br>
    <label>Kategori<br>
      <select name="category_id" required>
        <option value="">-- Kategori seç --</option>
        <?php
        $role = current_user_role();
        // Admin/moderator görebilsin; normal user 'is_key_topic' olan kategorileri görmesin
        if ($role === 'admin' || $role === 'moderator') {
            $res = $mysqli->query("SELECT id,name,is_key_topic FROM categories ORDER BY is_key_topic DESC, name");
        } else {
            $res = $mysqli->query("SELECT id,name,is_key_topic FROM categories WHERE is_key_topic = 0 OR is_key_topic IS NULL ORDER BY name");
        }
        while($r = $res->fetch_assoc()) {
            echo '<option value="'.intval($r['id']).'">'.htmlspecialchars($r['name']);
            if($r['is_key_topic']) echo ' (Anahtar)';
            echo '</option>';
        }
        ?>
      </select>
    </label><br><br>

    <label>İçerik<br><textarea name="body" required rows="8"></textarea></label><br><br>

    <label>Şehir (zorunlu)<br>
      <select name="city_id" required>
        <option value="">-- Şehir seç --</option>
        <?php
        $res2 = $mysqli->query("SELECT id,name FROM cities ORDER BY name");
        while($c = $res2->fetch_assoc()) {
            echo '<option value="'.intval($c['id']).'">'.htmlspecialchars($c['name']).'</option>';
        }
        ?>
      </select>
    </label><br><br>

    <button type="submit">Konu Oluştur</button>
  </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>

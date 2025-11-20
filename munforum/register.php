<?php
// register.php
require_once __DIR__ . '/includes/functions.php';
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<div style="max-width:700px;margin:20px auto;padding:16px;border:1px solid #eee;border-radius:6px;">
  <h2>Kayıt Ol</h2>
  <?php if(!empty($_GET['error'])): ?>
    <div style="color:red;margin-bottom:10px;"><?=htmlspecialchars($_GET['error'])?></div>
  <?php endif; ?>
  <form action="/register_handler.php" method="post">
    <label>Kullanıcı adı<br><input type="text" name="username" required maxlength="50"></label><br><br>
    <label>E-posta<br><input type="email" name="email" required maxlength="255"></label><br><br>
    <label>Şifre<br><input type="password" name="password" required></label><br><br>
    <label>Şehir (zorunlu)<br>
      <select name="city_id" required>
        <option value="">-- Şehir seç --</option>
        <?php
        $res = $mysqli->query("SELECT id,name FROM cities ORDER BY name");
        while($r = $res->fetch_assoc()) {
            echo '<option value="'.intval($r['id']).'">'.htmlspecialchars($r['name']).'</option>';
        }
        ?>
      </select>
    </label><br><br>
    <button type="submit">Kayıt Ol</button>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

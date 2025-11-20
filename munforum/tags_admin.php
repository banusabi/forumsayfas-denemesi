<?php
require 'header.php';
if(!isset($_SESSION['user'])){ header('Location: /munforum/login.php'); exit; }
if($_SESSION['user']['username'] !== 'admin'){ echo "<div class='card'>Yetkisiz</div>"; require 'footer.php'; exit; }

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $slug = preg_replace('/[^a-z0-9]+/i','-',strtolower($name));
    if($name){
        $stmt = $pdo->prepare("INSERT IGNORE INTO tags (name,slug) VALUES (:n,:s)");
        $stmt->execute(['n'=>$name,'s'=>$slug]);
    }
    header('Location: /munforum/tags_admin.php'); exit;
}

$tags = $pdo->query("SELECT * FROM tags ORDER BY name")->fetchAll();
?>
<div class="card">
  <h2>Tag Yönetimi</h2>
  <form method="post">
    <div class="form-row"><input name="name" placeholder="Yeni tag"></div>
    <div class="form-row"><button class="btn">Ekle</button></div>
  </form>
  <h3>Mevcut Tagler</h3>
  <?php foreach($tags as $t): ?><div class="small"><?=htmlspecialchars($t['name'])?></div><?php endforeach; ?>
</div>
<?php require 'footer.php'; ?>

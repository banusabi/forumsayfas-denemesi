<?php
require 'header.php';
if(!isset($_SESSION['user'])) { header('Location: /munforum/login.php'); exit; }

$errors = [];
$tags = $pdo->query("SELECT * FROM tags ORDER BY name")->fetchAll();
$cities = $pdo->query("SELECT * FROM cities ORDER BY name")->fetchAll();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $tag_ids = $_POST['tags'] ?? [];
    $city_id = intval($_POST['city_id'] ?? 0);

    if(strlen($title) < 5) $errors[] = "Başlık kısa.";
    if(strlen($body) < 5) $errors[] = "İçerik kısa.";

    if(empty($errors)){
        $slug = substr(preg_replace('/[^a-z0-9]+/i','-',strtolower($title)),0,200);
        $ins = $pdo->prepare("INSERT INTO topics (user_id,title,slug,body,city_id) VALUES (:uid,:t,:s,:b,:c)");
        $ins->execute(['uid'=>$_SESSION['user']['id'],'t'=>$title,'s'=>$slug,'b'=>$body,'c'=> $city_id ?: null]);
        $tid = $pdo->lastInsertId();

        // attach tags (only allow existing tag ids)
        if($tag_ids && is_array($tag_ids)){
            $st = $pdo->prepare("INSERT IGNORE INTO topic_tags (topic_id,tag_id) VALUES (:tid,:tag)");
            foreach($tag_ids as $tag) {
                $st->execute(['tid'=>$tid,'tag'=>intval($tag)]);
            }
        }

        // add points
        $pdo->prepare("INSERT INTO points_log (user_id,action,points,related_type,related_id) VALUES (:uid,'new_topic',10,'topic',:tid)")
            ->execute(['uid'=>$_SESSION['user']['id'],'tid'=>$tid]);

        $pdo->prepare("UPDATE users SET points = points + 10 WHERE id = :uid")->execute(['uid'=>$_SESSION['user']['id']]);

        header("Location: /munforum/topic.php?id={$tid}"); exit;
    }
}
?>
<div class="card">
  <h2>Yeni Tartışma Aç</h2>
  <?php if($errors) foreach($errors as $e) echo "<div class='small' style='color:red'>$e</div>"; ?>
  <form method="post">
    <div class="form-row"><input name="title" placeholder="Başlık" value="<?=htmlspecialchars($_POST['title'] ?? '')?>"></div>
    <div class="form-row"><textarea name="body" rows="8" placeholder="İçerik"><?=htmlspecialchars($_POST['body'] ?? '')?></textarea></div>
    <div class="form-row">
      <select name="city_id">
        <option value="">Şehir (isteğe bağlı)</option>
        <?php foreach($cities as $c): ?>
          <option value="<?=$c['id']?>" <?=isset($_POST['city_id']) && $_POST['city_id']==$c['id']?'selected':''?>><?=htmlspecialchars($c['name'])?></option>
        <?php endforeach;?>
      </select>
    </div>
    <div class="form-row">
      <label>Etiketler</label>
      <select name="tags[]" multiple>
        <?php foreach($tags as $t): ?>
          <option value="<?=$t['id']?>"><?=htmlspecialchars($t['name'])?></option>
        <?php endforeach;?>
      </select>
    </div>
    <div class="form-row"><button class="btn">Paylaş</button></div>
  </form>
</div>
<?php require 'footer.php'; ?>

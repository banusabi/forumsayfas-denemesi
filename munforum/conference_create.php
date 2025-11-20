<?php
// conference_create.php
require 'header.php';
if(!isset($_SESSION['user'])) { header('Location: /munforum/login.php'); exit; }

$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = trim($_POST['title'] ?? '');
    $city_id = intval($_POST['city_id'] ?? 0);
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $details = trim($_POST['details'] ?? '');
    $type = trim($_POST['type'] ?? '');

    if(strlen($title) < 5) $errors[] = "Başlık kısa.";
    if(!$start_date) $errors[] = "Başlangıç tarihi gir.";
    if(!$end_date) $errors[] = "Bitiş tarihi gir.";
    if(empty($errors)){
        $slug = substr(preg_replace('/[^a-z0-9]+/i','-',strtolower($title)),0,200);
        $ins = $pdo->prepare("INSERT INTO conferences (title,slug,city_id,start_date,end_date,type,details,created_by) VALUES (:t,:s,:c,:st,:en,:ty,:det,:cb)");
        $ins->execute([
            't'=>$title,'s'=>$slug,'c'=>$city_id ?: null,'st'=>$start_date,'en'=>$end_date,'ty'=>$type,'det'=>$details,'cb'=>$_SESSION['user']['id']
        ]);
        $cid = $pdo->lastInsertId();
        header("Location: /munforum/conference.php?id={$cid}"); exit;
    }
}

$cities = $pdo->query("SELECT * FROM cities ORDER BY name")->fetchAll();
?>
<div class="card">
  <h2>Yeni Konferans Ekle</h2>
  <?php if($errors) foreach($errors as $e) echo "<div class='small' style='color:var(--danger)'>$e</div>"; ?>
  <form method="post">
    <div class="form-row"><input name="title" placeholder="Konferans başlığı" value="<?=htmlspecialchars($_POST['title'] ?? '')?>"></div>
    <div class="form-row">
      <select name="city_id">
        <option value="">Şehir (isteğe bağlı)</option>
        <?php foreach($cities as $c): ?>
          <option value="<?=$c['id']?>" <?=isset($_POST['city_id']) && $_POST['city_id']==$c['id']?'selected':''?>><?=htmlspecialchars($c['name'])?></option>
        <?php endforeach;?>
      </select>
    </div>
    <div class="form-row"><input name="start_date" type="date" value="<?=htmlspecialchars($_POST['start_date'] ?? '')?>"></div>
    <div class="form-row"><input name="end_date" type="date" value="<?=htmlspecialchars($_POST['end_date'] ?? '')?>"></div>
    <div class="form-row"><input name="type" placeholder="Konferans türü (ör: General Assembly)" value="<?=htmlspecialchars($_POST['type'] ?? '')?>"></div>
    <div class="form-row"><textarea name="details" rows="6" placeholder="Konferans detayları"><?=htmlspecialchars($_POST['details'] ?? '')?></textarea></div>
    <div class="form-row"><button class="btn">Kaydet</button></div>
  </form>
</div>

<?php require 'footer.php'; ?>

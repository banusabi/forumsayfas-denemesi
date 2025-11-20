<?php
require 'header.php';
$id = intval($_GET['id'] ?? 0);
if(!$id){ echo "<div class='card'>Konferans bulunamadı</div>"; require 'footer.php'; exit; }
$stmt = $pdo->prepare("SELECT c.*, city.name as city_name, u.username as organizer FROM conferences c LEFT JOIN cities city ON city.id=c.city_id LEFT JOIN users u ON u.id=c.created_by WHERE c.id = :id");
$stmt->execute(['id'=>$id]);
$c = $stmt->fetch();
if(!$c){ echo "<div class='card'>Konferans bulunamadı</div>"; require 'footer.php'; exit; }
?>
<div class="card">
  <h2><?=htmlspecialchars($c['title'])?></h2>
  <div class="small"><?=htmlspecialchars($c['city_name'])?> • <?=htmlspecialchars($c['start_date'])?> - <?=htmlspecialchars($c['end_date'])?></div>
  <div style="margin-top:12px"><?=nl2br(htmlspecialchars($c['details']))?></div>
  <div class="small">Organizatör: <?=htmlspecialchars($c['organizer'])?></div>
</div>
<?php require 'footer.php'; ?>

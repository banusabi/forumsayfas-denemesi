<?php
// widgets/popular_weekly.php
require_once __DIR__ . '/../includes/functions.php';

$query = "
SELECT t.id, t.title, COUNT(v.id) AS views_in_week
FROM topics t
LEFT JOIN view_logs v ON v.topic_id = t.id AND v.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY t.id
ORDER BY views_in_week DESC
LIMIT 10
";
$res = $mysqli->query($query);
?>
<div class="widget">
  <h3>Haftanın Popüler Konuları</h3>
  <ul>
  <?php while($row = $res->fetch_assoc()): ?>
    <li><a href="/topic.php?id=<?=intval($row['id'])?>"><?=htmlspecialchars($row['title'])?></a> (<?=intval($row['views_in_week'])?>)</li>
  <?php endwhile; ?>
  </ul>
</div>

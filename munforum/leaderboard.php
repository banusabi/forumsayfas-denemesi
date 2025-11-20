<?php
require 'header.php';
$users = $pdo->query("SELECT id,username,points FROM users ORDER BY points DESC LIMIT 50")->fetchAll();
?>
<div class="card">
  <h2>Leaderboard</h2>
  <ol>
    <?php foreach($users as $u): ?>
      <li><?=htmlspecialchars($u['username'])?> — <?=htmlspecialchars($u['points'])?> puan</li>
    <?php endforeach;?>
  </ol>
</div>
<?php require 'footer.php'; ?>

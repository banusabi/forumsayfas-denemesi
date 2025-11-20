<?php
// header.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';
$config = require __DIR__ . '/config.php';

function current_user() {
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}
$user = current_user();
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <title>MUN TURKEY Forum</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/munforum/styles.css">
</head>
<body>
<header class="site-header">
  <div class="wrap">
    <a href="/munforum/index.php" class="logo">MUN TURKEY</a>
    <nav class="topnav">
      <a href="/munforum/index.php">Forum</a>
      <a href="/munforum/conferences.php">MUN'lar</a>
      <a href="/munforum/leaderboard.php">Top 5</a>
      <?php if($user): ?>
        <a href="/munforum/profile.php?id=<?=htmlspecialchars($user['id'])?>"><?=htmlspecialchars($user['username'])?></a>
        <a href="/munforum/logout.php">Çıkış</a>
      <?php else: ?>
        <a href="/munforum/login.php">Giriş</a>
        <a href="/munforum/register.php">Kayıt</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="wrap main-grid">

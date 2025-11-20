<?php
// includes/header.php
require_once __DIR__ . '/functions.php';
?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>MUN Forum</title>
<link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header class="site-header" style="display:flex;align-items:center;justify-content:space-between;padding:10px 20px;border-bottom:1px solid #eee;">
  <div class="brand">
    <a href="/index.php">MUN Forum</a>
  </div>
  <nav style="display:flex;align-items:center;gap:10px;">
    <?php if(is_logged_in()): ?>
      <a href="/profile.php?id=<?=htmlspecialchars(current_user_id())?>">Profilim</a>
      <a href="/messages.php">DM'ler</a>
      <a href="/friends.php">Arkadaşlar</a>
      <a href="/logout.php">Çıkış</a>
    <?php else: ?>
      <a href="/login.php" style="order:2;">Giriş</a>
      <a href="/register.php" style="order:1;padding:8px 12px;border-radius:6px;background:#2b7cff;color:#fff;">Kayıt Ol</a>
    <?php endif; ?>
  </nav>
</header>

<!-- Yeni konu butonu: header içinde değil, ama sayfa üstünde sabit görünür -->
<div style="max-width:1000px;margin:18px auto;padding:0 16px;">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
    <h1 style="margin:0">Forum</h1>
    <?php if(is_logged_in()): ?>
      <a href="/create_topic.php" style="padding:8px 14px;border-radius:6px;background:#0b8d4f;color:#fff;text-decoration:none;">+ Yeni Konu Oluştur</a>
    <?php else: ?>
      <a href="/login.php" style="padding:8px 14px;border-radius:6px;background:#0b8d4f;color:#fff;text-decoration:none;">Giriş yapıp konu aç</a>
    <?php endif; ?>
  </div>

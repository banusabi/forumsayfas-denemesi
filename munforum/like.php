<?php
require 'db.php';
session_start();
if(!isset($_SESSION['user'])) { header('Location: /munforum/login.php'); exit; }

$type = $_POST['type'] ?? '';
$id = intval($_POST['id'] ?? 0);
$uid = $_SESSION['user']['id'];
if(!in_array($type,['topic','reply']) || !$id) { header('Location: /munforum/index.php'); exit; }

// toggle: mevcutsa sil, yoksa ekle
$check = $pdo->prepare("SELECT id FROM likes WHERE user_id=:uid AND target_type=:t AND target_id=:id");
$check->execute(['uid'=>$uid,'t'=>$type,'id'=>$id]);
$exists = $check->fetch();

if($exists){
    $pdo->prepare("DELETE FROM likes WHERE id = :id")->execute(['id'=>$exists['id']]);
    // optionally reduce points for un-like
} else {
    $pdo->prepare("INSERT INTO likes (user_id,target_type,target_id) VALUES (:uid,:t,:id)")
        ->execute(['uid'=>$uid,'t'=>$type,'id'=>$id]);

    // award points to owner if topic liked
    if($type === 'topic'){
        // find topic owner
        $owner = $pdo->prepare("SELECT user_id FROM topics WHERE id=:id")->execute(['id'=>$id]);
        $ownerId = $pdo->prepare("SELECT user_id FROM topics WHERE id=:id")->fetchColumn();
        if($ownerId && $ownerId != $uid) {
            $pdo->prepare("INSERT INTO points_log (user_id,action,points,related_type,related_id) VALUES (:uid,'liked',5,'topic',:id)")
                ->execute(['uid'=>$ownerId,'id'=>$id]);
            $pdo->prepare("UPDATE users SET points = points + 5 WHERE id = :uid")->execute(['uid'=>$ownerId]);
        }
    }
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/munforum/index.php'));
exit;

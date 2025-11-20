<?php
require 'db.php';
session_start();
if(!isset($_SESSION['user'])) { header('Location: /munforum/login.php'); exit; }

$topic_id = intval($_POST['topic_id'] ?? 0);
$body = trim($_POST['body'] ?? '');
if(!$topic_id || strlen($body) < 2) { header("Location: /munforum/topic.php?id={$topic_id}"); exit; }

// insert
$ins = $pdo->prepare("INSERT INTO replies (topic_id,user_id,body) VALUES (:tid,:uid,:b)");
$ins->execute(['tid'=>$topic_id,'uid'=>$_SESSION['user']['id'],'b'=>$body]);

// update replies_count
$pdo->prepare("UPDATE topics SET replies_count = replies_count + 1 WHERE id = :tid")->execute(['tid'=>$topic_id]);

// points
$pdo->prepare("INSERT INTO points_log (user_id,action,points,related_type,related_id) VALUES (:uid,'reply',3,'topic',:tid)")
    ->execute(['uid'=>$_SESSION['user']['id'],'tid'=>$topic_id]);
$pdo->prepare("UPDATE users SET points = points + 3 WHERE id = :uid")->execute(['uid'=>$_SESSION['user']['id']]);

// notify topic owner (in-app)
$owner = $pdo->prepare("SELECT user_id FROM topics WHERE id = :tid")->execute(['tid'=>$topic_id]);
$ownerId = $pdo->prepare("SELECT user_id FROM topics WHERE id = :tid")->fetchColumn();
if($ownerId && $ownerId != $_SESSION['user']['id']){
    $pdo->prepare("INSERT INTO notifications (user_id,type,meta) VALUES (:uid,'new_reply',:m)")
        ->execute(['uid'=>$ownerId,'m'=>json_encode(['topic_id'=>$topic_id,'from'=>$_SESSION['user']['id']])]);
}

header("Location: /munforum/topic.php?id={$topic_id}");
exit;

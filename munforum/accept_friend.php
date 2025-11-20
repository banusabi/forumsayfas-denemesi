<?php
// accept_friend.php
require 'db.php';
session_start();
if(!isset($_SESSION['user'])) { header('Location: /munforum/login.php'); exit; }
$me = $_SESSION['user']['id'];
$id = intval($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

if(!$id) { header('Location: /munforum/index.php'); exit; }

// fetch request
$f = $pdo->prepare("SELECT * FROM friends WHERE id = :id AND recipient_id = :me LIMIT 1");
$f->execute(['id'=>$id,'me'=>$me]);
$fr = $f->fetch();
if(!$fr){ header('Location: /munforum/index.php'); exit; }

if($action === 'accept'){
    $pdo->prepare("UPDATE friends SET status='accepted',updated_at=NOW() WHERE id = :id")->execute(['id'=>$id]);
    // notify requester
    $pdo->prepare("INSERT INTO notifications (user_id,type,meta) VALUES (:uid,'friend_accepted',:meta)")
        ->execute(['uid'=>$fr['requester_id'],'meta'=>json_encode(['by'=>$me])]);
} elseif($action === 'reject'){
    $pdo->prepare("UPDATE friends SET status='rejected',updated_at=NOW() WHERE id = :id")->execute(['id'=>$id]);
}

header('Location: /munforum/profile.php?id=' . $fr['requester_id']);
exit;

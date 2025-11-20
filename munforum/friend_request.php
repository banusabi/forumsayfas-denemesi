<?php
// friend_request.php
require 'db.php';
session_start();
if(!isset($_SESSION['user'])) { header('Location: /munforum/login.php'); exit; }
$from = $_SESSION['user']['id'];
$to = intval($_POST['to'] ?? 0);
if(!$to || $to == $from) { header('Location: /munforum/index.php'); exit; }

// check existing
$check = $pdo->prepare("SELECT * FROM friends WHERE (requester_id=:from AND recipient_id=:to) OR (requester_id=:to AND recipient_id=:from) LIMIT 1");
$check->execute(['from'=>$from,'to'=>$to]);
$ex = $check->fetch();
if($ex){
    // if previously rejected, allow re-request
    if($ex['status'] === 'rejected'){
        $pdo->prepare("UPDATE friends SET requester_id=:from,recipient_id=:to,status='pending',updated_at=NOW() WHERE id=:id")
            ->execute(['from'=>$from,'to'=>$to,'id'=>$ex['id']]);
    }
    header('Location: /munforum/profile.php?id=' . $to);
    exit;
}

// insert
$ins = $pdo->prepare("INSERT INTO friends (requester_id,recipient_id,status) VALUES (:from,:to,'pending')");
$ins->execute(['from'=>$from,'to'=>$to]);

// notify user (in-app)
$pdo->prepare("INSERT INTO notifications (user_id,type,meta) VALUES (:uid,'friend_request',:meta)")
    ->execute(['uid'=>$to,'meta'=>json_encode(['from'=>$from])]);

header('Location: /munforum/profile.php?id=' . $to);
exit;

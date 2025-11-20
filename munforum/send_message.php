<?php
// send_message.php
require 'db.php';
session_start();
if(!isset($_SESSION['user'])) { header('Location: /munforum/login.php'); exit; }
$from = $_SESSION['user']['id'];
$to = intval($_POST['to'] ?? 0);
$body = trim($_POST['body'] ?? '');
if(!$to || !$body){ header('Location: /munforum/messages.php'); exit; }

$pdo->prepare("INSERT INTO messages (sender_id,recipient_id,body) VALUES (:s,:r,:b)")->execute(['s'=>$from,'r'=>$to,'b'=>$body]);

// notify recipient
$pdo->prepare("INSERT INTO notifications (user_id,type,meta) VALUES (:uid,'new_message',:meta)")
    ->execute(['uid'=>$to,'meta'=>json_encode(['from'=>$from])]);

header('Location: /munforum/messages.php?with=' . $to);
exit;

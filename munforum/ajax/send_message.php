<?php
// ajax/send_message.php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok'=>false,'error'=>'Bad request']);
    exit;
}
if (!is_logged_in()) {
    echo json_encode(['ok'=>false,'error'=>'Login required']);
    exit;
}
$sender = current_user_id();
$recipient = intval($_POST['recipient_id'] ?? 0);
$body = trim($_POST['body'] ?? '');
if ($recipient <= 0 || $body === '') {
    echo json_encode(['ok'=>false,'error'=>'Eksik veri']);
    exit;
}

$stmt = $mysqli->prepare("INSERT INTO messages (sender_id,recipient_id,body,created_at) VALUES (?,?,?,NOW())");
$stmt->bind_param('iis', $sender, $recipient, $body);
$ok = $stmt->execute();
if (!$ok) {
    echo json_encode(['ok'=>false,'error'=>'db error']);
    exit;
}
$id = $stmt->insert_id;
$stmt->close();
echo json_encode(['ok'=>true,'id'=>$id,'created_at'=>date('Y-m-d H:i:s')]);

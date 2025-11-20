<?php
// ajax/fetch_messages.php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');
if (!is_logged_in()) {
    echo json_encode(['ok'=>false,'error'=>'login']);
    exit;
}
$other = intval($_GET['u'] ?? 0);
$me = current_user_id();
if ($other <= 0) {
    echo json_encode(['ok'=>false,'error'=>'no other']);
    exit;
}
$stmt = $mysqli->prepare("SELECT m.*, su.username as sender_name FROM messages m LEFT JOIN users su ON su.id = m.sender_id WHERE (m.sender_id = ? AND m.recipient_id = ?) OR (m.sender_id = ? AND m.recipient_id = ?) ORDER BY m.created_at ASC");
$stmt->bind_param('iiii', $me, $other, $other, $me);
$stmt->execute();
$res = $stmt->get_result();
$messages = [];
while($r = $res->fetch_assoc()) {
    $messages[] = $r;
}
echo json_encode(['ok'=>true,'messages'=>$messages]);

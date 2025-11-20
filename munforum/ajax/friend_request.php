<?php
// ajax/friend_request.php
require_once __DIR__ . '/../includes/functions.php';
if (!is_logged_in()) {
    header('Location: /login.php');
    exit;
}
$me = current_user_id();
$to = intval($_GET['to'] ?? 0);
if ($to <= 0 || $to == $me) {
    die('Invalid');
}
// ensure no existing pair
$stmt = $mysqli->prepare("SELECT id FROM friends WHERE (requester_id = ? AND recipient_id = ?) OR (requester_id = ? AND recipient_id = ?) LIMIT 1");
$stmt->bind_param('iiii', $me, $to, $to, $me);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    die('Zaten istek/gönderilmiş/arkadaşsınız.');
}
$stmt->close();

$stmt = $mysqli->prepare("INSERT INTO friends (requester_id, recipient_id, status, created_at) VALUES (?, ?, 'pending', NOW())");
$stmt->bind_param('ii', $me, $to);
$stmt->execute();
$stmt->close();
header('Location: /friends.php');

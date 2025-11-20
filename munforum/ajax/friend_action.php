<?php
// ajax/friend_action.php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$me = current_user_id();
$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) die('Invalid');

if ($action === 'accept') {
    $stmt = $mysqli->prepare("UPDATE friends SET status='accepted', updated_at=NOW(), recipient_id = recipient_id WHERE id = ? AND recipient_id = ?");
    $stmt->bind_param('ii', $id, $me);
    $stmt->execute();
    $stmt->close();
    header('Location: /friends.php');
    exit;
} elseif ($action === 'reject') {
    $stmt = $mysqli->prepare("UPDATE friends SET status='rejected', updated_at=NOW() WHERE id = ? AND recipient_id = ?");
    $stmt->bind_param('ii', $id, $me);
    $stmt->execute();
    $stmt->close();
    header('Location: /friends.php');
    exit;
}

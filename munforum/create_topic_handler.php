<?php
// create_topic_handler.php
require_once __DIR__ . '/includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /create_topic.php');
    exit;
}

$user_id = current_user_id();
$title = trim($_POST['title'] ?? '');
$body = trim($_POST['body'] ?? '');
$category_id = intval($_POST['category_id'] ?? 0);
$city_id = intval($_POST['city_id'] ?? 0);

if ($title === '' || $body === '' || $category_id <= 0 || $city_id <= 0) {
    header('Location: /create_topic.php?error=' . urlencode('Lütfen tüm zorunlu alanları doldurun.'));
    exit;
}

// Eğer seçilen kategori "anahtar" ve kullanıcı admin/moderator değilse reddet
$stmt = $mysqli->prepare("SELECT is_key_topic FROM categories WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $category_id);
$stmt->execute();
$stmt->bind_result($is_key);
if ($stmt->fetch()) {
    if ($is_key && !in_array(current_user_role(), ['admin','moderator'])) {
        $stmt->close();
        header('Location: /create_topic.php?error=' . urlencode('Seçilen kategori yalnızca admin/moderator tarafından kullanılabilir.'));
        exit;
    }
}
$stmt->close();

$slug = strtolower(preg_replace('/[^a-z0-9]+/i','-', $title));
$slug = trim($slug, '-');

$stmt = $mysqli->prepare("INSERT INTO topics (user_id,title,slug,body,created_at,city_id) VALUES (?,?,?,?,NOW(),?)");
$stmt->bind_param('isssi', $user_id, $title, $slug, $body, $city_id);
if (!$stmt->execute()) {
    error_log('Topic insert error: ' . $stmt->error);
    header('Location: /create_topic.php?error=' . urlencode('Konu oluşturulamadı.'));
    exit;
}
$topic_id = $stmt->insert_id;
$stmt->close();

// topic_tags or categories link — if you want pivot, create topic_category table; currently topics has no category_id, we can add:
// quick approach: store category_id in topics table (if not present, consider altering DB). For now, create a simple mapping table if not exists.
$mysqli->query("CREATE TABLE IF NOT EXISTS topic_category (topic_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(topic_id), FOREIGN KEY(topic_id) REFERENCES topics(id) ON DELETE CASCADE, FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
$stmt = $mysqli->prepare("INSERT INTO topic_category (topic_id,category_id) VALUES (?,?)");
$stmt->bind_param('ii', $topic_id, $category_id);
$stmt->execute();
$stmt->close();

header('Location: /topic.php?id=' . intval($topic_id));
exit;

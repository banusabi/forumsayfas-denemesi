<?php
// register_handler.php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /register.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$city_id = $_POST['city_id'] ?? '';

if ($username === '' || $email === '' || $password === '' || $city_id === '') {
    header('Location: /register.php?error=' . urlencode('Tüm alanları doldurun ve şehir seçin.'));
    exit;
}

// Basit validasyon
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: /register.php?error=' . urlencode('Geçersiz e-posta.'));
    exit;
}

// Duplicate check
$stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
$stmt->bind_param('ss', $username, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    header('Location: /register.php?error=' . urlencode('Kullanıcı adı veya e-posta zaten kayıtlı.'));
    exit;
}
$stmt->close();

// Hash the password
$pw_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare("INSERT INTO users (username,email,password,city_id,created_at) VALUES (?,?,?,?,NOW())");
$stmt->bind_param('ssis', $username, $email, $pw_hash, $city_id);
$ok = $stmt->execute();
if (!$ok) {
    error_log('Register insert error: ' . $stmt->error);
    header('Location: /register.php?error=' . urlencode('Kayıt yapılamadı. Lütfen tekrar deneyin.'));
    exit;
}
$new_user_id = $stmt->insert_id;
$stmt->close();

// auto-login after register (isteğe bağlı) veya yönlendir
$_SESSION['user_id'] = $new_user_id;
$_SESSION['role'] = 'user';

// redirect to profile or homepage correctly (no blank page)
header('Location: /profile.php?id=' . intval($new_user_id));
exit;

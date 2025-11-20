<?php
require 'header.php';
$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=:u OR email=:u LIMIT 1");
    $stmt->execute(['u'=>$u]);
    $user = $stmt->fetch();
    if(!$user || !password_verify($p, $user['password'])) {
        $errors[] = "Kullanıcı adı / şifre hatalı.";
    } else {
        // login
        $_SESSION['user'] = ['id'=>$user['id'],'username'=>$user['username'],'email'=>$user['email']];
        $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id")->execute(['id'=>$user['id']]);
        header('Location: /munforum/index.php'); exit;
    }
}
?>
<div class="card">
  <h2>Giriş</h2>
  <?php if($errors): foreach($errors as $e): ?><div class="small" style="color:red"><?=$e?></div><?php endforeach; endif; ?>
  <form method="post">
    <div class="form-row"><input name="username" placeholder="Kullanıcı adı veya e-posta"></div>
    <div class="form-row"><input type="password" name="password" placeholder="Parola"></div>
    <div class="form-row"><button class="btn">Giriş</button></div>
  </form>
</div>
<?php require 'footer.php'; ?>

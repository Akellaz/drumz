<?php
session_start(); // ← ЭТО ОБЯЗАТЕЛЬНО
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] === 'jocker') { // ← замени на свой!
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Неверный пароль';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Вход в админку</title>
  <style>
    body { font-family: sans-serif; background: #0f0c1d; color: #f0f0f5; text-align: center; padding: 50px; }
    .form { display: inline-block; background: #1a172a; padding: 30px; border-radius: 10px; }
    input[type="password"] { width: 250px; padding: 10px; margin: 10px 0; border-radius: 5px; border: none; }
    button { padding: 10px 20px; background: #4ecdc4; color: #0f0c1d; border: none; border-radius: 5px; cursor: pointer; }
    .error { color: #ff6b6b; margin: 10px 0; }
  </style>
</head>
<body>
  <div class="form">
    <h2>🔐 Админка Drumz</h2>
    <?php if (!empty($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="login.php">
      <input type="password" name="password" placeholder="Пароль" required>
      <br>
      <button type="submit">Войти</button>
    </form>
  </div>
</body>
</html>
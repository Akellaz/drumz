<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Обработка добавления ученика
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slug = trim($_POST['slug']);
    $full_name = trim($_POST['full_name']);

    if ($slug && $full_name) {
        try {
            $stmt = $pdo->prepare("INSERT INTO students (slug, full_name) VALUES (?, ?)");
            $stmt->execute([$slug, $full_name]);
            $success = "Ученик «$full_name» добавлен!";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Ошибка: ученик с таким ID («$slug») уже существует.";
            } else {
                $error = "Ошибка БД: " . $e->getMessage();
            }
        }
    } else {
        $error = "Заполните все поля";
    }
}

// Список учеников
$students = $pdo->query("SELECT * FROM students ORDER BY full_name")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Админка — Ученики</title>
  <style>
    body { font-family: sans-serif; max-width: 800px; margin: 20px auto; }
    .form-group { margin: 10px 0; }
    input { padding: 8px; width: 300px; }
    button { padding: 8px 16px; background: #5a4a8c; color: white; border: none; cursor: pointer; }
    .success { color: green; }
    .error { color: red; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    td, th { border: 1px solid #ccc; padding: 8px; text-align: left; }
  </style>
</head>
<body>
  <h2>👥 Ученики</h2>

  <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>
  <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

  <h3>➕ Добавить ученика</h3>
  <form method="POST">
    <div class="form-group">
      <label>ID (англ., без пробелов):</label><br>
      <input type="text" name="slug" placeholder="Например: akella, afina" required>
      <div><small>Будет в ссылке: https://drumz.ru/student/akella</small></div>
    </div>
    <div class="form-group">
      <label>Полное имя:</label><br>
      <input type="text" name="full_name" placeholder="Например: Сергей Щепотин" required>
    </div>
    <button type="submit">Добавить ученика</button>
  </form>

  <h3>Список учеников</h3>
  <?php if ($students): ?>
    <table>
      <tr>
        <th>ID</th>
        <th>Имя</th>
        <th>Ссылка</th>
      </tr>
      <?php foreach ($students as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s['slug']) ?></td>
          <td><?= htmlspecialchars($s['full_name']) ?></td>
          <td><a href="/student/<?= urlencode($s['slug']) ?>" target="_blank">Открыть</a></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php else: ?>
    <p>Нет учеников.</p>
  <?php endif; ?>

  <p><a href="dashboard.php">← Назад в статистику</a></p>
</body>
</html>
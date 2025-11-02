<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Список учеников
$students = $pdo->query("SELECT id, full_name FROM students ORDER BY full_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $date = $_POST['date'];
    $duration = $_POST['duration'];
    $price = $_POST['price'] ?: LESSON_PRICE;
    $paid = isset($_POST['paid']);
    $notes = $_POST['notes'];

    $stmt = $pdo->prepare("INSERT INTO lessons (student_id, date, duration, price, paid, notes) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$student_id, $date, $duration, $price, $paid, $notes]);
    $success = 'Урок добавлен';
}

$lessons = $pdo->query("
    SELECT l.*, s.full_name 
    FROM lessons l 
    JOIN students s ON l.student_id = s.id 
    ORDER BY l.date DESC LIMIT 20
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Админка — Уроки</title></head>
<body>
<h2>➕ Добавить урок</h2>
<?php if (!empty($success)) echo "<p style='color:green'>$success</p>"; ?>
<form method="POST">
  <select name="student_id" required>
    <option value="">— Выберите ученика —</option>
    <?php foreach ($students as $s): ?>
      <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['full_name']) ?></option>
    <?php endforeach; ?>
  </select><br><br>
  
  Дата: <input type="date" name="date" value="<?= date('Y-m-d') ?>" required><br><br>
  Длительность (мин): <input type="number" name="duration" value="60" required><br><br>
  Цена (₽): <input type="number" name="price" value="<?= LESSON_PRICE ?>" required><br><br>
  Оплачено: <input type="checkbox" name="paid" checked><br><br>
  Заметки: <textarea name="notes"></textarea><br><br>
  <button type="submit">Добавить</button>
</form>

<h3>Последние уроки</h3>
<ul>
<?php foreach ($lessons as $l): ?>
  <li><?= $l['date'] ?> — <?= $l['full_name'] ?> — <?= $l['price'] ?> ₽ 
      <?= $l['paid'] ? '✅' : '❌' ?></li>
<?php endforeach; ?>
</ul>
</body>
</html>
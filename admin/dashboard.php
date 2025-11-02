<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/auth.php';
require_once 'includes/db.php';

// Статистика
$stmt = $pdo->query("SELECT COUNT(*) FROM lessons");
$total_lessons = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM lessons WHERE paid = 1");
$paid_lessons = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT SUM(price) FROM lessons WHERE paid = 1");
$total_income = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT s.full_name, COUNT(l.id) as lessons, SUM(l.price) as income 
                     FROM students s 
                     LEFT JOIN lessons l ON s.id = l.student_id AND l.paid = 1
                     GROUP BY s.id ORDER BY income DESC");
$students_stats = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Админка — Статистика</title></head>
<body>
<h2>📊 Статистика</h2>
<p>Всего уроков: <strong><?= $total_lessons ?></strong></p>
<p>Оплаченных: <strong><?= $paid_lessons ?></strong></p>
<p>Доход: <strong><?= number_format($total_income, 0, '', ' ') ?> ₽</strong></p>

<h3>По ученикам:</h3>
<ul>
<?php foreach ($students_stats as $s): ?>
  <li><?= htmlspecialchars($s['full_name']) ?>: 
      <?= $s['lessons'] ?> уроков, 
      <?= number_format($s['income'] ?: 0, 0, '', ' ') ?> ₽</li>
<?php endforeach; ?>
</ul>

<a href="students.php">Ученики</a> | 
<a href="lessons.php">Уроки</a> | 
<a href="progress.php">Прогресс</a> | 
<a href="logout.php">Выйти</a>
</body>
</html>
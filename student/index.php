<?php
// student/index.php
error_reporting(E_ALL);
ini_set('display_errors', 0); // скрываем от посетителей, но логируем
ini_set('log_errors', 1);

require_once __DIR__ . '/../includes/db.php';

$slug = $_GET['slug'] ?? null;
if (!$slug) {
    http_response_code(404);
    echo 'Ученик не указан';
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, full_name FROM students WHERE slug = ?");
    $stmt->execute([$slug]);
    $student = $stmt->fetch();

    if (!$student) {
        http_response_code(404);
        echo 'Ученик не найден';
        exit;
    }

    // Загружаем прогресс по уровням
    $stmt = $pdo->prepare("
        SELECT 
            l.id as level_id,
            l.name as level_name,
            l.description as level_desc,
            l.sort_order as level_order,
            t.id as task_id,
            t.title as task_title,
            COALESCE(p.completed, 0) as completed
        FROM levels l
        JOIN tasks t ON l.id = t.level_id
        LEFT JOIN progress p ON t.id = p.task_id AND p.student_id = ?
        ORDER BY l.sort_order, t.sort_order
    ");
    $stmt->execute([$student['id']]);
    $rows = $stmt->fetchAll();

    $levels = [];
    foreach ($rows as $row) {
        $levels[$row['level_id']][] = $row;
    }

} catch (Exception $e) {
    error_log('Student page error: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при загрузке данных. Попробуйте позже.';
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Прогресс: <?= htmlspecialchars($student['full_name']) ?> | Drumz</title>
  <meta name="description" content="Прогресс ученика барабанной студии Drumz в Троицке">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: sans-serif; }
    body { background: #0f0c1d; color: #f0f0f5; padding: 20px; }
    .container { max-width: 800px; margin: 0 auto; }
    h1 { text-align: center; margin: 30px 0 20px; color: #ff6b6b; }
    .level {
      background: #1a172a;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      border-left: 4px solid #444;
    }
    .level.completed { border-left-color: #4ecdc4; }
    .progress-bar {
      height: 8px;
      background: #2c2940;
      border-radius: 4px;
      margin: 12px 0;
      overflow: hidden;
    }
    .progress-fill {
      height: 100%;
      background: #ff6b6b;
      width: 0%;
    }
    .task { margin: 6px 0; color: #ccc; }
    .task.done { color: #4ecdc4; text-decoration: line-through; }
    .btn {
      display: inline-block;
      margin-top: 16px;
      padding: 8px 16px;
      background: #5a4a8c;
      color: white;
      text-decoration: none;
      border-radius: 6px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>🥁 Прогресс: <?= htmlspecialchars($student['full_name']) ?></h1>

    <?php foreach ($levels as $level_id => $tasks):
        $completed_count = array_sum(array_column($tasks, 'completed'));
        $total = count($tasks);
        $progress_percent = $total ? round(($completed_count / $total) * 100) : 0;
        $is_completed = ($progress_percent == 100);
        $level_name = $tasks[0]['level_name'];
        $level_desc = $tasks[0]['level_desc'];
    ?>
      <div class="level <?= $is_completed ? 'completed' : '' ?>">
        <h2><?= htmlspecialchars($level_name) ?> — <em><?= htmlspecialchars($level_desc) ?></em></h2>
        <div class="progress-bar">
          <div class="progress-fill" style="width: <?= $progress_percent ?>%"></div>
        </div>
        <div>
          <?php foreach ($tasks as $task): ?>
            <div class="task <?= $task['completed'] ? 'done' : '' ?>">
              <?= htmlspecialchars($task['task_title']) ?>
            </div>
          <?php endforeach; ?>
        </div>
        <?php if ($is_completed): ?>
          <a href="/certificate.html?name=<?= urlencode($student['full_name']) ?>&level=<?= urlencode($level_name) ?>"
             class="btn" target="_blank">🎓 Скачать сертификат</a>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</body>
</html>
<?php
require_once 'includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$students = $pdo->query("SELECT id, slug, full_name FROM students ORDER BY full_name")->fetchAll();
$selected_student_id = $_GET['student'] ?? null;
$selected_student = null;
$levels_with_tasks = [];

if ($selected_student_id) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$selected_student_id]);
    $selected_student = $stmt->fetch();

    if ($selected_student) {
        $stmt = $pdo->prepare("
            SELECT 
                l.id as level_id,
                l.name as level_name,
                l.description as level_desc,
                t.id as task_id,
                t.title as task_title,
                COALESCE(p.completed, 0) as completed
            FROM levels l
            JOIN tasks t ON l.id = t.level_id
            LEFT JOIN progress p ON t.id = p.task_id AND p.student_id = ?
            ORDER BY l.sort_order, t.sort_order
        ");
        $stmt->execute([$selected_student_id]);
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) {
            $levels_with_tasks[$row['level_id']][] = $row;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $task_ids = $_POST['task_ids'] ?? [];
    $completed_flags = $_POST['completed'] ?? [];

    $pdo->prepare("DELETE FROM progress WHERE student_id = ?")->execute([$student_id]);
    $stmt = $pdo->prepare("INSERT INTO progress (student_id, task_id, completed) VALUES (?, ?, ?)");
    foreach ($task_ids as $task_id) {
        $completed = in_array($task_id, $completed_flags) ? 1 : 0;
        $stmt->execute([$student_id, $task_id, $completed]);
    }
    header("Location: ?page=progress&student=" . $student_id);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Админка — Прогресс ученика</title>
  <style>
    body { font-family: sans-serif; max-width: 900px; margin: 20px auto; }
    .form-group { margin: 15px 0; }
    select, button { padding: 8px 12px; font-size: 16px; }
    .level { background: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 8px; }
    .task { margin: 6px 0; }
    .task input { margin-right: 8px; }
    .btn-save { background: #4ecdc4; color: #0f0c1d; border: none; padding: 10px 20px; font-weight: bold; cursor: pointer; }
    a { color: #5a4a8c; text-decoration: none; }
  </style>
</head>
<body>
  <h2>✅ Редактор прогресса</h2>

  <form method="GET">
    <input type="hidden" name="page" value="progress">
    <div class="form-group">
      <label>Выберите ученика:</label><br>
      <select name="student" onchange="this.form.submit()">
        <option value="">— Не выбран —</option>
        <?php foreach ($students as $s): ?>
          <option value="<?= $s['id'] ?>" <?= ($selected_student_id == $s['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($s['full_name']) ?> (<?= htmlspecialchars($s['slug']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </form>

  <?php if ($selected_student): ?>
    <h3>Прогресс: <?= htmlspecialchars($selected_student['full_name']) ?></h3>

    <form method="POST">
      <input type="hidden" name="student_id" value="<?= $selected_student['id'] ?>">

      <?php foreach ($levels_with_tasks as $level_id => $tasks): 
          $level_name = $tasks[0]['level_name'];
          $level_desc = $tasks[0]['level_desc'];
      ?>
        <div class="level">
          <h4><?= htmlspecialchars($level_name) ?> — <em><?= htmlspecialchars($level_desc) ?></em></h4>
          <?php foreach ($tasks as $task): ?>
            <div class="task">
              <label>
                <input type="checkbox" 
                       name="completed[]" 
                       value="<?= $task['task_id'] ?>"
                       <?= $task['completed'] ? 'checked' : '' ?>>
                <?= htmlspecialchars($task['task_title']) ?>
                <input type="hidden" name="task_ids[]" value="<?= $task['task_id'] ?>">
              </label>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>

      <button type="submit" class="btn-save">💾 Сохранить прогресс</button>
    </form>

    <p style="margin-top: 20px;">
      <a href="https://drumz.ru/student/<?= urlencode($selected_student['slug']) ?>" target="_blank">
        👁️ Посмотреть публичную страницу ученика
      </a>
    </p>
  <?php endif; ?>

  <p><a href="dashboard.php">← Назад в статистику</a></p>
</body>
</html>
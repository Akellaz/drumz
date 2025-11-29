<?php
// admin/stages.php
session_start();
require_once 'includes/auth.php'; // Проверка авторизации
require_once 'includes/db.php';

// Обработка POST-запросов (добавление/удаление)
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_stage'])) {
        $rank_id = (int)$_POST['rank_id'];
        $step_number = (int)$_POST['step_number'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);

        if ($rank_id && $step_number && $name) {
            $stmt = $pdo->prepare("INSERT INTO stages (rank_id, step_number, name, description, sort_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$rank_id, $step_number, $name, $description, $step_number]); // sort_order = step_number как база
            $message = "Ступень добавлена.";
        } else {
            $message = "Ошибка: заполните все поля.";
        }
    }

    if (isset($_POST['add_task'])) {
        $stage_id = (int)$_POST['stage_id'];
        $title = trim($_POST['title']);

        if ($stage_id && $title) {
            $stmt = $pdo->prepare("INSERT INTO tasks_new (stage_id, title, sort_order) VALUES (?, ?, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM tasks_new t2 WHERE t2.stage_id = ?))");
            $stmt->execute([$stage_id, $title, $stage_id]);
            $message = "Задача добавлена.";
        } else {
            $message = "Ошибка: заполните название задачи.";
        }
    }

    if (isset($_GET['delete_stage']) && isset($_GET['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
        $stage_id_to_delete = (int)$_GET['delete_stage'];
        $stmt = $pdo->prepare("DELETE FROM stages WHERE id = ?");
        $stmt->execute([$stage_id_to_delete]);
        $message = "Ступень и все её задачи удалены.";
    }

    if (isset($_GET['delete_task']) && isset($_GET['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
        $task_id_to_delete = (int)$_GET['delete_task'];
        $stmt = $pdo->prepare("DELETE FROM tasks_new WHERE id = ?");
        $stmt->execute([$task_id_to_delete]);
        $message = "Задача удалена.";
    }
}

// Генерация CSRF токена для безопасности
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Загружаем титулы и ступени
$ranks_stmt = $pdo->query("SELECT * FROM ranks ORDER BY sort_order");
$ranks = $ranks_stmt->fetchAll(PDO::FETCH_ASSOC);

$stages_by_rank = [];
$tasks_by_stage = [];

foreach ($ranks as $rank) {
    $stmt = $pdo->prepare("SELECT * FROM stages WHERE rank_id = ? ORDER BY sort_order");
    $stmt->execute([$rank['id']]);
    $stages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stages_by_rank[$rank['id']] = $stages;

    foreach ($stages as $stage) {
        $stmt = $pdo->prepare("SELECT * FROM tasks_new WHERE stage_id = ? ORDER BY sort_order");
        $stmt->execute([$stage['id']]);
        $tasks_by_stage[$stage['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$selected_rank_id = (int)($_GET['rank'] ?? 0);
$selected_stage_id = (int)($_GET['stage'] ?? 0);

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Админка — Титулы и Ступени</title>
  <style>
    body { font-family: sans-serif; max-width: 1000px; margin: 20px auto; }
    .form-group { margin: 10px 0; }
    input, select { padding: 8px; width: 300px; }
    button, .btn { padding: 8px 16px; background: #5a4a8c; color: white; border: none; cursor: pointer; text-decoration: none; display: inline-block; }
    .btn-danger { background: #e53e3e; }
    .success { color: green; }
    .error { color: red; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    td, th { border: 1px solid #ccc; padding: 8px; text-align: left; }
    .stage-card { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 8px; }
    .task-item { margin: 6px 0; display: flex; justify-content: space-between; align-items: center; }
    .task-item span { flex-grow: 1; }
    .task-item a { margin-left: 10px; }
    a { color: #5a4a8c; text-decoration: none; }
    a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <h2>🏆 Управление Титулами и Ступенями</h2>

  <?php if ($message): ?>
      <p class="<?= strpos($message, 'Ошибка') === 0 ? 'error' : 'success' ?>"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <p><a href="dashboard.php">← Назад в статистику</a></p>

  <div style="display: flex; gap: 30px; margin-top: 20px;">
    <!-- Левая колонка: Список титулов -->
    <div style="flex: 1;">
      <h3>📋 Титулы</h3>
      <ul style="list-style: none; padding: 0;">
      <?php foreach ($ranks as $rank): ?>
        <li style="margin-bottom: 8px;">
          <a href="?rank=<?= $rank['id'] ?>" style="font-weight: <?= $selected_rank_id === $rank['id'] ? 'bold' : 'normal'; ?>;">
            <?= htmlspecialchars($rank['name']) ?>
          </a>
        </li>
      <?php endforeach; ?>
      </ul>

      <?php if ($selected_rank_id): ?>
        <h4>➕ Добавить ступень в "<?= htmlspecialchars($ranks[array_search($selected_rank_id, array_column($ranks, 'id'))]['name']) ?>"</h4>
        <form method="post" class="form-group">
          <input type="hidden" name="rank_id" value="<?= $selected_rank_id ?>">
          <label>Номер ступени (1-5):<br>
            <input type="number" name="step_number" min="1" max="5" required>
          </label><br><br>
          <label>Название:<br>
            <input type="text" name="name" placeholder="Например: Основы ритма" required>
          </label><br><br>
          <label>Описание (опционально):<br>
            <input type="text" name="description" placeholder="Краткое описание ступени">
          </label><br><br>
          <button type="submit" name="add_stage">Добавить ступень</button>
        </form>
      <?php endif; ?>
    </div>

    <!-- Правая колонка: Ступени и задачи -->
    <div style="flex: 2;">
      <?php if ($selected_rank_id && isset($stages_by_rank[$selected_rank_id])): ?>
        <h3>📖 Ступени: <?= htmlspecialchars($ranks[array_search($selected_rank_id, array_column($ranks, 'id'))]['name']) ?></h3>
        <ul style="list-style: none; padding: 0;">
        <?php foreach ($stages_by_rank[$selected_rank_id] as $stage): ?>
          <li class="stage-card">
            <a href="?rank=<?= $selected_rank_id ?>&stage=<?= $stage['id'] ?>"
               style="font-weight: <?= $selected_stage_id === $stage['id'] ? 'bold' : 'normal'; ?>; text-decoration: none;">
              <h4><?= $stage['step_number'] ?>. <?= htmlspecialchars($stage['name']) ?></h4>
            </a>
            <p style="color: #777; font-size: 0.9em; margin: 5px 0;"><?= htmlspecialchars($stage['description']) ?></p>
            <a href="?delete_stage=<?= $stage['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>" 
               class="btn btn-danger" 
               onclick="return confirm('Удалить ступень и все её задачи?')">❌ Удалить ступень</a>
          </li>
        <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <?php if ($selected_stage_id && isset($tasks_by_stage[$selected_stage_id])): ?>
        <h3>📝 Задачи для ступени: <?= htmlspecialchars($stages_by_rank[$selected_rank_id][array_search($selected_stage_id, array_column($stages_by_rank[$selected_rank_id], 'id'))]['name']) ?></h3>
        <ul style="list-style: none; padding: 0;">
        <?php foreach ($tasks_by_stage[$selected_stage_id] as $task): ?>
          <li class="task-item">
            <span><?= htmlspecialchars($task['title']) ?></span>
            <a href="?delete_task=<?= $task['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>" 
               class="btn btn-danger" 
               style="padding: 4px 8px; font-size: 0.9em;" 
               onclick="return confirm('Удалить задачу?')">❌</a>
          </li>
        <?php endforeach; ?>
        </ul>

        <h4>➕ Добавить задачу</h4>
        <form method="post" class="form-group">
          <input type="hidden" name="stage_id" value="<?= $selected_stage_id ?>">
          <label>Название задачи:<br>
            <input type="text" name="title" placeholder="Например: Постановка рук" required>
          </label><br><br>
          <button type="submit" name="add_task">Добавить задачу</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
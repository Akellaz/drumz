<?php
// student/index.php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/seo.php';

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

    // Группируем уровни по титулам
    $titles = [];
    foreach ($rows as $row) {
        $level_name = $row['level_name']; // Например: "Новичок 1"
        $level_desc = $row['level_desc']; // Например: "Новичок"
        
        // Определяем титул из описания
        $title_key = $level_desc;
        
        if (!isset($titles[$title_key])) {
            $titles[$title_key] = [
                'name' => $title_key,
                'levels' => []
            ];
        }
        
        if (!isset($titles[$title_key]['levels'][$row['level_id']])) {
            // Парсим номер ступени из названия
            $parts = explode(' ', $level_name, 2);
            $step_number = isset($parts[1]) ? $parts[1] : '1';
            
            $titles[$title_key]['levels'][$row['level_id']] = [
                'level_id' => $row['level_id'],
                'level_name' => $level_name,
                'step_number' => $step_number,
                'tasks' => []
            ];
        }
        
        $titles[$title_key]['levels'][$row['level_id']]['tasks'][] = $row;
    }

    // Загружаем всех учеников, сортируя по прогрессу: по номеру последнего завершённого уровня + кол-ву выполненных задач
$all_students_query = "
    SELECT 
        s.id,
        s.slug,
        s.full_name,
        COALESCE(MAX(l.sort_order), 0) as last_level_order,
        COALESCE(SUM(p.completed), 0) as completed_tasks
    FROM students s
    LEFT JOIN progress p ON s.id = p.student_id
    LEFT JOIN tasks t ON p.task_id = t.id
    LEFT JOIN levels l ON t.level_id = l.id AND (
        -- Только если все задачи уровня выполнены
        SELECT COUNT(*) 
        FROM tasks t2 
        WHERE t2.level_id = l.id 
          AND t2.id NOT IN (SELECT task_id FROM progress WHERE student_id = s.id AND completed = 0)
    ) = (
        SELECT COUNT(*) FROM tasks WHERE level_id = l.id
    )
    GROUP BY s.id, s.slug, s.full_name
    ORDER BY last_level_order DESC, completed_tasks DESC, s.full_name
";
$all_students_stmt = $pdo->query($all_students_query);
$all_students = $all_students_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log('Student page error: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при загрузке данных. Попробуйте позже.';
    exit;
}

// Цвета для титулов
$title_colors = [
    'Ударник' => '#6c757d',
    'Барабанщик' => '#28a745',
    'Мастер' => '#17a2b8',
    'Про' => '#ffc107',
    'Элита' => '#fd7e14',
    'Легенда' => '#dc3545'
];

// Функция для проверки завершения всех ступеней титула
function isTitleCompleted($title_levels) {
    foreach ($title_levels as $level) {
        $tasks = $level['tasks'];
        $completed_count = array_sum(array_column($tasks, 'completed'));
        $total = count($tasks);
        if ($completed_count < $total) {
            return false;
        }
    }
    return true;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Прогресс: <?= htmlspecialchars($student['full_name']) ?> | Drumz</title>
  <meta name="description" content="Прогресс ученика барабанной студии Drumz в Троицке">
  <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
  <?php require_once '../includes/header.php'; ?>

  <main class="container">
    <div style="display: grid; grid-template-columns: 250px 1fr; gap: 24px; align-items-start;">
      <!-- Сайдбар -->
      <aside class="card" style="padding: 20px;">
        <h3>Все ученики</h3>
        <ul style="list-style: none; padding: 0;">
          <?php foreach ($all_students as $s): ?>
            <li style="margin-bottom: 8px;">
              <a href="/student/<?= urlencode($s['slug']) ?>" 
                 style="color: <?= $s['slug'] === $slug ? 'var(--primary)' : 'var(--text)'; ?>; font-weight: <?= $s['slug'] === $slug ? 'bold' : 'normal'; ?>;">
                <?= htmlspecialchars($s['full_name']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
        <hr style="margin: 16px 0; border: none; border-top: 1px solid var(--border);">
        <a href="/hall-of-fame/">← Все ученики (Зал славы)</a>
      </aside>

      <!-- Основной контент -->
      <section>
        <section class="hero">
          <h1>Прогресс: <?= htmlspecialchars($student['full_name']) ?></h1>
        </section>

        <?php foreach ($titles as $title_key => $title_data): 
            $is_title_completed = isTitleCompleted($title_data['levels']);
        ?>
          <div class="card" style="padding: 0; margin-bottom: 24px; border-left: 4px solid <?= $title_colors[$title_key] ?? 'var(--border)' ?>;">
            <div style="padding: 20px; background: rgba(0,0,0,0.03); border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
              <h2 style="margin: 0; color: <?= $title_colors[$title_key] ?? 'var(--text)' ?>;">
                <?= htmlspecialchars($title_key) ?>
              </h2>
              <?php if ($is_title_completed): ?>
                <a href="/certificate.html?name=<?= urlencode($student['full_name']) ?>&level=<?= urlencode($title_key) ?>"
                   class="btn" style="margin: 0; padding: 6px 12px; font-size: 0.9rem;" target="_blank">🎓 Сертификат</a>
              <?php endif; ?>
            </div>
            
            <?php foreach ($title_data['levels'] as $level): 
                $tasks = $level['tasks'];
                $completed_count = array_sum(array_column($tasks, 'completed'));
                $total = count($tasks);
                $progress_percent = $total ? round(($completed_count / $total) * 100) : 0;
                $is_completed = ($progress_percent == 100);
            ?>
              <div style="padding: 20px; border-bottom: 1px solid var(--border);">
                <h3 style="margin-top: 0; margin-bottom: 12px;">Ступень <?= htmlspecialchars($level['step_number']) ?></h3>
                <div style="height: 8px; background: var(--border); border-radius: 4px; margin: 12px 0; overflow: hidden;">
                  <div style="height: 100%; background: var(--primary); width: <?= $progress_percent ?>%;"></div>
                </div>
                <div>
                  <?php foreach ($tasks as $task): ?>
                    <div style="margin: 6px 0; color: <?= $task['completed'] ? 'var(--success)' : 'var(--text)' ?>; <?= $task['completed'] ? 'text-decoration: line-through;' : '' ?>">
                      <?= htmlspecialchars($task['task_title']) ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </section>
    </div>
  </main>

  <?php require_once '../includes/footer.php'; ?>
</body>
</html>

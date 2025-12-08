<?php
// Функция: определить количество полностью завершённых ТИТУЛОВ
function getCompletedTitlesCount($pdo, $student_id) {
    // Определяем структуру титулов: каждый титул (кроме "Новичка") состоит из 4 уровней
    // "Ударник" (2й титул): уровни 1-4
    // "Барабанщик" (3й титул): уровни 5-8
    // "Мастер" (4й титул): уровни 9-12
    // и т.д.
    // Индекс в массиве соответствует номеру титула (0 = Новичок, 1 = Ударник, ...)
    $titles = [
        ['name' => 'Новичок', 'color' => '#6c757d', 'emoji' => '👶'],
        ['name' => 'Ударник', 'color' => '#28a745', 'emoji' => '🥁'],
        ['name' => 'Барабанщик', 'color' => '#17a2b8', 'emoji' => '🥁'],
        ['name' => 'Мастер', 'color' => '#ffc107', 'emoji' => '🥇'],
        ['name' => 'Про', 'color' => '#fd7e14', 'emoji' => '⚡'],
        ['name' => 'Элита', 'color' => '#dc3545', 'emoji' => '👑'],
        ['name' => 'Легенда', 'color' => '#000000', 'emoji' => '🏆']
    ];

    $completed_titles = 0;
    $num_titles = count($titles);

    // Начинаем с 1, пропуская "Новичок"
    for ($i = 1; $i < $num_titles; $i++) {
        // Уровни для текущего титула (индекс $i)
        // Ударник (индекс 1) -> уровни 1-4 (sort_order 1-4)
        // Барабанщик (индекс 2) -> уровни 5-8 (sort_order 5-8)
        // ...
        $start_level_sort_order = ($i - 1) * 4 + 1; // Уровни 1, 5, 9, 13, ...
        $end_level_sort_order = $i * 4;             // Уровни 4, 8, 12, 16, ...

        $levels_query = "
            SELECT l.id
            FROM levels l
            WHERE l.sort_order BETWEEN ? AND ?
        ";
        $stmt = $pdo->prepare($levels_query);
        $stmt->execute([$start_level_sort_order, $end_level_sort_order]);
        $levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $all_levels_completed = true;

        foreach ($levels as $level) {
            $tasks_query = "
                SELECT COUNT(*) as total_tasks
                FROM tasks t
                WHERE t.level_id = ?
            ";
            $stmt = $pdo->prepare($tasks_query);
            $stmt->execute([$level['id']]);
            $total_tasks = $stmt->fetch(PDO::FETCH_ASSOC)['total_tasks'];

            $completed_tasks_query = "
                SELECT COUNT(*) as completed_tasks
                FROM progress p
                JOIN tasks t ON p.task_id = t.id
                WHERE p.student_id = ? AND t.level_id = ? AND p.completed = 1
            ";
            $stmt = $pdo->prepare($completed_tasks_query);
            $stmt->execute([$student_id, $level['id']]);
            $completed_tasks = $stmt->fetch(PDO::FETCH_ASSOC)['completed_tasks'];

            if ($completed_tasks < $total_tasks) {
                $all_levels_completed = false;
                break; // Прерываем цикл по уровням текущего титула, если не все задачи выполнены
            }
        }

        if ($all_levels_completed) {
            $completed_titles++;
        } else {
            break; // Прерываем цикл по титулам, если текущий титул не завершён
        }
    }

    return $completed_titles;
}

// Функция: определить текущий титул и прогресс в следующем
function getCurrentTitle($completed_titles, $pdo, $student_id) {
    $titles = [
        ['name' => 'Новичок', 'color' => '#6c757d', 'emoji' => '👶'],
        ['name' => 'Ударник', 'color' => '#28a745', 'emoji' => '🥁'],
        ['name' => 'Барабанщик', 'color' => '#17a2b8', 'emoji' => '🥁'],
        ['name' => 'Мастер', 'color' => '#ffc107', 'emoji' => '🥇'],
        ['name' => 'Про', 'color' => '#fd7e14', 'emoji' => '⚡'],
        ['name' => 'Элита', 'color' => '#dc3545', 'emoji' => '👑'],
        ['name' => 'Легенда', 'color' => '#000000', 'emoji' => '🏆']
    ];

    $total_titles = count($titles);
    $current_title_index = min($completed_titles, $total_titles - 1);
    $current_title_data = $titles[$current_title_index];

    // Если достигнут последний титул, возвращаем его как завершённый
    if ($completed_titles >= $total_titles - 1) {
        return [
            'title' => $current_title_data['name'],
            'full_title' => $current_title_data['name'],
            'color' => $current_title_data['color'],
            'emoji' => $current_title_data['emoji'],
            'progress_percent' => 100,
            'next_title' => null,
            'total_tasks_next' => 0,
            'completed_tasks_next' => 0,
        ];
    }

    // Иначе, вычисляем прогресс в следующем титуле
    $next_title_index = $completed_titles + 1;
    $next_title_data = $titles[$next_title_index];

    $start_level_next = $completed_titles * 4 + 1; // Уровни 1, 5, 9, 13...
    $end_level_next = $next_title_index * 4;       // Уровни 4, 8, 12, 16...

    // Получаем все уровни в следующем титуле
    $levels_query = "
        SELECT l.id
        FROM levels l
        WHERE l.sort_order BETWEEN ? AND ?
    ";
    $stmt = $pdo->prepare($levels_query);
    $stmt->execute([$start_level_next, $end_level_next]);
    $levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_tasks_next = 0;
    $completed_tasks_next = 0;

    foreach ($levels as $level) {
        // Подсчет всех задач в этом уровне
        $tasks_query = "
            SELECT COUNT(*) as total_tasks
            FROM tasks t
            WHERE t.level_id = ?
        ";
        $stmt = $pdo->prepare($tasks_query);
        $stmt->execute([$level['id']]);
        $total_tasks_next += $stmt->fetch(PDO::FETCH_ASSOC)['total_tasks'];

        // Подсчет выполненных задач в этом уровне для студента
        $completed_tasks_query = "
            SELECT COUNT(*) as completed_tasks
            FROM progress p
            JOIN tasks t ON p.task_id = t.id
            WHERE p.student_id = ? AND t.level_id = ? AND p.completed = 1
        ";
        $stmt = $pdo->prepare($completed_tasks_query);
        $stmt->execute([$student_id, $level['id']]);
        $completed_tasks_next += $stmt->fetch(PDO::FETCH_ASSOC)['completed_tasks'];
    }

    $progress_percent = $total_tasks_next > 0 ? ($completed_tasks_next / $total_tasks_next) * 100 : 0;

    // Возвращаем информацию о *текущем* титуле и прогрессе в *следующем*
    return [
        'title' => $current_title_data['name'],
        'full_title' => $current_title_data['name'],
        'color' => $current_title_data['color'],
        'emoji' => $current_title_data['emoji'],
        'progress_percent' => $progress_percent,
        'next_title' => $next_title_data['name'],
        'total_tasks_next' => $total_tasks_next,
        'completed_tasks_next' => $completed_tasks_next,
    ];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<?php require_once __DIR__ . '/../includes/seo.php'; ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="/assets/style.css">
  <style>
    /* Дополнительные стили для карточек, если не определены в основном CSS */
    .progress-cards .card {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .progress-cards .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    .progress-bar-container {
      position: relative;
      height: 10px;
      background: #e9ecef;
      border-radius: 5px;
      overflow: hidden;
      margin: 8px 0;
    }
    .progress-bar-fill {
      height: 100%;
      width: 0; /* Инициализируем 0, будет обновлено скриптом или inline стилем */
      transition: width 0.5s ease;
    }
  </style>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container">
    <section class="hero">
      <h1>Зал славы</h1>
      
    </section>

    <div class="progress-cards">
      <?php
      require_once '../includes/db.php';

      // Получаем всех учеников
      $students_query = "
          SELECT s.id, s.slug, s.full_name
          FROM students s
          ORDER BY s.created_at DESC
      ";
      $students_stmt = $pdo->query($students_query);
      $students = $students_stmt->fetchAll(PDO::FETCH_ASSOC);
      ?>

      <?php if (empty($students)): ?>
        <p>Пока нет ни одного ученика.</p>
      <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-top: 30px;">
          <?php foreach ($students as $student):
            $completed_titles = getCompletedTitlesCount($pdo, $student['id']);
            $title_info = getCurrentTitle($completed_titles, $pdo, $student['id']);
          ?>
            <div class="card" style="
                text-align: center;
                padding: 24px;
                border-top: 4px solid <?= $title_info['color'] ?>;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
              "
              onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.15)';"
              onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)';"
            >
              <h3 style="margin-top: 0;"><?= htmlspecialchars($student['full_name']) ?></h3>

              <p style="color: <?= $title_info['color'] ?>; font-weight: 600; margin: 8px 0; font-size: 1.25rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <?= $title_info['emoji'] ?>
                <?= $title_info['full_title'] ?>
              </p>

              <div style="margin: 16px 0;">
                <?php if ($title_info['progress_percent'] < 100): ?>
                  <!-- Показываем прогресс, только если титул не полностью получен -->
                  <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="
                      background: linear-gradient(90deg, <?= $title_info['color'] ?>, <?= $title_info['color'] ?> 80%, <?= $title_info['color'] ?>cc);
                      width: <?= $title_info['progress_percent'] ?>%;
                    "></div>
                  </div>
                  <small style="color: #6c757d; display: block; margin-top: 4px;">
                    До титула "<?= $title_info['next_title'] ?>": <?= round($title_info['progress_percent']) ?>%
                  </small>
                <?php else: ?>
                  <!-- Индикатор получения титула -->
                  <div style="
                    color: <?= $title_info['color'] ?>;
                    font-size: 0.9em;
                    font-weight: bold;
                    background-color: rgba(<?= hexdec(substr($title_info['color'], 1, 2)) ?>, <?= hexdec(substr($title_info['color'], 3, 2)) ?>, <?= hexdec(substr($title_info['color'], 5, 2)) ?>, 0.1);
                    padding: 4px 8px;
                    border-radius: 4px;
                    display: inline-block;
                    margin-top: 8px;
                  ">
                    Титул получен!
                  </div>
                <?php endif; ?>
              </div>

              <a href="/student/<?= urlencode($student['slug']) ?>" class="btn"
                 style="
                   margin-top: 12px;
                   background: <?= $title_info['color'] ?>;
                   border-color: <?= $title_info['color'] ?>;
                   color: white;
                   padding: 10px 20px;
                   text-decoration: none;
                   display: inline-block;
                   border-radius: 4px;
                   font-weight: 500;
                   transition: background 0.2s;
                 "
                 onmouseover="this.style.backgroundColor='<?= $title_info['color'] ?>cc'; this.style.borderColor='<?= $title_info['color'] ?>cc';"
                 onmouseout="this.style.backgroundColor='<?= $title_info['color'] ?>'; this.style.borderColor='<?= $title_info['color'] ?>';">
                Посмотреть прогресс
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <!-- Форма записи -->
    <?php include '../form/widget_hall.php'; ?>
    
  </main>

  <?php require_once '../includes/footer.php'; ?>
</body>
</html>
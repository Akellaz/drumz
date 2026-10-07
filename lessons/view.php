<?php
  $base = dirname(__DIR__);
  
  // ═══════════════════════════════════════════════════════════════
  // 1. Загружаем урок из БД (только опубликованные)
  // ═══════════════════════════════════════════════════════════════
  $host = 'localhost';
  $db   = 'cl439291_lessons';
  $user = 'cl439291_lessons';
  $pass = 'dM0fQ0vN5l';

  $lesson = null;
  $lessonId = (int)($_GET['id'] ?? 0);

  if ($lessonId > 0) {
      try {
          $pdo = new PDO(
              "mysql:host=$host;dbname=$db;charset=utf8mb4",
              $user, $pass,
              [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
          );
          $stmt = $pdo->prepare("SELECT id, title, data FROM lessons WHERE id = ? AND status = 'published'");
          $stmt->execute([$lessonId]);
          $lesson = $stmt->fetch();
      } catch (Exception $e) {
          // Тихо — покажем 404 ниже
      }
  }

  if (!$lesson) {
      http_response_code(404);
      $title = "Урок не найден | Drumz";
  } else {
      $title = htmlspecialchars($lesson['title']) . " | Уроки | Drumz";
  }

  // ═══════════════════════════════════════════════════════════════
  // 2. Определяем, какие блоки используются, чтобы подключить только нужные скрипты
  // ═══════════════════════════════════════════════════════════════
  $usedBlockTypes = [];
  if ($lesson) {
      $data = json_decode($lesson['data'], true);
      if (isset($data['cards']) && is_array($data['cards'])) {
          foreach ($data['cards'] as $card) {
              if (!isset($card['blocks'])) continue;
              foreach ($card['blocks'] as $block) {
                  if (isset($block['type'])) {
                      $usedBlockTypes[$block['type']] = true;
                  }
              }
          }
      }
  }

  $blockScriptMap = [
      'text'         => '/DLE/components/blocks/text.js',
      'grid'         => '/DLE/components/blocks/grid.js',
      'notation'     => '/DLE/components/blocks/notation.js',
      'drumkit-mini' => '/DLE/components/blocks/drum-kit-mini.js',
      'drumkit-real' => '/DLE/components/blocks/drum-kit-real.js',
      'css-art'      => '/DLE/components/blocks/css-art.js',
      'svg-art'      => '/DLE/components/blocks/svg-art.js',
  ];
  

  $needsGrooveScribe = isset($usedBlockTypes['grid']) || isset($usedBlockTypes['notation']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $title; ?></title>
  <link rel="stylesheet" href="/assets/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/abcjs-audio.min.css">
  <style>
  /* Гарантируем, что body занимает минимум всю высоту экрана */
  body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  .lesson-viewer {
    flex: 1; /* Растягиваем контент, прижимая футер к низу */
    width: 100%;
    max-width: 900px;
    margin: 0 auto;
    padding: 40px 20px 60px;
  }
  .lesson-viewer-header {
    margin-bottom: 5px;
    padding-bottom: 5px;
    border-bottom: 0px solid #e2e8f0;
  }
  .lesson-viewer-header h1 {
    font-size: 1.8rem;
    margin: 0 0 8px;
  }
  .lesson-viewer-header .back-link {
    display: inline-block;
    color: #64748b;
    text-decoration: none;
    font-size: 0.9rem;
    margin-bottom: 5px;
  }
  .lesson-viewer-header .back-link:hover { color: #0f172a; }
  .lesson-player-wrapper {
    background: #fff;
    border: 0px solid #e2e8f0;
    border-radius: 12px;
    padding: 5px;
    min-height: 400px;
  }
  .lesson-not-found {
    /* Красиво центрируем сообщение, если урок не найден и контента мало */
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: center;
    padding: 80px 20px;
    color: #64748b;
    min-height: 50vh;
  }
  .lesson-not-found h1 { color: #0f172a; margin-bottom: 12px; }
</style>
</head>
<body>
  <?php require_once $base . '/includes/header.php'; ?>

  <?php if (!$lesson): ?>
    <div class="lesson-viewer">
      <div class="lesson-not-found">
        <h1>Урок не найден</h1>
        <p>Возможно, он был удалён или ещё не опубликован.</p>
        <p style="margin-top: 20px;"><a href="/lessons/" style="color: #3b82f6;">← Вернуться к списку уроков</a></p>
      </div>
    </div>
  <?php else: ?>
    <div class="lesson-viewer">
      <div class="lesson-viewer-header">
        <a href="/lessons/" class="back-link">← Все уроки</a>
        <h1><?php echo htmlspecialchars($lesson['title']); ?></h1>
      </div>

      <div class="lesson-player-wrapper">
        <div id="lesson-root"></div>
      </div>
    </div>

    <!-- Базовые скрипты движка -->
    <script src="/DLE/components/registry.js"></script>
    <script src="/DLE/LessonEngine.js"></script>

    <?php if ($needsGrooveScribe): ?>
      <script src="/assets/GrooveScribe/js/abc2svg-1.js"></script>
      <script src="/assets/GrooveScribe/js/groove_utils.js"></script>
    <?php endif; ?>

    <!-- Подключаем только те блоки, что реально используются в уроке -->
    <?php foreach ($blockScriptMap as $type => $src): ?>
      <?php if (isset($usedBlockTypes[$type])): ?>
        <script src="<?php echo $src; ?>"></script>
      <?php endif; ?>
    <?php endforeach; ?>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const lessonData = <?php echo $lesson['data']; ?>;
        window.currentLesson = new LessonPlayer('lesson-root', lessonData);
      });
    </script>
  <?php endif; ?>

  <?php require_once $base . '/includes/footer.php'; ?>
</body>
</html>
<?php
  $base = dirname(__DIR__, 2);
  $title = "Прогресс | Drumz";
  $description = "Ваш прогресс и достижения в Drumz.";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title><?php echo $title; ?></title>
  <meta name="description" content="<?php echo $description; ?>">

  <link rel="stylesheet" href="/assets/style.css?v=<?php echo time(); ?>">
</head>
<body>
  <?php require_once $base . '/includes/header.php'; ?>

  <div class="workspace-layout">
    <?php require_once __DIR__ . '/../sidebar.php'; ?>
    
    <main class="workspace-main">
      <div class="workspace-content">
        <h1>Прогресс</h1>
        
        <div id="progressLoading" class="progress-loading">Загрузка данных...</div>
        
        <div id="progressContent" class="progress-content" style="display: none;">
          <!-- Общий XP -->
          <div class="progress-summary">
            <div class="progress-card">
              <div class="progress-card-label">Всего опыта</div>
              <div class="progress-card-value" id="totalXP">0</div>
              <div class="progress-card-unit">XP</div>
            </div>
          </div>
          
          <!-- История событий -->
          <div class="progress-history">
            <h2>История активности</h2>
            <div id="xpLogList" class="xp-log-list">
              <!-- Заполняется через JS -->
            </div>
          </div>
        </div>
        
        <div id="progressError" class="progress-error" style="display: none;">
          Не удалось загрузить данные. Убедитесь, что вы авторизованы.
        </div>
      </div>
    </main>
  </div>

  <?php require_once $base . '/includes/footer.php'; ?>
  
  <script type="module" src="/workspace/progress/progress.js"></script>
</body>
</html>
<?php
  $base = dirname(__DIR__, 2);
  $title = "Настройки | Drumz";
  $description = "Настройки рабочего пространства Drumz.";
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
    <?php require_once __DIR__ . '/../sidebar.php'; ?> <!-- Подключаем сайдбар -->
    
    <main class="workspace-main">
      <div class="workspace-content">
        <h1>Настройки</h1>
        
        <div id="settingsStatus" class="settings-status"></div>
        
        <form id="settingsForm" class="settings-form">
          <div class="form-group">
            <label for="displayName" class="form-label">Отображаемое имя</label>
            <input type="text" id="displayName" name="displayName" class="form-input" placeholder="Как вас называть?">
            <p class="form-hint">Оставьте пустым, чтобы использовать имя из Google-аккаунта.</p>
          </div>

          <div class="form-group">
            <label class="form-checkbox">
              <input type="checkbox" id="showHints" name="showHints">
              <span>Показывать подсказки в интерфейсе</span>
            </label>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-primary">Сохранить</button>
          </div>
        </form>
        <!-- Кнопки выхода здесь больше нет -->
      </div>
    </main>
  </div>

  <?php require_once $base . '/includes/footer.php'; ?>
  <script type="module" src="/workspace/settings/settings.js"></script>
</body>
</html>
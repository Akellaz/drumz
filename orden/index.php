<?php require_once __DIR__ . '/../includes/seo.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require_once __DIR__ . '/../includes/seo.php'; ?>
  <title>Система Орден</title>
  <link rel="stylesheet" href="/assets/style.css?v=<?php echo time(); ?>">
</head>
<body>
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container">
    <h1>Система Орден</h1>

    <?php
    $currentPath = $_SERVER['REQUEST_URI'];
    $isOrdenPage = strpos($currentPath, '/orden/') === 0;
    if ($isOrdenPage): ?>
    <nav class="sub-nav" style="display: flex; flex-wrap: wrap; gap: 10px; margin: 20px 0; justify-content: center;">
      <a href="/orden/manifest/" class="<?php echo strpos($currentPath, '/orden/manifest/') === 0 ? 'btn active' : 'btn'; ?>">Свод принципов</a>
      <a href="/orden/levels/" class="<?php echo strpos($currentPath, '/orden/levels/') === 0 ? 'btn active' : 'btn'; ?>">Уровни</a>
      <a href="/orden/professions/" class="<?php echo strpos($currentPath, '/orden/professions/') === 0 ? 'btn active' : 'btn'; ?>">Профессии</a>
      <a href="/orden/achievements/" class="<?php echo strpos($currentPath, '/orden/achievements/') === 0 ? 'btn active' : 'btn'; ?>">Достижения</a>
      <a href="/orden/ratings/" class="<?php echo strpos($currentPath, '/orden/ratings/') === 0 ? 'btn active' : 'btn'; ?>">Рейтинг</a>
      <a href="/orden/shadows/" class="<?php echo strpos($currentPath, '/orden/shadows/') === 0 ? 'btn active' : 'btn'; ?>">Тени Мастеров</a>
    </nav>
    <?php endif; ?>

    <div class="card">
      <p><strong>«Орден Перкуссии»</strong> — это не просто программа обучения, а движение, объединяющее перкуссионистов по всему миру в едином пути от новичка до Концертмейстера.</p>
      <p>Здесь каждый выбирает свой путь, развивает уникальные навыки, получает признание и участвует в живом музыкальном сообществе.</p>
      <p style="margin-top: 16px; font-style: italic;">«Твой ритм — твоя сила. Каждый удар — шаг к легенде»</p>
    </div>

    <?php include __DIR__ . '/../form/widget.php'; ?>
  </main>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
<?php require_once __DIR__ . '/../includes/seo.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require_once __DIR__ . '/../includes/seo.php'; ?>
  <title>Рейтинг — Орден Перкуссии</title>
  <link rel="stylesheet" href="/assets/style.css?v=<?php echo time(); ?>">
</head>
<body>
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container">
    <h1>Рейтинг мастерства</h1>

    <?php
    $currentPath = $_SERVER['REQUEST_URI'];
    if (strpos($currentPath, '/orden/') === 0): ?>
    <nav class="sub-nav" style="display: flex; flex-wrap: wrap; gap: 10px; margin: 20px 0; justify-content: center;">
      <a href="/orden/manifest/" class="btn">Свод принципов</a>
      <a href="/orden/levels/" class="btn">Уровни</a>
      <a href="/orden/professions/" class="btn">Профессии</a>
      <a href="/orden/achievements/" class="btn">Достижения</a>
      <a href="/orden/ratings/" class="btn active">Рейтинг</a>
      <a href="/orden/shadows/" class="btn">Тени Мастеров</a>
    </nav>
    <?php endif; ?>

    <div class="card">
      <p>Рейтинг отражает ваше <strong>текущее мастерство</strong> в сравнении с другими участниками.</p>

      <h3>Шкала рейтинга</h3>
      <ul>
        <li><strong>800–1199</strong> — Новичок</li>
        <li><strong>1200–1599</strong> — Ударник / Барабанщик</li>
        <li><strong>1600–1999</strong> — Мастер-ритма</li>
        <li><strong>2000–2399</strong> — Сессионщик</li>
        <li><strong>2400–2799</strong> — Концертмейстер</li>
        <li><strong>2800–2850</strong> — Высший предел для живых участников</li>
        <li><strong>2850+</strong> — Только легенды (см. «Тени Мастеров»)</li>
      </ul>

      <p>Рейтинг растёт за сдачу уровней, участие в челленджах и одобренные видео. Он привязан к вашей профессии.</p>
      <p>Таблицы лидеров по профессиям станут доступны в ближайшем обновлении.</p>
    </div>

    <div style="text-align: center; margin-top: 20px;">
      <a href="/orden/" class="btn">← Назад к Ордену</a>
    </div>
  </main>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
<?php require_once __DIR__ . '/../includes/seo.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require_once __DIR__ . '/../includes/seo.php'; ?>
  <title>Достижения — Орден Перкуссии</title>
  <link rel="stylesheet" href="/assets/style.css?v=<?php echo time(); ?>">
</head>
<body>
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container">
    <h1>Достижения и награды</h1>
    <p>Собирайте значки, делитесь успехами и получайте бонусы!</p>

    <?php
    $currentPath = $_SERVER['REQUEST_URI'];
    if (strpos($currentPath, '/orden/') === 0): ?>
    <nav class="sub-nav" style="display: flex; flex-wrap: wrap; gap: 10px; margin: 20px 0; justify-content: center;">
      <a href="/orden/manifest/" class="btn">Свод принципов</a>
      <a href="/orden/levels/" class="btn">Уровни</a>
      <a href="/orden/professions/" class="btn">Профессии</a>
      <a href="/orden/achievements/" class="btn active">Достижения</a>
      <a href="/orden/ratings/" class="btn">Рейтинг</a>
      <a href="/orden/shadows/" class="btn">Тени Мастеров</a>
    </nav>
    <?php endif; ?>

    <div class="card">
      <h3>Типы достижений</h3>

      <h4>🏆 Ранговые</h4>
      <ul>
        <li>«Первый Удар» — завершил Уровень 1</li>
        <li>«Голос Ритма» — стал Барабанщиком</li>
        <li>«Носитель Грува» — достиг Уровня 4</li>
        <li>«Легенда Перкуссии» — Концертмейстер</li>
      </ul>

      <h4>🎖️ Профессиональные</h4>
      <ul>
        <li>«Огонь Метала» — прошёл ветку Метал-стража</li>
        <li>«Душа Свинга» — завершил путь Джаз-ритмиста</li>
        <li>«Хранитель Ритмов Мира» — сдал 3 этнических модуля</li>
      </ul>

      <h4>🌟 Событийные</h4>
      <ul>
        <li>«Марафонщик» — 100 дней практики подряд</li>
        <li>«Наставник» — помог 5 другим сдать уровень</li>
        <li>«Сценический Огонь» — 10 подтверждённых выступлений</li>
        <li>«Экспериментатор» — сдал задание на нестандартном инструменте</li>
      </ul>

      <h4>📦 Коллекционные</h4>
      <ul>
        <li>«Комплект Перкуссии» — 5 разных инструментов</li>
        <li>«Стилевой Алхимик» — 4 разных жанра</li>
        <li>«Видео-Легенда» — 10 одобренных видео</li>
      </ul>

      <p style="margin-top: 16px;">
        Все достижения отображаются в вашем <strong>публичном профиле</strong> и могут быть опубликованы в соцсетях.
      </p>
    </div>

    <div style="text-align: center; margin-top: 20px;">
      <a href="/orden/" class="btn">← Назад к Ордену</a>
    </div>
  </main>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
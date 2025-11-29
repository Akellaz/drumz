<?php require_once __DIR__ . '/../includes/seo.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require_once __DIR__ . '/../includes/seo.php'; ?>
  <title>Уровни и титулы — Орден Перкуссии</title>
  <link rel="stylesheet" href="/assets/style.css?v=<?php echo time(); ?>">
</head>
<body>
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container">
    <h1>Уровни и титулы</h1>

    <?php
    $currentPath = $_SERVER['REQUEST_URI'];
    if (strpos($currentPath, '/orden/') === 0): ?>
    <nav class="sub-nav" style="display: flex; flex-wrap: wrap; gap: 10px; margin: 20px 0; justify-content: center;">
      <a href="/orden/manifest/" class="btn">Свод принципов</a>
      <a href="/orden/levels/" class="btn active">Уровни</a>
      <a href="/orden/professions/" class="btn">Профессии</a>
      <a href="/orden/achievements/" class="btn">Достижения</a>
      <a href="/orden/ratings/" class="btn">Рейтинг</a>
      <a href="/orden/shadows/" class="btn">Тени Мастеров</a>
    </nav>
    <?php endif; ?>

    <div class="card">
      <table style="width: 100%; border-collapse: collapse; margin-top: 16px;">
        <thead>
          <tr style="background: var(--primary-light); text-align: left;">
            <th style="padding: 12px; border: 1px solid var(--border);">Уровень</th>
            <th style="padding: 12px; border: 1px solid var(--border);">Титул</th>
            <th style="padding: 12px; border: 1px solid var(--border);">Описание</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>1</td><td>Новичок</td><td>Базовое владение инструментом, ритм, метроном, простые рудименты</td></tr>
          <tr><td>2</td><td>Ударник</td><td>Свободное владение популярными стилями, динамикой, сопровождением</td></tr>
          <tr><td>3</td><td>Барабанщик</td><td>Игра в ансамбле, импровизация в рамках жанра, понимание формы</td></tr>
          <tr><td>4</td><td>Мастер-ритма</td><td>Глубокое владение 2+ стилями, работа с нестандартными размерами</td></tr>
          <tr><td>5</td><td>Сессионщик</td><td>Профессиональный уровень: студийная запись, чтение партитур, адаптация</td></tr>
          <tr><td>6</td><td>Концертмейстер перкуссии</td><td>Ведущий исполнитель, наставник, создатель оригинального материала</td></tr>
        </tbody>
      </table>

      <p style="margin-top: 20px;">
        Каждый уровень подтверждается <strong>цифровым сертификатом</strong> и требует сдачи обязательных заданий через Совет Мастеров.
      </p>
    </div>

    <div style="text-align: center; margin-top: 20px;">
      <a href="/orden/" class="btn">← Назад к Ордену</a>
    </div>
  </main>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
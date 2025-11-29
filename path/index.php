<?php require_once __DIR__ . '/../includes/seo.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require_once __DIR__ . '/../includes/seo.php'; ?>
  <link rel="stylesheet" href="/assets/style.css?v=<?php echo time(); ?>">
  <title>Путь барабанщика | drumz.ru</title>
  <style>
    /* Специфичные стили для этой страницы */
    .level-card {
      background: white;
      border-radius: var(--radius);
      border: 1px solid var(--border);
      margin-bottom: 24px;
      overflow: hidden;
      box-shadow: var(--shadow);
    }
    .level-header {
      padding: 16px 20px;
      background: var(--primary-light);
      color: var(--primary);
      font-size: 1.4rem;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .level-header::after {
      content: '+';
      font-size: 1.5em;
      transition: transform 0.3s;
    }
    .level-header.active::after {
      content: '−';
    }
    .level-body {
      padding: 0;
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.4s ease-out;
    }
    .requirements {
      list-style: none;
      margin: 0;
      padding: 20px;
      background: #f9f9f9;
      border-top: 1px dashed var(--border);
    }
    .requirements li {
      margin-bottom: 12px;
      padding-left: 24px;
      position: relative;
      font-size: 0.95em;
      line-height: 1.4;
    }
    .requirements li::before {
      content: '•';
      color: var(--primary);
      font-weight: bold;
      position: absolute;
      left: 8px;
    }
    .step-label {
      display: inline-block;
      background: var(--primary);
      color: white;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.8em;
      font-weight: 600;
      margin-bottom: 12px;
    }
    .quote {
      font-style: italic;
      text-align: center;
      margin: 30px 20px;
      padding: 20px;
      background: var(--primary-light);
      border-radius: var(--radius);
      color: var(--text);
    }
    .cta-box {
      text-align: center;
      padding: 30px 20px;
      background: var(--primary);
      color: white;
      border-radius: var(--radius);
      margin: 24px;
    }
    .cta-box h2 {
      color: white;
      margin-top: 0;
    }
    .btn-cta {
      background: white;
      color: var(--primary);
      padding: 12px 30px;
      font-weight: bold;
      border: none;
      margin-top: 16px;
    }
    .btn-cta:hover {
      background: var(--primary-light);
    }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container">
    <h1>Путь барабанщика</h1>
    <p style="text-align: center; margin-bottom: 32px;">
      Это путешествие от первого удара до уверенности, мастерства и лидерства.
      Пройдите 5 уровней, каждый из которых состоит из 4 ступеней.
    </p>

    <!-- Уровень 1 -->
    <div class="level-card">
      <div class="level-header">Ударник</div>
      <div class="level-body">
        <ul class="requirements">
          <li><strong>Ступень 1:</strong> Правильная постановка рук, держание палочек</li>
          <li><strong>Ступень 2:</strong> Игра четвертей, восьмых, шестнадцатых</li>
          <li><strong>Ступень 3:</strong> Ритмы: полька, вальс, рок, блюз</li>
          <li><strong>Ступень 4:</strong> Работа с метрономом и минусовками</li>
        </ul>
      </div>
    </div>

    <!-- Уровень 2 -->
    <div class="level-card">
      <div class="level-header">Барабанщик</div>
      <div class="level-body">
        <ul class="requirements">
          <li><strong>Ступень 1:</strong> Парадидл, up-down, форшлаг</li>
          <li><strong>Ступень 2:</strong> Свинг, фанк, мамба, джаз-вальс</li>
          <li><strong>Ступень 3:</strong> Игра в ансамбле (дуэт, трио)</li>
          <li><strong>Ступень 4:</strong> Подбор ритма по слуху, запись треков</li>
        </ul>
      </div>
    </div>

    <!-- Уровень 3 -->
    <div class="level-card">
      <div class="level-header">Студийный</div>
      <div class="level-body">
        <ul class="requirements">
          <li><strong>Ступень 1:</strong> Чистота звука, контроль динамики</li>
          <li><strong>Ступень 2:</strong> Фанк, соул, R&B, пост-рок</li>
          <li><strong>Ступень 3:</strong> Запись партий, работа с DAW</li>
          <li><strong>Ступень 4:</strong> Сведение, самоанализ, обратная связь</li>
        </ul>
      </div>
    </div>

    <!-- Уровень 4 -->
    <div class="level-card">
      <div class="level-header">Сессионный</div>
      <div class="level-body">
        <ul class="requirements">
          <li><strong>Ступень 1:</strong> Идеальная игра в любом темпе (60–160 BPM)</li>
          <li><strong>Ступень 2:</strong> Джаз, латина, фьюжн, прогрессив-рок</li>
          <li><strong>Ступень 3:</strong> Сессия "с листа", живой джем</li>
          <li><strong>Ступень 4:</strong> Коллаборации, создание минусовок</li>
        </ul>
      </div>
    </div>

    <!-- Уровень 5 -->
    <div class="level-card">
      <div class="level-header">Концертмейстер</div>
      <div class="level-body">
        <ul class="requirements">
          <li><strong>Ступень 1:</strong> Руководство ритм-секцией и группой ударных</li>
          <li><strong>Ступень 2:</strong> Ведение ансамбля без дирижёра</li>
          <li><strong>Ступень 3:</strong> Организация репетиций и концертов</li>
          <li><strong>Ступень 4:</strong> Наставничество, преподавание, авторские проекты</li>
        </ul>
      </div>
    </div>

    <!-- Цитата -->
    <div class="quote">
      «Концертмейстер на ударных — это тот, кто чувствует всё: дыхание вокалиста, паузу гитариста, напряжение басиста. Он не просто играет — он направляет.»<br>
      <strong>— Сергей Щепотин, основатель drumz.ru</strong>
    </div>

    <!-- CTA -->
    <div class="cta-box">
      <h2>Готовы начать свой путь?</h2>
      <p>Запишитесь на пробный урок — и сделайте первый шаг к мастерству.</p>
      <a href="/about/#contact" class="btn btn-cta">Записаться на пробное занятие</a>
    </div>
  </main>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>

  <!-- Интерактивность -->
  <script>
    document.querySelectorAll('.level-header').forEach(header => {
      header.addEventListener('click', () => {
        const body = header.nextElementSibling;
        const card = header.parentElement;

        // Toggle active class
        header.classList.toggle('active');
        
        // Toggle expand/collapse
        if (body.style.maxHeight) {
          body.style.maxHeight = null;
        } else {
          body.style.maxHeight = body.scrollHeight + 'px';
        }
      });
    });
  </script>
</body>
</html>
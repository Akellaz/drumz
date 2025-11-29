<?php require_once __DIR__ . '/../includes/seo.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Вклад в развитие — Drumz</title>
  <link rel="stylesheet" href="/assets/style.css?v=<?= time() ?>">
  <style>
    .legal-details {
      margin-top: 40px;
      padding: 16px;
      font-size: 0.85rem;
      color: var(--text-light);
      border-top: 1px solid var(--border);
    }
    .legal-details p {
      margin: 4px 0;
    }
    .legal-details small {
      font-size: 0.85rem;
      opacity: 0.8;
    }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container">
    <h1>Вклад в развитие</h1>

    <p>Drumz — независимый образовательный проект, в котором ритм становится искусством, а путь ученика — осознанным.</p>

    <p>Вы можете внести <strong>добровольный взнос</strong> на поддержку и развитие проекта. Взнос не является оплатой за товар или услугу и <strong>не предполагает встречного предоставления</strong>.</p>

    <p>Средства пойдут на:</p>
    <ul>
      <li>Разработку и поддержку бесплатных интерактивных инструментов</li>
      <li>Создание новых упражнений по ритму, времени и перкуссии</li>
      <li>Развитие системы «Орден» как пространства осмысленного музыкального труда</li>
      <li>Обеспечение доступности материалов для всех желающих</li>
    </ul>

    <p>Благодаря таким вкладам Drumz остаётся открытым, независимым и живым.</p>

    <!-- Форма ЮKassa -->
    <div id="yoo-payment" style="margin: 30px 0;"></div>

  </main>

  <!-- Незаметные, но полные реквизиты -->
  <div class="legal-details">
    <p>Щепотин Сергей Владимирович, самозанятый (ИНН 504603187328). Деятельность: разработка и поддержка образовательного проекта в области музыкального образования (drumz.ru).</p>
    <small>Согласно ст. 579 ГК РФ, взнос является добровольным и не влечёт обязанности встречного предоставления.</small>
  </div>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>

  <script src="https://yookassa.ru/checkout-widget/v1"></script>
  <script>
    const shopId = 'your-shop-id';

    document.getElementById('yoo-payment').innerHTML =
      '<button class="btn" style="width:100%; padding:14px;">Поддержать проект</button>';

    document.querySelector('#yoo-payment button').onclick = () => {
      if (typeof window.CheckoutWidget !== 'undefined') {
        const widget = new window.CheckoutWidget({ shopId: shopId });
        widget.open();
      } else {
        alert('Не удалось загрузить платёжную форму. Попробуйте позже.');
      }
    };
  </script>
</body>
</html>
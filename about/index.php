<?php require_once __DIR__ . '/../includes/seo.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require_once __DIR__ . '/../includes/seo.php'; ?>
  <link rel="stylesheet" href="/assets/style.css">
  <style>
    .studio-card {
      margin-bottom: 32px;
      padding: 20px;
      background: #f8f9fa;
      border-radius: 12px;
      position: relative;
    }
    .map-container {
      margin-top: 16px;
      height: 250px;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      position: relative;
    }
    .map-container iframe {
      width: 100%;
      height: 100%;
      border: none;
    }
    .map-attribution {
      position: absolute;
      bottom: 4px;
      left: 8px;
      font-size: 10px;
      color: #999;
    }
    .highlight-box {
      margin-top: 20px;
      padding: 16px;
      background: rgba(255,107,107,0.15);
      border: 1px solid #ff6b6b;
      border-radius: 8px;
    }
    .btn {
      display: inline-block;
      padding: 10px 20px;
      background: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
      margin-top: 8px;
    }
    .btn--whatsapp {
      background: #25D366;
    }
    .card h3 {
      margin-top: 0.5em;
    }
    .studio-card:nth-child(1) .map-container { border-color: #007bff; }
    .studio-card:nth-child(2) .map-container { border-color: #28a745; }
    .studio-card:nth-child(3) .map-container { border-color: #6f42c1; }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container" itemscope itemtype="https://schema.org/Person">
    <!-- === О ПРЕПОДАВАТЕЛЕ === -->
    <div class="card" id="about">
      <h2>О преподавателе</h2>
      <p itemprop="name">Меня зовут <span itemprop="givenName">Сергей Щепотин</span> — я преподаю игру на барабанах в Троицке.</p>

      <p><strong>Профессиональные уроки для детей от 7 лет и взрослых</strong> — от первого удара до концертной сцены.</p>

      <h3>Образование и опыт:</h3>
      <ul>
        <li>ДШИ им. Глинки</li>
        <li>Big Band под управлением В.И. Герасимова</li>
        <li>Freedom Jazz Orchestra Николя Филибера</li>
        <li>Более 10 лет преподавания в Троицке</li>
        <li>Участие в концертных программах</li>
      </ul>
    </div>

   

    <!-- === СТУДИИ С КАРТАМИ === -->
    <div class="card" id="studios">
      <h2>Наши студии</h2>

      <!-- Студия 1: Микрорайон В, Троицк -->
      <div class="studio-card">
        <h3>📍 Студия в Троицке</h3>
        <p><strong>Адрес:</strong> Микрорайон В, 59 ст1</p>
		        <p><em>Занятия по предварительной записи</em></p>
        <div class="map-container">
          <iframe
            src="https://yandex.ru/map-widget/v1/?ll=37.296571%2C55.491124&mode=whatshere&whatshere%5Bpoint%5D=37.294792%2C55.491071&whatshere%5Bzoom%5D=17.33&z=17.33"
            frameborder="0"
            allowfullscreen
          ></iframe>
          <div class="map-attribution">© Яндекс.Карты</div>
        </div>
      </div>

      <!-- Студия 2: Рогозинино -->
      <div class="studio-card">
        <h3>📍 Студия в Рогозинино</h3>
        <p><strong>Адрес:</strong> Луговая ул., вл20Ас1, д. Рогозинино, этаж 2</p>
        <p><em>Занятия по предварительной записи</em></p>
        <div class="map-container">
          <iframe
            src="https://yandex.ru/map-widget/v1/?ll=37.185923%2C55.539976&mode=whatshere&whatshere%5Bpoint%5D=37.187854%2C55.539842&whatshere%5Bzoom%5D=16&z=16"
            frameborder="0"
            allowfullscreen
          ></iframe>
          <div class="map-attribution">© Яндекс.Карты</div>
        </div>
      </div>

      <!-- Студия 3: Жуковка -->
      <div class="studio-card">
        <h3>📍 Студия в Жуковке</h3>
        <p><strong>Адрес:</strong> Деревня Жуковка, вл1с1, район Троицк, Москва</p>
        <p><em>Занятия по предварительной записи</em></p>
        <div class="map-container">
          <iframe
            src="https://yandex.ru/map-widget/v1/?ll=37.286624%2C55.510871&mode=whatshere&whatshere%5Bpoint%5D=37.286074%2C55.511114&whatshere%5Bzoom%5D=17.59&z=17.59"
            frameborder="0"
            allowfullscreen
          ></iframe>
          <div class="map-attribution">© Яндекс.Карты</div>
        </div>
      </div>
    </div>

    <!-- === ФОРМА ЗАПИСИ === -->
    <?php include '../form/widget.php'; ?>

    <!-- === КОНТАКТЫ И ПРЕДЛОЖЕНИЕ === -->
    <div class="card" id="contact">
      <h2>Связаться и записаться</h2>
      <p><strong>📞 Телефон:</strong> <a href="tel:+79309930503" itemprop="telephone">+7 (930) 993-05-03</a></p>
      <p><strong>🕐 Режим работы:</strong> ежедневно с 08:00 до 23:00</p>
      <p>
        <a href="https://t.me/SchepotinSergey" class="btn" target="_blank">Telegram</a>
        <a href="https://wa.me/79309930503" class="btn btn--whatsapp" target="_blank">WhatsApp</a>
      </p>

      <div class="highlight-box">
        <h3>🔥 Пробный урок — 1500 ₽</h3>
        <p>Полноценное занятие 60 минут со скидкой 50% — убедитесь, что барабаны для вас!</p>
      </div>
    </div>

    <!-- === FAQ === -->
    <section class="faq-section" style="margin-top: 40px; padding: 20px; background: #f9f9f9; border-radius: 12px;">
      <h2>Вопросы и ответы</h2>
      <div itemscope itemtype="https://schema.org/FAQPage">
        <div itemscope itemtype="https://schema.org/Question" style="margin-bottom: 24px;">
          <h3 itemprop="name">Сколько стоит пробный урок?</h3>
          <div itemscope itemtype="https://schema.org/Answer" itemprop="acceptedAnswer">
            <div itemprop="text">Пробный урок — 1500 ₽ вместо 3000 ₽. Длится 60 минут и проходит в одной из трёх студий.</div>
          </div>
        </div>
        <div itemscope itemtype="https://schema.org/Question" style="margin-bottom: 24px;">
          <h3 itemprop="name">Для кого уроки?</h3>
          <div itemscope itemtype="https://schema.org/Answer" itemprop="acceptedAnswer">
            <div itemprop="text">Для детей от 7 лет и взрослых любого уровня — от новичка до профессионала.</div>
          </div>
        </div>
        <div itemscope itemtype="https://schema.org/Question" style="margin-bottom: 24px;">
          <h3 itemprop="name">Как записаться?</h3>
          <div itemscope itemtype="https://schema.org/Answer" itemprop="acceptedAnswer">
            <div itemprop="text">Напишите в Telegram: <a href="https://t.me/SchepotinSergey">@SchepotinSergey</a> — выберем студию и удобное время.</div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
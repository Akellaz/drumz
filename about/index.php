<!DOCTYPE html>
<html lang="ru" itemscope itemtype="https://schema.org/LocalBusiness">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require_once __DIR__ . '/../includes/seo.php'; ?>
  <link rel="stylesheet" href="/assets/style.css?v=<?= filemtime(__DIR__ . '/../assets/style.css') ?>">
  <!-- остальные стили -->
  <style>
    .studio-card {
      margin-bottom: 32px;
      padding: 20px;
      background: var(--card-bg);
      border-radius: var(--radius);
      position: relative;
      border: 1px solid var(--border);
    }
    .map-container {
      margin-top: 16px;
      height: 250px;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: var(--shadow);
      position: relative;
      border: 1px solid var(--border);
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
      color: var(--text-light);
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
      background: var(--primary);
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
      margin-top: 8px;
      margin-right: 10px;
      border: 1px solid var(--primary);
      transition: all 0.2s;
    }
    .btn:hover {
      background: var(--primary-light);
      color: var(--text);
      text-decoration: none;
    }
    .btn--whatsapp {
      background: #25D366;
      border-color: #25D366;
    }
    .btn--whatsapp:hover {
      background: #128C7E;
      border-color: #128C7E;
    }
    .card h3 {
      margin-top: 0.5em;
      color: var(--text);
    }
    .studio-card:nth-child(1) .map-container { border-color: var(--primary); }
    .studio-card:nth-child(2) .map-container { border-color: var(--success); }
    .studio-card:nth-child(3) .map-container { border-color: #6f42c1; }
    
    /* Секция контента */
    .content-section {
      margin-top: 40px;
      padding-top: 20px;
      border-top: 1px solid var(--border);
    }
    
    .content-section h2 {
      color: var(--text);
      margin-top: 30px;
      margin-bottom: 15px;
    }
    
    .content-section p {
      line-height: 1.6;
      margin-bottom: 15px;
    }
    
    .content-section ul, 
    .content-section ol {
      margin-bottom: 20px;
      padding-left: 20px;
    }
    
    .content-section li {
      margin-bottom: 10px;
      line-height: 1.5;
    }
    
    .keywords {
      background-color: var(--card-bg);
      padding: 15px;
      border-radius: var(--radius);
      border: 1px solid var(--border);
      font-size: 0.85em;
      color: var(--text-light);
    }
    
    /* FAQ стили */
    .faq-section {
      margin-top: 40px;
      padding: 20px;
      background: var(--card-bg);
      border-radius: var(--radius);
      border: 1px solid var(--border);
    }
    
    .faq-section h2 {
      margin-top: 0;
      color: var(--text);
    }
    
    .faq-section h3 {
      color: var(--text);
      margin-bottom: 10px;
    }
    
    .faq-item {
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 1px solid var(--border);
    }
    
    .faq-item:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }
    
    /* Адаптивность */
    @media (max-width: 768px) {
      .map-container {
        height: 200px;
      }
      .btn {
        display: block;
        margin: 10px 0;
        text-align: center;
      }
    }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container" itemprop="mainEntity" itemscope itemtype="https://schema.org/Person">
    <h1 itemprop="name"><?= htmlspecialchars($title) ?></h1>
    
  

    <!-- === СТУДИИ С КАРТАМИ === -->
    <div class="card" id="studios">
      <h2>Наши студии</h2>

      <!-- Студия 1: Микрорайон В, Троицк -->
      <div class="studio-card" itemscope itemtype="https://schema.org/Place">
        <h3>📍 Студия в Троицке</h3>
        <p><strong>Адрес:</strong> <span itemprop="address">Микрорайон В, 59 ст1</span></p>
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
      <div class="studio-card" itemscope itemtype="https://schema.org/Place">
        <h3>📍 Студия в Рогозинино</h3>
        <p><strong>Адрес:</strong> <span itemprop="address">Луговая ул., вл20Ас1, д. Рогозинино, этаж 2</span></p>
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
      <div class="studio-card" itemscope itemtype="https://schema.org/Place">
        <h3>📍 Студия в Жуковке</h3>
        <p><strong>Адрес:</strong> <span itemprop="address">Деревня Жуковка, вл1с1, район Троицк, Москва</span></p>
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
        <a href="https://t.me/SchepotinSergey" class="btn" target="_blank" rel="noopener">Telegram</a>
        <a href="https://wa.me/79309930503" class="btn btn--whatsapp" target="_blank" rel="noopener">WhatsApp</a>
      </p>
</div>

    
   
  </main>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>

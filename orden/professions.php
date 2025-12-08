<?php require_once __DIR__ . '/../includes/seo.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require_once __DIR__ . '/../includes/seo.php'; ?>
  <title>Профессии — Орден Перкуссии</title>
  <link rel="stylesheet" href="/assets/style.css?v=<?php echo time(); ?>">
  <style>
    .skills-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 20px;
      margin: 30px 0;
      padding: 20px;
      background: var(--card-bg);
      border-radius: var(--radius);
      border: 1px solid var(--border);
    }
    
    .skill-category {
      background: white;
      border-radius: 10px;
      padding: 15px;
      box-shadow: var(--shadow);
      border: 1px solid var(--border);
    }
    
    .category-header {
      text-align: center;
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 2px solid var(--border);
      font-weight: bold;
      color: var(--primary);
    }
    
    .skill-tree {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    
    .skill-node {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      font-size: 0.7rem;
      font-weight: 600;
      color: white;
      margin: 8px 0;
      cursor: pointer;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      position: relative;
    }
    
    .skill-node:hover {
      opacity: 0.9;
    }
    
    .skill-connector {
      width: 3px;
      height: 20px;
      background: #cbd5e0;
      margin: 0;
    }
    
    .info-panel {
      background: white;
      padding: 20px;
      border-radius: var(--radius);
      border: 1px solid var(--border);
      margin: 20px 0;
      display: none;
    }
    
    .info-panel.active {
      display: block;
      animation: fadeIn 0.3s;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .info-panel h3 {
      margin-top: 0;
      color: var(--primary);
    }
    
    /* Подменю Ордена */
    .sub-nav {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin: 20px 0;
      justify-content: center;
    }
    .sub-nav .btn {
      background: var(--card-bg);
      color: var(--text);
      text-decoration: none;
      font-weight: 500;
      border: 1px solid var(--border);
      padding: 8px 16px;
      border-radius: 8px;
    }
    .sub-nav .btn:hover,
    .sub-nav .btn.active {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
    }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container">
    <h1>Профессии</h1>

    <?php
    $currentPath = $_SERVER['REQUEST_URI'];
    if (strpos($currentPath, '/orden/') === 0): ?>
    <nav class="sub-nav">
      <a href="/orden/manifest/" class="btn">Свод принципов</a>
      <a href="/orden/levels/" class="btn">Уровни</a>
      <a href="/orden/professions/" class="btn active">Профессии</a>
      <a href="/orden/achievements/" class="btn">Достижения</a>
      <a href="/orden/ratings/" class="btn">Рейтинг</a>
      <a href="/orden/shadows/" class="btn">Тени Мастеров</a>
    </nav>
    <?php endif; ?>

    <div class="skills-grid">
      <!-- Категория: Грув и Поп -->
      <div class="skill-category">
        <div class="category-header">Грув и Поп</div>
        <div class="skill-tree">
          <div class="skill-node" style="background: #48bb78" data-key="groove-basic">
            Грув-мастер
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #48bb78" data-key="studio-tech">
            Студийный Техник
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #48bb78" data-key="groove-king">
            Король Ритма
          </div>
        </div>
      </div>

      <!-- Категория: Джаз и Импровизация -->
      <div class="skill-category">
        <div class="category-header">Джаз и Импровизация</div>
        <div class="skill-tree">
          <div class="skill-node" style="background: #ed8936" data-key="jazz-basic">
            Джаз-ритмист
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #ed8936" data-key="fusionist">
            Фьюжн-Импровизатор
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #ed8936" data-key="virtuoso">
            Виртуоз Импровизации
          </div>
        </div>
      </div>

       <!-- Категория: Этнические Стили -->
      <div class="skill-category">
        <div class="category-header">Этнические Стили</div>
        <div class="skill-tree">
          <div class="skill-node" style="background: #805ad5" data-key="ethno-basic">
            Этно-следопыт
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #805ad5" data-key="world-beat">
            Хранитель Культур
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #805ad5" data-key="culture-keeper">
            Хранитель Традиций
          </div>
        </div>
      </div>
	 

      <!-- Категория: Метал и Рок -->
      <div class="skill-category">
        <div class="category-header">Метал и Рок</div>
        <div class="skill-tree">
          <div class="skill-node" style="background: #e53e3e" data-key="metal-basic">
            Метал-страж
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #e53e3e" data-key="titan">
            Титан Метала
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #e53e3e" data-key="legend">
            Легенда Метала
          </div>
        </div>
      </div>

      <!-- Категория: Латинские Ритмы -->
      <div class="skill-category">
        <div class="category-header">Латинские Ритмы</div>
        <div class="skill-tree">
          <div class="skill-node" style="background: #d69e2e" data-key="latin-basic">
            Латин-дух
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #d69e2e" data-key="latin-expert">
            Эксперт Латинских Ритмов
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #d69e2e" data-key="rhythm-master">
            Мастер Латинских Ритмов
          </div>
        </div>
      </div>

 <!-- Категория: Математика Ритма -->
      <div class="skill-category">
        <div class="category-header">Математика Ритма</div>
        <div class="skill-tree">
          <div class="skill-node" style="background: #9f7aea" data-key="math-basic">
            Математик ритма
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #9f7aea" data-key="architect">
            Архитектор Времени
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #9f7aea" data-key="time-lord">
            Повелитель Времени
          </div>
        </div>
      </div>
    

      <!-- Категория: Студийная Работа -->
      <div class="skill-category">
        <div class="category-header">Студийная Работа</div>
        <div class="skill-tree">
          <div class="skill-node" style="background: #38b2ac" data-key="studio-basic">
            Студийный техник
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #38b2ac" data-key="master-tech">
            Мастер Студийных Технологий
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #38b2ac" data-key="studio-master">
            Мастер Звукозаписи
          </div>
        </div>
      </div>

      <!-- Категория: Шоу и Сцена -->
      <div class="skill-category">
        <div class="category-header">Шоу и Сцена</div>
        <div class="skill-tree">
          <div class="skill-node" style="background: #db64ac" data-key="show-basic">
            Шоу-перкуссионер
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #db64ac" data-key="showmaster">
            Мастер Шоу
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #db64ac" data-key="stage-legend">
            Легенда Сцены
          </div>
        </div>
      </div>

      <!-- Категория: Преподавание -->
      <div class="skill-category">
        <div class="category-header">Преподавание</div>
        <div class="skill-tree">
          <div class="skill-node" style="background: #4299e1" data-key="teacher-basic">
            Учитель
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #4299e1" data-key="educator">
            Педагог-гуру
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #4299e1" data-key="guru">
            Мудрец Ударного Дела
          </div>
        </div>
      </div>

      <!-- Категория: Оркестровая Перкуссия -->
      <div class="skill-category">
        <div class="category-header">Оркестровая Перкуссия</div>
        <div class="skill-tree">
          <div class="skill-node" style="background: #ed64a6" data-key="orchestra-basic">
            Симфонист
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #ed64a6" data-key="maestro">
            Маэстро Удара
          </div>
          <div class="skill-connector"></div>
          <div class="skill-node" style="background: #ed64a6" data-key="virtuoso-orchestra">
            Виртуоз Оркестра
          </div>
        </div>
      </div>
    </div>

    <div class="info-panel" id="infoPanel">
      <h3 id="infoTitle">Метал-страж</h3>
      <p id="infoDesc">Метал, хард-рок. Скорость, бласт-бит, двойная педаль. Сила и выносливость.</p>
    </div>

    <div class="card" style="margin-top: 30px;">
      <h3>Система Развития</h3>
      <p>Каждая специализация развивается по трем уровням:</p>
      <ul>
        <li><strong>Базовый</strong> - начальный уровень профессии</li>
        <li><strong>Продвинутый</strong> - мастерство в выбранной области</li>
        <li><strong>Экспертный</strong> - вершина мастерства</li>
      </ul>
      <p>Вы можете выбрать одну или несколько специализаций для развития. Каждая профессия — это стиль мышления, ритмическая философия. Каждый уровень «Ордена» — это дорожный знак для тех, кто заблудился в бесконечных роликах на YouTube.</p>
    </div>

    <div style="text-align: center; margin-top: 20px;">
      <a href="/orden/" class="btn">← Назад к Ордену</a>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const infoPanel = document.getElementById('infoPanel');
      const infoTitle = document.getElementById('infoTitle');
      const infoDesc = document.getElementById('infoDesc');

      const skills = {
        'groove-basic': {
          name: 'Грув-мастер',
          desc: 'Поп, фанк, R&B. Точность, динамика, работа с басом. Идеален для сессий и эстрады.'
        },
        'jazz-basic': {
          name: 'Джаз-ритмист',
          desc: 'Джаз, свинг, фьюжн. Импровизация, работа с ride-тарелкой, swing feel.'
        },
        'math-basic': {
          name: 'Математик ритма',
          desc: 'Полиметрия, нечётные размеры, прог-рок, фьюжн. Ритм как структура, логика и тайна.'
        },
        'metal-basic': {
          name: 'Метал-страж',
          desc: 'Метал, хард-рок. Скорость, бласт-бит, двойная педаль. Сила и выносливость.'
        },
        'latin-basic': {
          name: 'Латин-дух',
          desc: 'Сальса, мамбо, афро-куба. Полиритмия, конги, клаве. Ритм танца и страсти.'
        },
        'ethno-basic': {
          name: 'Этно-следопыт',
          desc: 'Африка, Индия, Ближний Восток. Экзотические инструменты: дарбука, уду, табла.'
        },
        'studio-basic': {
          name: 'Студийный техник',
          desc: 'Многожанровая запись, чтение чартов, подстройка под продюсера. Ухо и гибкость.'
        },
        'show-basic': {
          name: 'Шоу-перкуссионер',
          desc: 'Сценичность, визуальная техника, кастомы. Игра — это шоу!'
        },
        'teacher-basic': {
          name: 'Учитель',
          desc: 'Передача знаний, развитие учеников, методика преподавания ударных инструментов.'
        },
        'titan': {
          name: 'Титан Метала',
          desc: 'Мастер тяжелого звука. Владеет техниками двойной бас-педали, бласт-битами и сложными ритмами экстремального металла.'
        },
        'fusionist': {
          name: 'Фьюжн-Импровизатор',
          desc: 'Эксперт в сложных джазовых формах. Создает уникальные ритмические ландшафты с элементами импровизации.'
        },
        'studio-tech': {
          name: 'Студийный Техник',
          desc: 'Профессионал студийной записи. Отлично владеет всеми аспектами сессионной игры и техническими навыками.'
        },
        'architect': {
          name: 'Архитектор Времени',
          desc: 'Мастер сложных ритмических структур. Создает уникальные полиритмические композиции и метрические модуляции.'
        },
        'latin-expert': {
          name: 'Эксперт Латинских Ритмов',
          desc: 'Глубокое понимание латинской перкуссии. Мастер сальсы, босановы, румбы и других латинских жанров.'
        },
        'world-beat': {
          name: 'Хранитель Культур',
          desc: 'Эксперт в этнических ритмах. Владеет традициями и техниками ударных инструментов разных культур.'
        },
        'showmaster': {
          name: 'Мастер Шоу',
          desc: 'Эксперт в сценическом исполнении. Создает зрелищные перкуссионные шоу с уникальной визуальной составляющей.'
        },
        'educator': {
          name: 'Педагог-гуру',
          desc: 'Мастер преподавания. Разрабатывает уникальные методики и ведет учеников к вершинам мастерства.'
        },
        'legend': {
          name: 'Легенда Метала',
          desc: 'Живая легенда метал-сцены. Создает эталонные ритмы и вдохновляет новые поколения металлистов.'
        },
        'virtuoso': {
          name: 'Виртуоз Импровизации',
          desc: 'Абсолютный мастер джазовой импровизации. Создает уникальные ритмические пейзажи в реальном времени.'
        },
        'master-tech': {
          name: 'Мастер Студийных Технологий',
          desc: 'Эксперт по всем аспектам студийной работы. От записи до сведения и мастеринга перкуссионных партий.'
        },
        'time-lord': {
          name: 'Повелитель Времени',
          desc: 'Архитектор самых сложных ритмических структур. Мастер метрических модуляций и полиритмий.'
        },
        'groove-king': {
          name: 'Король Ритма',
          desc: 'Воплощение ритма. Создает грувы, которые становятся культовыми и вдохновляют музыкантов по всему миру.'
        },
        'culture-keeper': {
          name: 'Хранитель Традиций',
          desc: 'Создатель культурных мостов через ритм. Сохраняет и развивает традиции этнической перкуссии.'
        },
        'stage-legend': {
          name: 'Легенда Сцены',
          desc: 'Икона шоу-перкуссии. Его выступления становятся событиями в музыкальной индустрии.'
        },
        'guru': {
          name: 'Мудрец Ударного Дела',
          desc: 'Философ и учитель. Передает не только технику, но и философию игры на ударных инструментах.'
        },
        'rhythm-master': {
          name: 'Мастер Латинских Ритмов',
          desc: 'Абсолютный эксперт в латинской перкуссии. Создает ритмы, которые заставляют двигаться тела.'
        },
        'studio-master': {
          name: 'Мастер Звукозаписи',
          desc: 'Эксперт по всем аспектам звукозаписи. От записи до сведения и мастеринга перкуссионных партий.'
        },
        'orchestra-basic': {
          name: 'Симфонист',
          desc: 'Мастер оркестровой перкуссии. Владеет широким спектром ударных инструментов для симфонического оркестра.'
        },
        'maestro': {
          name: 'Маэстро Удара',
          desc: 'Эксперт в исполнении сложных оркестровых партий. Обладает исключительной точностью и музыкальностью.'
        },
        'virtuoso-orchestra': {
          name: 'Виртуоз Оркестра',
          desc: 'Абсолютный мастер оркестровой перкуссии. Создает эталонные исполнения и вдохновляет новые поколения музыкантов.'
        }
      };

      // Добавляем обработчики событий для всех навыков
      const skillNodes = document.querySelectorAll('.skill-node');
      skillNodes.forEach(node => {
        const key = node.dataset.key;
        const skill = skills[key];
        
        if (skill) {
          const handler = () => {
            // Показываем информацию
            infoTitle.textContent = skill.name;
            infoDesc.textContent = skill.desc;
            infoPanel.classList.add('active');
          };
          
          node.addEventListener('click', handler);
        }
      });

      // Закрытие панели при клике вне области
      document.addEventListener('click', (e) => {
        const skillsGrid = document.querySelector('.skills-grid');
        if (!skillsGrid.contains(e.target) && !infoPanel.contains(e.target)) {
          infoPanel.classList.remove('active');
        }
      });
    });
  </script>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>

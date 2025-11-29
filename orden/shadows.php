<?php require_once __DIR__ . '/../includes/seo.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require_once __DIR__ . '/../includes/seo.php'; ?>
  <title>Тени Мастеров — Орден Перкуссии</title>
  <link rel="stylesheet" href="/assets/style.css?v=<?php echo time(); ?>">
  <style>
    .legend-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.7);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }
    .legend-modal-content {
      background: white;
      width: 90%;
      max-width: 700px;
      max-height: 80vh;
      overflow-y: auto;
      border-radius: var(--radius);
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
      padding: 24px;
      position: relative;
    }
    .legend-modal h3 {
      margin-top: 0;
      color: var(--primary);
      font-size: 1.8rem;
    }
    .legend-modal p {
      margin-bottom: 16px;
      line-height: 1.6;
    }
    .close-modal {
      position: absolute;
      top: 16px;
      right: 16px;
      font-size: 1.5rem;
      cursor: pointer;
      color: var(--text-light);
    }
    .close-modal:hover {
      color: var(--danger);
    }
    .legend-row {
      cursor: pointer;
      transition: background 0.2s;
    }
    .legend-row:hover {
      background: var(--primary-light);
    }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container">
    <h1>Тени Мастеров</h1>

    <?php
    $currentPath = $_SERVER['REQUEST_URI'];
    if (strpos($currentPath, '/orden/') === 0): ?>
    <nav class="sub-nav" style="display: flex; flex-wrap: wrap; gap: 10px; margin: 20px 0; justify-content: center;">
      <a href="/orden/manifest/" class="btn">Свод принципов</a>
      <a href="/orden/levels/" class="btn">Уровни</a>
      <a href="/orden/professions/" class="btn">Профессии</a>
      <a href="/orden/achievements/" class="btn">Достижения</a>
      <a href="/orden/ratings/" class="btn">Рейтинг</a>
      <a href="/orden/shadows/" class="btn active">Тени Мастеров</a>
    </nav>
    <?php endif; ?>

    <div class="card">
      <p>Когда вы достигаете рейтинга <strong>2200+</strong>, Орден открывает вам доступ к древнему знанию — вы встречаете свою <strong>Тень</strong>.</p>
      <p>Тень — это легендарный перкуссионист, чей путь резонирует с вашим стилем. Его музыка становится вашим испытанием.</p>

      <blockquote>«Ты не повторяешь его — ты вступаешь в диалог через ритм.»</blockquote>

      <h3>Пантеон Легенд</h3>
      <p>Символические рейтинги великих. Они не достижимы — но к ним можно стремиться.</p>

      <table style="width: 100%; border-collapse: collapse; margin-top: 16px;">
        <thead>
          <tr style="background: var(--primary-light); text-align: left;">
            <th style="padding: 10px; border: 1px solid var(--border);">Имя</th>
            <th style="padding: 10px; border: 1px solid var(--border);">Стиль</th>
            <th style="padding: 10px; border: 1px solid var(--border);">Рейтинг</th>
          </tr>
        </thead>
        <tbody>
          <tr class="legend-row" data-legend="buddy_rich">
            <td>Бадди Рич</td><td>Big band, техника</td><td>2950</td>
          </tr>
          <tr class="legend-row" data-legend="tony_williams">
            <td>Тони Уильямс</td><td>Джаз, полиметрия</td><td>2930</td>
          </tr>
          <tr class="legend-row" data-legend="john_bonham">
            <td>Джон Бонэм</td><td>Рок, грув</td><td>2900</td>
          </tr>
          <tr class="legend-row" data-legend="dave_lombardo">
            <td>Дэйв Ломбардо</td><td>Трэш-метал (Slayer)</td><td>2890</td>
          </tr>
          <tr class="legend-row" data-legend="neil_peart">
            <td>Нил Пирт</td><td>Прог-рок (Rush)</td><td>2880</td>
          </tr>
          <tr class="legend-row" data-legend="elvin_jones">
            <td>Элвин Джонс</td><td>Свободный джаз</td><td>2870</td>
          </tr>
          <tr class="legend-row" data-legend="thomas_haake">
            <td>Томас Хааке</td><td>Полиметрический метал (Meshuggah)</td><td>2860</td>
          </tr>
          <tr class="legend-row" data-legend="steve_gadd">
            <td>Стив Гэдд</td><td>Сессионщик века</td><td>2850</td>
          </tr>
          <tr class="legend-row" data-legend="dennis_chambers">
            <td>Деннис Чамберс</td><td>Фьюжн, латина</td><td>2840</td>
          </tr>
          <tr class="legend-row" data-legend="joey_jordison">
            <td>Джои Джордисон</td><td>Ню-метал (Slipknot)</td><td>2820</td>
          </tr>
          <tr class="legend-row" data-legend="olatunji">
            <td>Бабатунде Олатунджи</td><td>Африканские ритмы</td><td>2800</td>
          </tr>
          <tr class="legend-row" data-legend="bostaph">
            <td>Пол Бостаф</td><td>Трэш-метал (Slayer)</td><td>2780</td>
          </tr>
          <tr class="legend-row" data-legend="keith_moon">
            <td>Кит Мун</td><td>Психоделический рок</td><td>2780</td>
          </tr>
          <tr class="legend-row" data-legend="aldridge">
            <td>Томми Олдридж</td><td>Хэви-метал (Ozzy)</td><td>2760</td>
          </tr>
          <tr class="legend-row" data-legend="carrington">
            <td>Терри Линн Каррингтон</td><td>Современный джаз</td><td>2760</td>
          </tr>
          <tr class="legend-row" data-legend="lars_ulrich">
            <td>Ларс Ульрих</td><td>Трэш-метал (Metallica)</td><td>2740</td>
          </tr>
          <tr class="legend-row" data-legend="bordin">
            <td>Майк Бордин</td><td>Альт-метал (Faith No More)</td><td>2730</td>
          </tr>
          <tr class="legend-row" data-legend="grohl">
            <td>Дэйв Грол</td><td>Гранж → рок</td><td>2750</td>
          </tr>
          <tr class="legend-row" data-legend="chad_smith">
            <td>Чад Смит</td><td>Фанк-рок</td><td>2720</td>
          </tr>
          <tr class="legend-row" data-legend="kamissa">
            <td>Мамаду Камисса</td><td>Африканские традиции</td><td>2700</td>
          </tr>
        </tbody>
      </table>

      <p style="margin-top: 20px;">
        Испытания Теней станут доступны участникам уровня 4+ с рейтингом 2200+. Следите за обновлениями!
      </p>
    </div>

    <div style="text-align: center; margin-top: 20px;">
      <a href="/orden/" class="btn">← Назад к Ордену</a>
    </div>
  </main>

  <!-- Модальное окно -->
  <div id="legendModal" class="legend-modal">
    <div class="legend-modal-content">
      <span class="close-modal" id="closeModal">&times;</span>
      <h3 id="modalTitle">Имя</h3>
      <p id="modalText">Описание...</p>
    </div>
  </div>

  <script>
    const legends = {
      buddy_rich: {
        name: "Бадди Рич (2950)",
        desc: "Король скорости, виртуоз big band эпохи. Бадди — эталон технического совершенства: его дробь на 200+ BPM остаётся недосягаемой даже для современных барабанщиков. Он не просто играл — он доминировал над оркестром. Рейтинг 2950 — это почти мифический предел, символ того, что человек может приблизиться к машине, но сохранить человеческий огонь."
      },
      tony_williams: {
        name: "Тони Уильямс (2930)",
        desc: "Революционер джаза, голос свободного ритма. С 17 лет он изменил джаз, играя с Майлзом Дэвисом. Его стиль — это разговор между руками и ногами, где метр растворяется в эмоции. Рейтинг 2930 отражает не скорость, а глубину музыкального мышления — он слышал то, чего не слышали другие."
      },
      john_bonham: {
        name: "Джон Бонэм (2900)",
        desc: "Грув, который сотрясает землю. Бонэм создал архетип рок-барабанщика: мощь, естественность, мелодичность даже в простых ритмах. Его «When the Levee Breaks» — один из самых семплируемых ритмов в истории. 2900 — это дань уважения гению простоты, который звучит так, будто бьёт прямо в сердце."
      },
      dave_lombardo: {
        name: "Дэйв Ломбардо (2890)",
        desc: "Отец трэш-метала, машина скорости и агрессии. Его бласт-биты в Slayer — это чистая энергия разрушения, сведённая к математической точности. Влияние Ломбардо на метал сопоставимо с Бонэмом в роке: он задал стандарт на десятилетия. 2890 — почти предел, но уступает Бадди в универсальной технике."
      },
      neil_peart: {
        name: "Нил Пирт (2880)",
        desc: "Философ прогрессивного рока. Пирт — не просто барабанщик, а композитор за ударной установкой. Его пьесы — это архитектура из ритмов, где каждый хит — часть замысла. 2880 — за интеллект, музыкальность и способность говорить на языке, понятном только посвящённым."
      },
      elvin_jones: {
        name: "Элвин Джонс (2870)",
        desc: "Полиметрия как поэзия. В трио Колтрейна он создал ритмическую стихию, где традиционный метр исчезал. Его игра — это хаос с внутренним порядком. 2870 — за смелость разрушить правила и создать новые."
      },
      thomas_haake: {
        name: "Томас Хааке (2860)",
        desc: "Математик металла, отец «djent». С Meshuggah он превратил барабаны в инструмент полиметрической абстракции. Его партии звучат как алгоритмы, но играет он с безумной физической силой. 2860 — потому что его сложность технически сопоставима с джазовыми легендами, но в ином измерении."
      },
      steve_gadd: {
        name: "Стив Гэдд (2850)",
        desc: "Сессионщик, который звучит как легенда. Его грув в «Aja» (Steely Dan) — эталон студийной игры. Гэдд может вписаться в любой жанр и сделать его лучше. 2850 — за универсальность, вкус и умение служить музыке, а не собственному эго."
      },
      dennis_chambers: {
        name: "Деннис Чамберс (2840)",
        desc: "Скорость с душой. Фьюжн, латина, фанк — везде он играет на грани возможного, но никогда не теряет грува. Его техника пугает, но звучит органично. 2840 — за способность быть сверхчеловеком и человеком одновременно."
      },
      joey_jordison: {
        name: "Джои Джордисон (2820)",
        desc: "Икона ню-метала, техничный, сценичный, трагическая фигура. С Slipknot он сочетал машинную точность с театральной яростью. Его двойная педаль и сольные партии стали гимном поколения. 2820 — это культовый статус, но уступает Ломбардо по историческому влиянию на жанр."
      },
      olatunji: {
        name: "Бабатунде Олатунджи (2800)",
        desc: "Посол африканского ритма миру. Его альбом «Drums of Passion» открыл Западу дух африканских ритмов. Он не учил технике — он учил слушать землю. 2800 — за миссию, а не за виртуозность."
      },
      bostaph: {
        name: "Пол Бостаф (2780)",
        desc: "Хранитель трэш-огня. После Ломбардо он доказал, что Slayer могут звучать так же сокрушительно. Его скорость — как лезвие бритвы. 2780 — за уважение к наследию и собственное мастерство."
      },
      keith_moon: {
        name: "Кит Мун (2780)",
        desc: "Хаос как искусство. С The Who он превратил барабаны в театр разрушения. Его игра — это безумие, но с внутренним ритмом. 2780 — за смелость быть непредсказуемым в эпоху стандартизации."
      },
      aldridge: {
        name: "Томми Олдридж (2760)",
        desc: "Первый, кто поставил двойную бас-бочку в хэви-метал. С Ozzy Osbourne он создал архетип мощи — тяжёлый, но гибкий грув. 2760 — за новаторство, которое стало нормой."
      },
      carrington: {
        name: "Терри Линн Каррингтон (2760)",
        desc: "Голос нового джаза. Она не только играет — она меняет лицо джаза, включая женские и афроамериканские голоса. 2760 — за лидерство, педагогику и искусство."
      },
      lars_ulrich: {
        name: "Ларс Ульрих (2740)",
        desc: "Не самый техничный, но главный популяризатор метал-ритма. С Metallica он вывел трэш в массы. Его грувы просты, но цепляют на уровне инстинкта. 2740 — честный рейтинг: уважение за влияние, а не виртуозность."
      },
      bordin: {
        name: "Майк Бордин (2730)",
        desc: "Грув в мире безумия. С Faith No More он создал гибрид фанка, метала и панка, где каждый ритм — загадка. 2730 — за уникальность и способность быть «чужим» везде — и везде своим."
      },
      grohl: {
        name: "Дэйв Грол (2750)",
        desc: "От гранжа к рок-олимпу. С Nirvana он дал голос поколению, с Foo Fighters — стал символом энергии и искренности. Он не гений техники, но его грув заставляет двигаться миллионы. 2750 — за эмоциональную силу."
      },
      chad_smith: {
        name: "Чад Смит (2720)",
        desc: "Фанк-рок как праздник. С Red Hot Chili Peppers он доказал, что металлическая мощь и чувственность могут жить вместе. Его грув — это танец. 2720 — за заразительность и драйв."
      },
      kamissa: {
        name: "Мамаду Камисса (2700)",
        desc: "Хранитель устной традиции Мали. Его дундун — не инструмент, а голос предков. Он не записывает альбомы — он оживляет ритуалы. 2700 — за связь с корнями, которую невозможно измерить BPM."
      }
    };

    document.querySelectorAll('.legend-row').forEach(row => {
      row.addEventListener('click', () => {
        const key = row.dataset.legend;
        const legend = legends[key];
        if (legend) {
          document.getElementById('modalTitle').textContent = legend.name;
          document.getElementById('modalText').textContent = legend.desc;
          document.getElementById('legendModal').style.display = 'flex';
        }
      });
    });

    document.getElementById('closeModal').addEventListener('click', () => {
      document.getElementById('legendModal').style.display = 'none';
    });

    window.addEventListener('click', (e) => {
      if (e.target === document.getElementById('legendModal')) {
        document.getElementById('legendModal').style.display = 'none';
      }
    });
  </script>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>